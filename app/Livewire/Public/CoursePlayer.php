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
    public $course;
    public $selectedTopicId = null;
    public $expandedChapterId = null;
    public $videoUrl = null;
    public $selectedTopic = null; // Add this line

    public function mount($course)
    {
        // Load course
        if ($course instanceof Course) {
            $this->course = $course;
        } elseif (is_numeric($course)) {
            $this->course = Course::with(['chapters.topics'])
                ->where('is_published', true)
                ->findOrFail($course);
        } else {
            $this->course = Course::with(['chapters.topics'])
                ->where('is_published', true)
                ->where('slug', $course)
                ->firstOrFail();
        }

        // Check enrollment
        if (auth()->check()) {
            $isEnrolled = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $this->course->id)
                ->exists();
                
            if (!$isEnrolled) {
                return redirect()->route('courses.show', $this->course->slug);
            }
        }

        // Set initial state
        $firstChapter = $this->course->chapters->first();
        if ($firstChapter) {
            $this->expandedChapterId = $firstChapter->id;
            $firstTopic = $firstChapter->topics->first();
            if ($firstTopic) {
                $this->selectedTopicId = $firstTopic->id;
                $this->selectedTopic = $firstTopic; // Set selected topic
                $this->videoUrl = $firstTopic->video_url;
            }
        }
    }

    public function toggleChapter($chapterId)
    {
        // Toggle chapter - if same chapter, close it; else open new one
        $this->expandedChapterId = $this->expandedChapterId === $chapterId ? null : $chapterId;
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopicId = $topicId;
        
        // Find the topic and set video URL
        foreach ($this->course->chapters as $chapter) {
            foreach ($chapter->topics as $topic) {
                if ($topic->id == $topicId) {
                    $this->selectedTopic = $topic; // Set the selected topic object
                    $this->videoUrl = $topic->video_url;
                    $this->expandedChapterId = $chapter->id; // Keep chapter open
                    break 2;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.public.course-player');
    }
}