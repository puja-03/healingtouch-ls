<?php

namespace App\Livewire\Instructor\Topic;

use App\Models\Topics;
use App\Models\Chapters;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Illuminate\Http\File;   

#[Title('Topic Form')]
class TopicForm extends Component
{
    use WithFileUploads;

    public $chapterId;
    public $editingId = null;
    public $video;
    public $currentVideo;
    public $attachments = []; 
    public $currentAttachments = []; 

    public $topic_title = '';
    public $content = '';
    public $order_index = 1;

    protected function rules()
    {
        return [
            'topic_title' => 'required|string|min:3|max:255',
            'content' => 'nullable|string',
            'order_index' => 'required|integer|min:1',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:102400', // 100MB
            'attachments.*' => 'nullable|file|max:51200', // 50MB per file
        ];
    }

    protected $messages = [
        'topic_title.required' => 'Topic title is required.',
        'topic_title.min' => 'Topic title must be at least 3 characters.',
        'order_index.required' => 'Order index is required.',
        'order_index.min' => 'Order index must be at least 1.',
        'video.mimetypes' => 'Only MP4 and MOV video formats are allowed.',
        'video.max' => 'Video size must not exceed 100MB.',
        'attachments.*.max' => 'Each attachment must not exceed 50MB.',
    ];

    public function mount($chapterId, $editingId = null)
    {
        $this->chapterId = $chapterId;
        $this->editingId = $editingId;
        
        if ($editingId) {
            $this->loadTopic($editingId);
        } else {
            $this->setNextOrderIndex();
        }
    }

    public function loadTopic($id)
    {
        try {
            $topic = Topics::where('chapters_id', $this->chapterId)
                ->findOrFail($id);
            
            $this->topic_title = $topic->topic_title;
            $this->content = $topic->content;
            $this->order_index = $topic->order_index;
            $this->currentVideo = $topic->video_url;
            
            // Load existing attachments
            if ($topic->attachments) {
                $this->currentAttachments = json_decode($topic->attachments, true);
            }
            
        } catch (\Exception $e) {
            Log::error('Error loading topic: ' . $e->getMessage());
            $this->dispatch('error', message: 'Topic not found.');
            $this->cancel();
        }
    }

    public function setNextOrderIndex()
    {
        $lastTopic = Topics::where('chapters_id', $this->chapterId)
            ->orderBy('order_index', 'desc')
            ->first();
        
        $this->order_index = $lastTopic ? $lastTopic->order_index + 1 : 1;
    }

    public function save()
    {
        $this->validate();

        try {
            $topicData = [
                'topic_title' => $this->topic_title,
                'content' => $this->content,
                'order_index' => $this->order_index,
                'chapters_id' => $this->chapterId,
            ];

            Log::info('Attempting to save topic:', $topicData);

            // Handle video upload
            if ($this->video) {
                $topicData['video_url'] = $this->uploadVideo($this->video, 'videos');
                Log::info('Video uploaded successfully');
            }

            // Handle attachments upload
            $uploadedAttachments = [];
            if ($this->attachments) {
                foreach ($this->attachments as $attachment) {
                    $attachmentUrl = $this->uploadAttachment($attachment, 'attachments');
                    $uploadedAttachments[] = [
                        'name' => $attachment->getClientOriginalName(),
                        'url' => $attachmentUrl,
                        'size' => $attachment->getSize(),
                        'type' => $attachment->getMimeType(),
                    ];
                }
            }
            // Merge attachments if editing
            if ($this->editingId) {
                $topic = Topics::where('chapters_id', $this->chapterId)
                    ->where('id', $this->editingId)
                    ->first();
                
                if (!$topic) {
                    throw new \Exception('Topic not found for editing.');
                }

                // Delete old video if new one is uploaded
                if ($this->video && $topic->video_url) {
                    $this->deleteOldFile($topic->video_url);
                }

                // Merge existing attachments with new ones
                $existingAttachments = $topic->attachments ? json_decode($topic->attachments, true) : [];
                $allAttachments = array_merge($existingAttachments, $uploadedAttachments);
                $topicData['attachments'] = !empty($allAttachments) ? json_encode($allAttachments) : null;
                
                $topic->update($topicData);
                $message = 'Topic updated successfully.';
                Log::info('Topic updated:', ['id' => $topic->id]);
            } else {
                // Create new topic
                if (!empty($uploadedAttachments)) {
                    $topicData['attachments'] = json_encode($uploadedAttachments);
                }
                
                $topic = Topics::create($topicData);
                $message = 'Topic created successfully.';
                Log::info('Topic created:', ['id' => $topic->id]);
            }
            $this->resetForm();
            $this->dispatch('topic-saved', message: $message);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Topic save error: ' . $e->getMessage());
            $this->dispatch('error', message: 'Failed to save topic: ' . $e->getMessage());
            return false;
        }
    }

