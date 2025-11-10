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
    public $chapters = [];
    public $step = 1;

    protected $rules = [
        'selectedCourse' => 'required_if:step,1',
        'selectedChapter' => 'required_if:step,1',
        'topic_title' => 'required|min:3',
        'content' => 'nullable',
        'order_index' => 'required|numeric|min:0',
        'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:102400', // max 100MB
    ];

    protected $messages = [
        'selectedCourse.required_if' => 'Please select a course',
        'selectedChapter.required_if' => 'Please select a chapter',
    ];

    public function mount($chapters_id = null, $topic_id = null)
    {
        $this->chaptersId = $chapters_id;

        if ($topic_id) {
            $this->loadTopic($topic_id);
        } elseif ($chapters_id) {
            $this->preloadFromChapter($chapters_id);
        }
    }

    protected function preloadFromChapter($chapter_id)
    {
        $chapter = Chapters::with('course')->find($chapter_id);
        if ($chapter) {
            $this->selectedChapter = $chapter->id;
            $this->selectedCourse = $chapter->course_id;
            $this->loadChapters(); // Load chapters for the course
        }
    }

    public function loadTopic($id)
    {
        $topic = Topics::with('chapter.course')->findOrFail($id);
        $this->topicId = $topic->id;
        $this->topic_title = $topic->topic_title;
        $this->content = $topic->content;
        $this->order_index = $topic->order_index;
        $this->currentVideo = $topic->video_url;
        $this->selectedChapter = $topic->chapters_id;
        
        if ($topic->chapter) {
            $this->selectedCourse = $topic->chapter->course_id;
            $this->loadChapters();
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validateOnly('selectedCourse');
            $this->validateOnly('selectedChapter');
        }
        
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function updatedSelectedCourse($courseId)
    {
        $this->selectedChapter = null;
        $this->loadChapters();
    }

    protected function loadChapters()
    {
        if ($this->selectedCourse) {
            $this->chapters = Chapters::where('course_id', $this->selectedCourse)
                ->orderBy('order_index')
                ->orderBy('chapter_title')
                ->get()
                ->toArray();
        } else {
            $this->chapters = [];
        }
        
        // Debug output
        logger('Chapters loaded:', [
            'course' => $this->selectedCourse,
            'chapters_count' => count($this->chapters),
            'chapters' => $this->chapters
        ]);
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
                $data['video_url'] = $this->uploadVideo();
            }

            if ($this->topicId) {
                Topics::find($this->topicId)->update($data);
                $message = 'Topic updated successfully!';
            } else {
                Topics::create($data);
                $message = 'Topic created successfully!';
            }

            session()->flash('success', $message);
            return redirect()->route('admin.topics', ['chapters_id' => $this->selectedChapter]);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
            logger('Topic save error: ' . $e->getMessage());
        }
    }

    protected function uploadVideo()
    {
        try {
            $chapter = Chapters::find($this->selectedChapter);
            $chapterFolder = $chapter ? Str::slug($chapter->chapter_title) : 'uncategorized';
            
            // Debug video information
            logger('Video Upload Debug:', [
                'originalName' => $this->video->getClientOriginalName(),
                'mimeType' => $this->video->getMimeType(),
                'size' => $this->video->getSize(),
                'tempPath' => $this->video->getRealPath(),
                'chapterFolder' => $chapterFolder
            ]);

            $fileName = time() . '_' . Str::random(10) . '.' . $this->video->getClientOriginalExtension();

            if ($this->currentVideo) {
                $this->deleteOldVideo();
            }

            // Ensure the video file exists and is valid
            if (!$this->video->isValid()) {
                throw new \Exception('Invalid video file');
            }

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
            logger('Video Upload Success:', ['url' => $url]);
            return $url;
        } catch (\Exception $e) {
            logger('Video Upload Error:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function deleteOldVideo()
    {
        $oldPath = parse_url($this->currentVideo, PHP_URL_PATH);
        if ($oldPath) {
            Storage::disk('do_spaces')->delete(ltrim($oldPath, '/'));
        }
    }

    public function render()
    {
        $courses = Course::orderBy('title')->get();
        
        return view('livewire.admin.topics.topic-form', [
            'courses' => $courses,
        ]);
    }
}