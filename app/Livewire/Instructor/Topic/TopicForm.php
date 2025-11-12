<?php

namespace App\Livewire\Instructor\Topic;

use App\Models\Topics;
use App\Models\Chapters;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TopicForm extends Component
{
    use WithFileUploads;

    public $chapterId;
    public $editingId = null;
    public $video;
    public $currentVideo;

    public $form = [
        'topic_title' => '',
        'content' => '',
        'order_index' => 0,
    ];

    protected function rules()
    {
        return [
            'form.topic_title' => ['required', 'string', 'max:255'],
            'form.content' => ['nullable', 'string'],
            'form.order_index' => ['required', 'numeric', 'min:0'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime', 'max:102400'],
        ];
    }

    public function mount($chapterId, $editingId = null)
    {
        $this->chapterId = $chapterId;
        
        if ($editingId) {
            $this->load($editingId);
        }
    }

    public function load($id)
    {
        $topic = Topics::where('chapters_id', $this->chapterId)
            ->findOrFail($id);
        
        $this->editingId = $topic->id;
        $this->form = [
            'topic_title' => $topic->topic_title,
            'content' => $topic->content,
            'order_index' => $topic->order_index,
        ];
        $this->currentVideo = $topic->video_url;
    }

    public function resetForm()
    {
        $this->form = [
            'topic_title' => '',
            'content' => '',
            'order_index' => 0,
        ];
        $this->video = null;
        $this->currentVideo = null;
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $data = [
                'topic_title' => $this->form['topic_title'],
                'content' => $this->form['content'],
                'order_index' => $this->form['order_index'],
            ];

            // Handle video upload
            if ($this->video) {
                $data['video_url'] = $this->uploadVideo();
            }

            if ($this->editingId) {
                $topic = Topics::where('chapters_id', $this->chapterId)
                    ->findOrFail($this->editingId);
                
                // Delete old video if new one is uploaded
                if ($this->video && $topic->video_url) {
                    $this->deleteOldVideo($topic->video_url);
                }

                $topic->update($data);
            } else {
                Topics::create([
                    'chapters_id' => $this->chapterId,
                    ...$data,
                ]);
            }

            $this->emitUp('topicSaved');
            $this->resetForm();
            $this->editingId = null;
        } catch (\Exception $e) {
            Log::error('Instructor topic save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the topic: ' . $e->getMessage());
        }
    }

    protected function uploadVideo()
    {
        try {
            $chapter = Chapters::findOrFail($this->chapterId);
            $chapterFolder = Str::slug($chapter->chapter_title);
            $fileName = time() . '_' . Str::random(10) . '.' . $this->video->getClientOriginalExtension();

            logger('Uploading video:', [
                'fileName' => $fileName,
                'mimeType' => $this->video->getMimeType(),
                'size' => $this->video->getSize(),
            ]);

            $path = Storage::disk('do_spaces')->putFileAs(
                "videos/{$chapterFolder}",
                $this->video,
                $fileName,
                'public'
            );

            if (!Storage::disk('do_spaces')->exists($path)) {
                throw new \Exception('Failed to upload video to DigitalOcean Spaces');
            }

            $url = Storage::disk('do_spaces')->url($path);
            logger('Video uploaded successfully:', ['url' => $url]);
            
            return $url;
        } catch (\Exception $e) {
            logger('Video upload error: ' . $e->getMessage());
            throw new \Exception('Video upload failed: ' . $e->getMessage());
        }
    }

    protected function deleteOldVideo($videoUrl)
    {
        try {
            $path = parse_url($videoUrl, PHP_URL_PATH);
            if ($path) {
                Storage::disk('do_spaces')->delete(ltrim($path, '/'));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete old video: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->emitUp('topicCancelled');
    }

    public function render()
    {
        return view('livewire.instructor.topic.topic-form');
    }
}
