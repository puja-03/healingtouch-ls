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

    public $courseId;
    public $search = '';
    public $showForm = false;
    public $editingId = null;
    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount($courseId)
    {
        // Verify course belongs to logged-in instructor
        $this->courseId = Course::where('user_id', auth()->id())
            ->findOrFail($courseId)->id;
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
        $chapter = Chapters::where('course_id', $this->courseId)
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
            $chapter = Chapters::where('course_id', $this->courseId)
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
    public function onChapterSaved($message = null)
    {
        $this->showForm = false;
        $this->editingId = null;
        
        if ($message) {
            session()->flash('success', $message);
        } else {
            session()->flash('success', 'Chapter saved successfully.');
        }
        
        $this->resetPage();
    }

    #[On('chapter-cancelled')]
    public function onChapterCancelled()
    {
        $this->showForm = false;
        $this->editingId = null;
    }

    #[On('error')]
    public function onError($message)
    {
        session()->flash('error', $message);
        $this->showForm = false;
        $this->editingId = null;
    }   

    public function render()
    {
        $course = Course::findOrFail($this->courseId);

        $query = Chapters::where('course_id', $this->courseId);

        if (!empty($this->search)) {
            $query->where('chapter_title', 'like', '%' . $this->search . '%');
        }

        $chapters = $query->orderBy('order_index')->paginate(10);

        return view('livewire.instructor.chapter.chapter-index', [
            'chapters' => $chapters,
            'course' => $course,
        ]);
    }
}