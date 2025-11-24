<?php

namespace App\Livewire\Public;

use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Topic;
use App\Models\Chapters;

#[Layout('components.layouts.app')]
#[Title('Course Details')]
class CourseDetail extends Component
{
    public Course $course;

    public function mount(Course $course)
    {
        $this->course = $course;
    }

    public function render()
    {
        return view('livewire.public.course-detail', [
            'course' => $this->course,
        ]);
    }
}
