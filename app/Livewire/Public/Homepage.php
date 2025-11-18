<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Course;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Topic;
use App\Models\Chapters;

#[Layout('components.layouts.app')]
#[Title('homepage')]
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

        return view('livewire.public.homepage', [
            'courses' => $courses,
        ]);
    }
}
