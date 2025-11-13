<?php

namespace App\Livewire\Instructor\Topic;

use App\Models\Topics;
use App\Models\Chapters;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Topic Index')]
#[Layout('components.layouts.instructor')]
class TopicIndex extends Component
{
    use WithPagination;

    public $chapterId;
    public $search = '';
    public $showForm = false;
    public $editingId = null;
    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount($chapterId)
    {
        // Verify chapter belongs to logged-in instructor's course
        $chapter = Chapters::with('course')
            ->findOrFail($chapterId);
        
        if ($chapter->course->user_id !== auth()->id()) {
            abort(403);
        }

        $this->chapterId = $chapterId;
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
        $this->editingId = $id;
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
            $topic = Topics::where('chapters_id', $this->chapterId)
                ->findOrFail($id);
            
            // Delete video from storage if exists
            if ($topic->video_url) {
                $this->deleteOldVideo($topic->video_url);
            }

            $topic->delete();
            
            session()->flash('success', 'Topic deleted successfully.');
            $this->resetPage();
            
        } catch (\Exception $e) {
            Log::error('Topic delete error: ' . $e->getMessage());
            session()->flash('error', 'Unable to delete topic: ' . $e->getMessage());
        }

        $this->confirmingDelete = false;
        $this->deletingId = null;
    }

    protected function deleteOldVideo($videoUrl)
    {
        try {
            $path = parse_url($videoUrl, PHP_URL_PATH);
            if ($path) {
                \Illuminate\Support\Facades\Storage::disk('do_spaces')->delete(ltrim($path, '/'));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete old video: ' . $e->getMessage());
        }
    }

    #[On('topic-saved')]
    public function onTopicSaved($message = null)
    {
        $this->showForm = false;
        $this->editingId = null;
        
        if ($message) {
            session()->flash('success', $message);
        } else {
            session()->flash('success', 'Topic saved successfully.');
        }
        
        $this->resetPage();
    }

    #[On('topic-cancelled')]
    public function onTopicCancelled()
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
        $chapter = Chapters::findOrFail($this->chapterId);

        $query = Topics::where('chapters_id', $this->chapterId);

        if (!empty($this->search)) {
            $query->where('topic_title', 'like', '%' . $this->search . '%');
        }

        $topics = $query->orderBy('order_index')->paginate(10);

        return view('livewire.instructor.topic.topic-index', [
            'topics' => $topics,
            'chapter' => $chapter,
        ]);
    }
}