<?php

namespace App\Livewire\Public;

use App\Models\Course;
use App\Models\Enrollment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('Course Player')]
class CoursePlayer extends Component
{
    public $courseId;
    public $course;
    public $selectedTopic = null;
    public $expandedChapter = null;
    public $isEnrolled = false;

    public function mount($courseId)
    {
        $this->courseId = $courseId;
        $this->course = Course::with(['chapters.topics'])
            ->where('is_published', true)
            ->findOrFail($courseId);

        // Check if user is enrolled
        if (auth()->check()) {
            $this->isEnrolled = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $this->courseId)
                ->where('status', 'completed')
                ->exists();
        }

        // If not enrolled, redirect to course detail
        if (!$this->isEnrolled) {
            return redirect()->route('courses.show', $this->course->slug);
        }

        // Select first topic by default
        if ($this->course->chapters->count() > 0 && $this->course->chapters[0]->topics->count() > 0) {
            $this->selectedTopic = $this->course->chapters[0]->topics[0];
            $this->expandedChapter = $this->course->chapters[0]->id;
        }
    }

    public function selectTopic($topicId)
    {
        foreach ($this->course->chapters as $chapter) {
            foreach ($chapter->topics as $topic) {
                if ($topic->id == $topicId) {
                    $this->selectedTopic = $topic;
                    $this->expandedChapter = $chapter->id;
                    return;
                }
            }
        }
    }

    public function toggleChapter($chapterId)
    {
        if ($this->expandedChapter === $chapterId) {
            $this->expandedChapter = null;
        } else {
            $this->expandedChapter = $chapterId;
        }
    }

    public function render()
    {
        return view('livewire.public.course-player', [
            'videoUrl' => $this->selectedTopic ? $this->selectedTopic->video_url : null,
        ]);
    }
}
