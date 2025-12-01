<?php

namespace App\Livewire\User;

use App\Models\Course;
use App\Models\Enrollment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Course Player')]
class CoursePlayer extends Component
{
    public $course;
    public $selectedTopicId;
    public $expandedChapterId;
    public $videoUrl;

    public function mount($course)
    {
        // Load course
        if ($course instanceof Course) {
            $this->course = $course->load(['chapters.topics']);
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
        if ($this->course->chapters->first()) {
            $firstChapter = $this->course->chapters->first();
            $this->expandedChapterId = $firstChapter->id;
            
            if ($firstChapter->topics->first()) {
                $firstTopic = $firstChapter->topics->first();
                $this->selectedTopicId = $firstTopic->id;
                $this->videoUrl = $firstTopic->video_url;
            }
        }
    }

    public function toggleChapter($chapterId)
    {
        $this->expandedChapterId = $this->expandedChapterId === $chapterId ? null : $chapterId;
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopicId = $topicId;
        // Find the topic and set video URL
        foreach ($this->course->chapters as $chapter) {
            foreach ($chapter->topics as $topic) {
                if ($topic->id == $topicId) {
                    $this->videoUrl = $topic->video_url;
                    $this->expandedChapterId = $chapter->id; // Keep chapter open
                    $this->dispatch('playVideo'); // Dispatch event for video.js
                    break 2;
                }
            }
        }
    }

    public function render()
    {
        // Find selected topic for display
        $selectedTopic = null;
        if ($this->selectedTopicId) {
            foreach ($this->course->chapters as $chapter) {
                foreach ($chapter->topics as $topic) {
                    if ($topic->id == $this->selectedTopicId) {
                        $selectedTopic = $topic;
                        break 2;
                    }
                }
            }
        }

        return view('livewire.user.course-player', [
            'selectedTopic' => $selectedTopic,
        ]);
    }
}
