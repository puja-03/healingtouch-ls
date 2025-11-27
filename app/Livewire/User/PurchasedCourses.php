<?php

namespace App\Livewire\User;

use App\Models\Payment;
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
        $this->selectedCourse = Payment::where('user_id', auth()->id())
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
        $query = Payment::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with('course');

        if (!empty($this->search)) {
            $query->whereHas('course', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('livewire.user.purchased-courses', [
            'payments' => $payments,
        ]);
    }
}
