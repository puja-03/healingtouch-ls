<?php

namespace App\Livewire\Instructor\Chapter;

use App\Models\Chapters;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Chapter Index')]
#[Layout('components.layouts.instructor')]
class ChapterIndex extends Component
{
    use WithPagination;

    public $course; // Course object directly
    public $search = '';
    public $showForm = false;
    public $editingId = null;
    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount(Course $course)
    {
        // Course automatically resolved by slug
        $this->course = $course;
        
        // Verify course belongs to logged-in instructor
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
    }

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
        $chapter = Chapters::where('course_id', $this->course->id)
            ->findOrFail($id);
        
        $this->editingId = $chapter->id;
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->editingId = null;
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
            $chapter = Chapters::where('course_id', $this->course->id)
                ->findOrFail($id);
            
            $chapter->delete();
            session()->flash('success', 'Chapter deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Instructor chapter delete error: ' . $e->getMessage());
            session()->flash('error', 'Unable to delete chapter.');
        }

        $this->confirmingDelete = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    #[On('chapter-saved')]
    public function onChapterSaved()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetPage();
        session()->flash('success', 'Chapter saved successfully.');
    }

    #[On('chapter-cancelled')]
    public function onChapterCancelled()
    {
        $this->showForm = false;
        $this->editingId = null;
    }

    public function render()
    {
        $query = Chapters::where('course_id', $this->course->id);

        if (!empty($this->search)) {
            $query->where('chapter_title', 'like', '%' . $this->search . '%');
        }

        $chapters = $query->orderBy('order_index')->paginate(10);

        return view('livewire.instructor.chapter.chapter-index', [
            'chapters' => $chapters,
            'course' => $this->course,
        ]);
    }
}