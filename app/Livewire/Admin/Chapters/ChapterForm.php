<?php

namespace App\Livewire\Admin\Chapters;

use App\Models\Chapters;
use App\Models\Course;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Chapter Form')]
#[Layout('components.layouts.admin')]

class ChapterForm extends Component
{
    public $courseId;
    public $chapterId;
    public $chapter_title;
    public $order_index = 0;
    public $selectedCourse;

    protected $rules = [
        'chapter_title' => 'required|min:3',
        'order_index' => 'required|numeric|min:0',
    ];

    public function mount($courseId = null, $chapterId = null)
    {
        $this->courseId = $courseId;
        $this->selectedCourse = $courseId;
        if ($chapterId) {
            $this->loadChapter($chapterId);
        }
    }

    public function loadChapter($id)
    {
        $chapter = Chapters::findOrFail($id);
        $this->chapterId = $chapter->id;
        $this->chapter_title = $chapter->chapter_title;
        $this->order_index = $chapter->order_index;
        // Ensure the course select is prefilled when editing
        $this->selectedCourse = $chapter->course_id;
        $this->courseId = $chapter->course_id;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'chapter_title' => $this->chapter_title,
                'chapter_slug' => Str::slug($this->chapter_title),
                'order_index' => $this->order_index,
                'course_id' => $this->selectedCourse,
            ];

            if ($this->chapterId) {
                $chapter = Chapters::findOrFail($this->chapterId);
            $chapter->update($data);
            session()->flash('success', 'Chapter updated successfully!');
            } else {
                Chapters::create($data);
                $message = 'Chapter created successfully!';
            }

            session()->flash('success', $message);
            return redirect()->route('admin.chapters', ['course_id' => $this->courseId]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.chapters.chapter-form', [
            'course' => Course::find($this->courseId),
            'courses' => Course::all()
        ]);
    }
}