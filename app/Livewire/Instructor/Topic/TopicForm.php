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

class TopicForm extends Component
{
    use WithFileUploads;

    public $chapterId;
    public $editingId = null;
    public $video;
    public $currentVideo;

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
        ];
    }

    protected $messages = [
        'topic_title.required' => 'Topic title is required.',
        'topic_title.min' => 'Topic title must be at least 3 characters.',
        'order_index.required' => 'Order index is required.',
        'order_index.min' => 'Order index must be at least 1.',
        'video.mimetypes' => 'Only MP4 and MOV video formats are allowed.',
        'video.max' => 'Video size must not exceed 100MB.',
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
                $topicData['video_url'] = $this->uploadVideo();
                Log::info('Video uploaded successfully');
            }

            if ($this->editingId) {
                // Update existing topic
                $topic = Topics::where('chapters_id', $this->chapterId)
                    ->where('id', $this->editingId)
                    ->first();
                
                if (!$topic) {
                    throw new \Exception('Topic not found for editing.');
                }

                // Delete old video if new one is uploaded
                if ($this->video && $topic->video_url) {
                    $this->deleteOldVideo($topic->video_url);
                }
                
                $topic->update($topicData);
                $message = 'Topic updated successfully.';
                Log::info('Topic updated:', ['id' => $topic->id]);
            } else {
                // Create new topic
                $topic = Topics::create($topicData);
                $message = 'Topic created successfully.';
                Log::info('Topic created:', ['id' => $topic->id]);
            }

            // Reset form
            $this->resetForm();
            
            // Dispatch success event
            $this->dispatch('topic-saved', message: $message);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Topic save error: ' . $e->getMessage());
            $this->dispatch('error', message: 'Failed to save topic: ' . $e->getMessage());
            return false;
        }
    }

    protected function uploadVideo()
    {
        try {
            $chapter = Chapters::findOrFail($this->chapterId);
            $courseFolder = Str::slug($chapter->course->title ?? 'course');
            $chapterFolder = Str::slug($chapter->chapter_title);
            
            $fileName = time() . '_' . Str::random(10) . '.' . $this->video->getClientOriginalExtension();

            Log::info('Uploading video:', [
                'fileName' => $fileName,
                'courseFolder' => $courseFolder,
                'chapterFolder' => $chapterFolder,
            ]);

            // Upload to DigitalOcean Spaces
            $path = $this->video->storeAs(
                "videos/{$courseFolder}/{$chapterFolder}",
                $fileName,
                'do_spaces'
            );

            if (!Storage::disk('do_spaces')->exists($path)) {
                throw new \Exception('Failed to upload video to storage');
            }

            $url = Storage::disk('do_spaces')->url($path);
            Log::info('Video uploaded successfully:', ['url' => $url]);
            
            return $url;
        } catch (\Exception $e) {
            Log::error('Video upload error: ' . $e->getMessage());
            throw new \Exception('Video upload failed: ' . $e->getMessage());
        }
    }

    protected function deleteOldVideo($videoUrl)
    {
        try {
            $path = parse_url($videoUrl, PHP_URL_PATH);
            if ($path) {
                Storage::disk('do_spaces')->delete(ltrim($path, '/'));
                Log::info('Old video deleted:', ['path' => $path]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete old video: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->topic_title = '';
        $this->content = '';
        $this->order_index = 1;
        $this->video = null;
        $this->currentVideo = null;
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