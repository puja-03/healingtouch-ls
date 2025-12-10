<?php

namespace App\Livewire\Instructor\Chapter;

use App\Models\Chapters;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

#[Title('Chapter Form')]
class ChapterForm extends Component
{
    public $courseId;
    public $editingId = null;

    public $chapter_title = '';
    public $order_index = 1;

    protected function rules()
    {
        return [
            'chapter_title' => 'required|string|min:3|max:255',
            'order_index' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'chapter_title.required' => 'Chapter title is required.',
        'chapter_title.min' => 'Chapter title must be at least 3 characters.',
        'order_index.required' => 'Order index is required.',
        'order_index.min' => 'Order index must be at least 1.',
    ];

    public function mount($courseId, $editingId = null)
    {
        $this->courseId = $courseId;
        $this->editingId = $editingId;
        
        if ($editingId) {
            $this->loadChapter($editingId);
        } else {
            $this->setNextOrderIndex();
        }
    }

    public function loadChapter($id)
    {
        try {
            $chapter = Chapters::where('course_id', $this->courseId)
                ->findOrFail($id);
            
            $this->chapter_title = $chapter->chapter_title;
            $this->order_index = $chapter->order_index;
            
        } catch (\Exception $e) {
            Log::error('Error loading chapter: ' . $e->getMessage());
            $this->dispatch('error', message: 'Chapter not found.');
            $this->cancel();
        }
    }

    public function setNextOrderIndex()
    {
        $lastChapter = Chapters::where('course_id', $this->courseId)
            ->orderBy('order_index', 'desc')
            ->first();
        
        $this->order_index = $lastChapter ? $lastChapter->order_index + 1 : 1;
    }

    public function save()
    {
        $this->validate();

        try {
            $chapterData = [
                'chapter_title' => $this->chapter_title,
                'order_index' => $this->order_index,
                'course_id' => $this->courseId,
                'user_id' => auth()->id(),
            ];

            Log::info('Attempting to save chapter:', $chapterData);

            if ($this->editingId) {
                // Update existing chapter
                $chapter = Chapters::where('course_id', $this->courseId)
                    ->where('id', $this->editingId)
                    ->first();
                
                if (!$chapter) {
                    throw new \Exception('Chapter not found for editing.');
                }
                
                $chapter->update($chapterData);
                $message = 'Chapter updated successfully.';
                Log::info('Chapter updated:', ['id' => $chapter->id]);
            } else {
                // Create new chapter
                $chapter = Chapters::create($chapterData);
                $message = 'Chapter created successfully.';
                Log::info('Chapter created:', ['id' => $chapter->id]);
            }

            $this->resetForm();
            $this->dispatch('chapter-saved', message: $message);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Chapter save error: ' . $e->getMessage());
            $this->dispatch('error', message: 'Failed to save chapter: ' . $e->getMessage());
            return false;
        }
    }

    private function resetForm()
    {
        $this->chapter_title = '';
        $this->order_index = 1;
        $this->editingId = null;
    }

    public function cancel()
    {
        $this->resetForm();
        $this->dispatch('chapter-cancelled');
    }

    public function render()
    {
        return view('livewire.instructor.chapter.chapter-form');
    }
}