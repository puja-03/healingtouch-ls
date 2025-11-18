<?php

namespace App\Livewire\User;

use App\Models\Enrollment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('Purchased Courses')]
class PurchasedCourses extends Component
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
        $this->selectedCourse = Enrollment::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with('course')
            ->findOrFail($courseId);
    }

    public function closeCourseDetail()
    {
        $this->selectedCourse = null;
    }

    public function render()
    {
        $query = Enrollment::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with('course');

        if (!empty($this->search)) {
            $query->whereHas('course', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });
        }

        $enrollments = $query->orderBy('enrolled_at', 'desc')->paginate(9);

        return view('livewire.user.purchased-courses', [
            'enrollments' => $enrollments,
        ]);
    }
}
