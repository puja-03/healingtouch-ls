<?php

namespace App\Livewire\Admin\Topics;

use App\Models\Topics;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Chapters;

#[Title('Topic Index')]
#[Layout('components.layouts.admin')]
class TopicIndex extends Component
{
    use WithPagination;

    public $showDeleteModal = false;
    public $topicToDelete;
    public $search = '';
    public $chaptersId = '';

    public function confirmDelete($topicId)
    {
        $this->topicToDelete = $topicId;
        $this->showDeleteModal = true;
    }

    public function deleteTopic()
    {
        try {
            $topic = Topics::findOrFail($this->topicToDelete);
            
            // Delete video if exists
            if ($topic->video_url) {
                $path = parse_url($topic->video_url, PHP_URL_PATH);
                if ($path) {
                    Storage::disk('do_spaces')->delete(ltrim($path, '/'));
                }
            }

            $topic->delete();
            session()->flash('success', 'Topic deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting topic: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->topicToDelete = null;
    }

    public function render()
    {
        $query = Topics::with(['chapter', 'chapter.course']);

        if ($this->search) {
            $query->where('topic_title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        }

        if ($this->chaptersId) {
            $query->where('chapters_id', $this->chaptersId);
        }

        return view('livewire.admin.topics.index', [
            'topics' => $query->orderBy('order_index')->paginate(10),
            'chapters' => Chapters::orderBy('chapter_title')->get()
        ]);
    }
}