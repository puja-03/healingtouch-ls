<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Course Form')]

class CourseForm extends Component
{
    public $courseId;
    public $title;
    public $description;
    public $price = 0;
    public $is_published = false;

    protected function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'is_published' => 'boolean'
        ];
    }

    public function mount($courseId = null)
    {
        if ($courseId) {
            $this->courseId = $courseId;
            $course = Course::findOrFail($courseId);
            $this->title = $course->title;
            $this->description = $course->description;
            $this->price = $course->price;
            $this->is_published = $course->is_published;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'price' => $this->price,
                'is_published' => $this->is_published,
            ];

            // Save/Update Course
            if ($this->courseId) {
                Course::find($this->courseId)->update($data);
                session()->flash('success', 'Course updated successfully with all uploads.');
            } else {
                Course::create($data);
                session()->flash('success', 'Course created successfully with all uploads.');
            }

            return redirect()->route('admin.courses');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save course: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.admin.course-form');
    }
}