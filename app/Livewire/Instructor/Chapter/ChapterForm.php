<?php

namespace App\Livewire\Instructor\Chapter;

use App\Models\Chapters;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ChapterForm extends Component
{
    public $courseId;
    public $editingId = null;

    public $form = [
        'chapter_title' => '',
        'order_index' => 0,
    ];

    protected function rules()
    {
        return [
            'form.chapter_title' => ['required', 'string', 'max:255'],
            'form.order_index' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function mount($courseId, $editingId = null)
    {
        $this->courseId = $courseId;
        
        if ($editingId) {
            $this->load($editingId);
        }
    }

    public function load($id)
    {
        $chapter = Chapters::where('course_id', $this->courseId)
            ->findOrFail($id);
        
        $this->editingId = $chapter->id;
        $this->form = [
            'chapter_title' => $chapter->chapter_title,
            'order_index' => $chapter->order_index,
        ];
    }

    public function resetForm()
    {
        $this->form = [
            'chapter_title' => '',
            'order_index' => 0,
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            if ($this->editingId) {
                $chapter = Chapters::where('course_id', $this->courseId)
                    ->findOrFail($this->editingId);
                
                $chapter->update($this->form);
            } else {
                Chapters::create([
                    'course_id' => $this->courseId,
                    'user_id' => auth()->id(),
                    ...$this->form,
                ]);
            }

            $this->dispatch('chapter-saved');

            $this->resetForm();
            $this->editingId = null;
        } catch (\Exception $e) {
            Log::error('Instructor chapter save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the chapter.');
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->dispatch('chapter-cancelled');
    }

    public function render()
    {
        return view('livewire.instructor.chapter.chapter-form');
    }
}
