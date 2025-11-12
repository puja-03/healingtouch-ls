<?php

namespace App\Livewire\Instructor\Course;

use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Course Form')]
#[Layout('components.layouts.instructor')]
class CourseForm extends Component
{
    public $editingId = null;

    public $form = [
        'title' => '',
        'description' => '',
        'price' => '',
        'is_published' => false,
    ];

    protected function rules()
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.description' => ['nullable', 'string'],
            'form.price' => ['nullable', 'integer', 'min:0'],
            'form.is_published' => ['boolean'],
        ];
    }

    public function mount($editingId = null)
    {
        if ($editingId) {
            $this->load($editingId);
        }
    }

    public function load($id)
    {
        $course = Course::where('user_id', auth()->id())
            ->findOrFail($id);
        
        $this->editingId = $course->id;
        $this->form = [
            'title' => $course->title,
            'description' => $course->description,
            'price' => $course->price,
            'is_published' => $course->is_published,
        ];
    }

    public function resetForm()
    {
        $this->form = [
            'title' => '',
            'description' => '',
            'price' => '',
            'is_published' => false,
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            if ($this->editingId) {
                $course = Course::where('user_id', auth()->id())
                    ->findOrFail($this->editingId);
                
                $course->update($this->form);
            } else {
                Course::create([
                    'user_id' => auth()->id(),
                    ...$this->form,
                ]);
            }

            $this->dispatch('course-saved');
            
            session()->flash('message', 'Course saved successfully!');
            
            $this->resetForm();
            $this->editingId = null;
            
            return redirect()->route('instructor.courses');
            
        } catch (\Exception $e) {
            Log::error('Instructor course save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the course.');
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->editingId = null;
        
        // Option 1: Use dispatch for events (Livewire v3)
        $this->dispatch('course-cancelled');
        
        // Option 2: Simply reset without event
        // No action needed beyond the reset above
    }

    public function render()
    {
        return view('livewire.instructor.course.course-form');
    }
}