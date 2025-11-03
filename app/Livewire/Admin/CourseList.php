<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
#[Title('Course List')]
class CourseList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $confirmingCourseDeletion = false;
    public $courseIdToDelete;

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmCourseDeletion($courseId)
    {
        $this->confirmingCourseDeletion = true;
        $this->courseIdToDelete = $courseId;
    }

    public function deleteCourse()
    {
        $course = Course::find($this->courseIdToDelete);
        
        if ($course) {
            // Delete featured image if exists
            if ($course->featured_image) {
                Storage::delete($course->featured_image);
            }
            $course->delete();
            session()->flash('message', 'Course deleted successfully.');
        }

        $this->confirmingCourseDeletion = false;
        $this->courseIdToDelete = null;
    }

    public function togglePublish($courseId)
    {
        $course = Course::find($courseId);
        if ($course) {
            $course->update(['is_published' => !$course->is_published]);
            session()->flash('message', 'Course status updated successfully.');
        }
    }

    public function render()
    {
        $courses = Course::when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.course-list', [
            'courses' => $courses
        ])->layout('layouts.admin');
    }
}