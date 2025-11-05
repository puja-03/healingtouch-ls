<?php

namespace App\Livewire\Admin\Chapters;

use App\Models\Chapters;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title ('Chapter Index')]
#[Layout('components.layouts.admin')]
class ChapterIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $showDeleteModal = false;
    public $chapterToDelete;

    public function confirmDelete($chapterId)
    {
        $this->chapterToDelete = $chapterId;
        $this->showDeleteModal = true;
    }

    public function deleteChapter()
    {
        try {
            $chapter = Chapters::findOrFail($this->chapterToDelete);
            $chapter->delete();
            session()->flash('success', 'Chapter deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting chapter: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->chapterToDelete = null;
    }

    public function render()
    {
        return view('livewire.admin.chapters.index', [
            'chapters' => Chapters::with(['course', 'topics'])
                ->orderBy('order_index')
                ->paginate(10)
        ]);
    }
}