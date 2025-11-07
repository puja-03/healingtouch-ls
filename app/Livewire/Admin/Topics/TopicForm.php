<?php

namespace App\Livewire\Admin\Topics;

use App\Models\Topics;
use App\Models\Chapters;
use App\Models\Course;
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
    public $selectedChapter = null;
    public $selectedCourse = null;
    public $availableChapters = [];
    public $step = 1;

    protected function rules()
    {
        return [
            'topic_title' => 'required|min:3',
            'content' => 'nullable',
            'order_index' => 'required|numeric|min:0',
        ];
    }

    public function mount($chapters_id = null, $topic_id = null)
    {
        $this->chaptersId = $chapters_id;
        $this->selectedChapter = $chapters_id;
        if ($topic_id) {
            $this->loadTopic($topic_id);
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
        // set selectedCourse if chapter has one
        $chapter = Chapters::find($topic->chapters_id);
        if ($chapter) {
            $this->selectedCourse = $chapter->course_id ?? null;
            // preload chapters for that course
            $this->availableChapters = Chapters::where('course_id', $this->selectedCourse)
                ->orderBy('chapter_title')
                ->get();
        }
    }

    public function nextStep()
    {
        if ($this->step < 3) $this->step++;
    }

    public function prevStep()
    {
        if ($this->step > 1) $this->step--;
    }

    public function updatedSelectedCourse($courseId)
    {
        if (!empty($courseId)) {
            $this->availableChapters = Chapters::where('course_id', $courseId)
                ->orderBy('chapter_title')
                ->get();
            $this->selectedChapter = null;
        } else {
            $this->availableChapters = [];
            $this->selectedChapter = null;
        }
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

            if ($this->video) {
                $chaptersIdForFolder = $this->selectedChapter ?? $this->chaptersId;
                $chapter = Chapters::find($chaptersIdForFolder);
                $chapterFolder = $chapter ? Str::slug($chapter->chapter_title) : 'uncategorized';
                $fileName = time() . '_' . Str::random(10) . '.' . $this->video->getClientOriginalExtension();

                if ($this->currentVideo) {
                    $oldPath = parse_url($this->currentVideo, PHP_URL_PATH);
                    if ($oldPath) {
                        Storage::disk('do_spaces')->delete(ltrim($oldPath, '/'));
                    }
                }

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
        $courses = Course::orderBy('title')->get();
        $chapters = $this->availableChapters; // only chapters for selected course

        return view('livewire.admin.topics.topic-form', [
            'courses' => $courses,
            'chapters' => $chapters,
        ]);
    }
}