    // protected function uploadVideo($file, $type = 'videos')
    // {
    //     return $this->uploadToDigitalOcean($file, $type);
    // }
    protected function uploadVideo($file)
{
    try {

        // 1. Temporary original file
        $tempPath = $file->getRealPath();

        // 2. Temporary converted file path (local)
        $convertedFileName = time() . '_converted.mp4';
        $convertedPath = storage_path('app/' . $convertedFileName);

        // 3. Convert to H.265 (HEVC)
        $command = "ffmpeg -i \"$tempPath\" -vcodec libx265 -crf 28 -preset medium -acodec aac \"$convertedPath\" -y";
        exec($command);

        if (!file_exists($convertedPath)) {
            throw new \Exception('Video conversion failed — ffmpeg output file missing.');
        }

        // 4. Prepare folder path
        $chapter = Chapters::findOrFail($this->chapterId);
        $courseFolder = Str::slug($chapter->course->title ?? 'course');
        $chapterFolder = Str::slug($chapter->chapter_title);
        $topicFolder = Str::slug($this->topic_title);

        // 5. Upload converted file to DigitalOcean Spaces
        $finalName = time() . '_hevc.mp4';

        $path = Storage::disk('do_spaces')->putFileAs(
            "videos/$courseFolder/$chapterFolder/$topicFolder",
            new File($convertedPath),
            $finalName,
            'public'
        );

        // 6. Remove local converted file
        unlink($convertedPath);

        return Storage::disk('do_spaces')->url($path);

    } catch (\Exception $e) {
        Log::error("Video conversion/upload error: " . $e->getMessage());
        throw new \Exception("Video upload failed: " . $e->getMessage());
    }
}


    protected function uploadAttachment($file, $type = 'attachments')
    {
        return $this->uploadToDigitalOcean($file, $type);
    }

    protected function uploadToDigitalOcean($file, $type = 'files')
    {
        try {
            $chapter = Chapters::findOrFail($this->chapterId);
            $courseFolder = Str::slug($chapter->course->title ?? 'course');
            $chapterFolder = Str::slug($chapter->chapter_title);
            $topicFolder = Str::slug($this->topic_title);
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(10) . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

            Log::info('Uploading to DigitalOcean:', [
                'fileName' => $fileName,
                'courseFolder' => $courseFolder,
                'chapterFolder' => $chapterFolder,
                'topicFolder' => $topicFolder,
                'type' => $type,
            ]);

            // Upload to DigitalOcean Spaces
            $path = $file->storeAs(
                "{$type}/{$courseFolder}/{$chapterFolder}/{$topicFolder}",
                $fileName,
                'do_spaces'
            );

            if (!Storage::disk('do_spaces')->exists($path)) {
                throw new \Exception('Failed to upload file to storage');
            }

            $url = Storage::disk('do_spaces')->url($path);
            Log::info('File uploaded successfully:', ['url' => $url]);
            
            return $url;
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    // Method to remove an existing attachment
    public function removeAttachment($index)
    {
        if (isset($this->currentAttachments[$index])) {
            $attachment = $this->currentAttachments[$index];
            
            // Delete from storage
            $this->deleteOldFile($attachment['url']);
            
            // Remove from array
            unset($this->currentAttachments[$index]);
            $this->currentAttachments = array_values($this->currentAttachments); // Reindex array
            
            // Update the topic with remaining attachments
            if ($this->editingId) {
                $topic = Topics::find($this->editingId);
                if ($topic) {
                    $topic->attachments = !empty($this->currentAttachments) ? json_encode($this->currentAttachments) : null;
                    $topic->save();
                }
            }
        }
    }

    // Method to remove a newly uploaded attachment
    public function removeNewAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = aClickrray_values($this->attachments); // Reindex array
        }
    }

    protected function deleteOldFile($fileUrl)
    {
        try {
            $path = parse_url($fileUrl, PHP_URL_PATH);
            if ($path) {
                Storage::disk('do_spaces')->delete(ltrim($path, '/'));
                Log::info('Old file deleted:', ['path' => $path]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete old file: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->topic_title = '';
        $this->content = '';
        $this->order_index = 1;
        $this->video = null;
        $this->currentVideo = null;
        $this->attachments = [];
        $this->currentAttachments = [];
        $this->editingId = null;
    }

    public function cancel()
    {
        $this->resetForm();
        $this->dispatch('topic-cancelled');
    }

    public function render()
    {
        return view('livewire.instructor.topic.topic-form');
    }
}