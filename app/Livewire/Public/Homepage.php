<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Course;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Topic;
use App\Models\Chapters;
use App\Models\Enrollment;

#[Title('homepage')]
#[Layout('components.layouts.app')]
class Homepage extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCourse = null;

    protected $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectCourse($courseId)
    {
        $this->selectedCourse = Course::where('is_published', true)
            ->findOrFail($courseId);
    }

    public function closeCourseDetail()
    {
        $this->selectedCourse = null;
    }

    public function render()
    {
        $query = Course::where('is_published', true);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $courses = $query->with('user')->orderBy('created_at', 'desc')->paginate(9);

        $enrolledCourseIds = [];
        if (auth()->check()) {
            // Consider any enrollment record as enrolled (ignore status values)
            $enrolledCourseIds = Enrollment::where('user_id', auth()->id())
                ->pluck('course_id')
                ->toArray();
        }

        return view('livewire.public.homepage', [
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }
}
