<?php

namespace App\Livewire\Admin\Topics;

use App\Models\Topics;
use App\Models\Chapters;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Topic Form')]
#[Layout('components.layouts.admin')]
class TopicForm extends Component
{
    use WithFileUploads;

    public $chaptersId;
    public $topicId;
    public $topic_title;
    public $content;
    public $video;
    public $order_index = 0;
    public $currentVideo;
    public $selectedChapter;

    protected function rules()
    {
        // $videoRule = $this->topicId ? 'nullable' : 'required';
        return [
            'topic_title' => 'required|min:3',
            'content' => 'nullable',
            // 'video' => $videoRule . '|file|mimetypes:video/mp4,video/quicktime|max:102400',
            'order_index' => 'required|numeric|min:0',
        ];
    }

    public function mount($chaptersId = null, $topicId = null)
    {
        $this->chaptersId = $chaptersId;
        $this->selectedChapter = $chaptersId;
        if ($topicId) {
            $this->loadTopic($topicId);
        }
    }

    public function loadTopic($id)
    {
        $topic = Topics::findOrFail($id);
        $this->topicId = $topic->id;
        $this->topic_title = $topic->topic_title;
        $this->content = $topic->content;
        $this->order_index = $topic->order_index;
        $this->currentVideo = $topic->video_url;
        $this->selectedChapter = $topic->chapters_id;
        $this->chaptersId = $topic->chapters_id;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'topic_title' => $this->topic_title,
                'topic_slug' => Str::slug($this->topic_title),
                'content' => $this->content,
                'order_index' => $this->order_index,
                'chapters_id' => $this->selectedChapter,
            ];

            // Handle video upload
            if ($this->video) {
                // Determine which chapter to use for folder naming: prefer selectedChapter (from select), fallback to mount chaptersId
                $chaptersIdForFolder = $this->selectedChapter ?? $this->chaptersId;
                $chapter = Chapters::find($chaptersIdForFolder);
                $chapterFolder = $chapter ? Str::slug($chapter->chapter_title) : 'uncategorized';
                $fileName = time() . '_' . Str::random(10) . '.' . $this->video->getClientOriginalExtension();

                // Delete old video if exists
                if ($this->currentVideo) {
                    $oldPath = parse_url($this->currentVideo, PHP_URL_PATH);
                    if ($oldPath) {
                        Storage::disk('do_spaces')->delete(ltrim($oldPath, '/'));
                    }
                }

                // Upload new video
                $path = Storage::disk('do_spaces')->putFileAs(
                    "videos/{$chapterFolder}",
                    $this->video,
                    $fileName,
                    'public'
                );

                $data['video_url'] = Storage::disk('do_spaces')->url($path);
            }

            if ($this->topicId) {
                Topics::find($this->topicId)->update($data);
                $message = 'Topic updated successfully!';
            } else {
                Topics::create($data);
                $message = 'Topic created successfully!';
            }

            session()->flash('success', $message);
            return redirect()->route('admin.topics', ['chapters_id' => $this->chaptersId]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.topics.topic-form', [
            'chapter' => Chapters::find($this->chaptersId),
            'chapters' => Chapters::all() 
        ]);
    }
}