<?php

namespace App\Livewire\User;

use App\Models\Enrollment;
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

    public function selectCourse($enrollmentId)
    {
        $this->selectedCourse = Enrollment::where('user_id', auth()->id())
            ->with('course')
            ->findOrFail($enrollmentId);
    }

    public function closeCourseDetail()
    {
        $this->selectedCourse = null;
    }

    public function render()
    {
        $query = Enrollment::where('user_id', auth()->id())
            ->with('course');

        if (!empty($this->search)) {
            $query->whereHas('course', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(9);

        // If there are no enrollments but there are completed payments, create enrollments
        if ($enrollments->count() === 0) {
            $payments = Payment::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->get();

            foreach ($payments as $payment) {
                Enrollment::firstOrCreate([
                    'user_id' => auth()->id(),
                    'course_id' => $payment->course_id,
                ], [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount ?? 0,
                    'currency' => $payment->currency ?? 'INR',
                    'status' => 'completed',
                    'enrolled_at' => now(),
                ]);
            }

            // reload enrollments
            $enrollments = $query->orderBy('created_at', 'desc')->paginate(9);
        }

        return view('livewire.user.purchased-courses', [
            'enrollments' => $enrollments,
        ]);
    }
}
