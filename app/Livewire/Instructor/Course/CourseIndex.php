<?php

namespace App\Livewire\Instructor\Course;

use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;  


#[Title('Course Index')]
#[Layout('components.layouts.instructor')]
class CourseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editingId = null;

    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'tailwind';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $course = Course::where('user_id', auth()->id())
            ->findOrFail($id);
        
        $this->editingId = $course->id;
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->showForm = false;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    public function delete($id)
    {
        try {
            $course = Course::where('user_id', auth()->id())
                ->findOrFail($id);
            
            $course->delete();
            session()->flash('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Instructor course delete error: ' . $e->getMessage());
            session()->flash('error', 'Unable to delete course.');
        }

        $this->confirmingDelete = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function onCourseSaved()
    {
        $this->showForm = false;
        $this->resetPage();
        session()->flash('success', 'Course saved successfully.');
        return redirect()->route('instructor.courses');
    }

    public function onCourseCancelled()
    {
        $this->showForm = false;
    }

    public function render()
    {
        $query = Course::where('user_id', auth()->id());

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.instructor.course.course-index', [
            'courses' => $courses,
        ]);
    }
}
