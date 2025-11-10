<?php

namespace App\Livewire\Admin\Instructor;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Instructor Form')]
#[Layout('components.layouts.admin')]

class InstructorForm extends Component
{
    public $editingId = null;

    public $form = [
        'name' => '',
        'email' => '',
        'role' => 'instructor',
        'password' => '',
    ];

    protected $listeners = [
        'editInstructor' => 'load',
        'createInstructor' => 'initCreate',
    ];

    protected function rules()
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'form.role' => ['required', 'string', Rule::in(['instructor', 'admin', 'user'])],
            // password rule added dynamically in save()
        ];
    }

    public function mount($editingId = null)
    {
        if ($editingId) {
            $this->load($editingId);
        }
    }

    public function initCreate()
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function load($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->form = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => '',
        ];
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'role' => 'instructor',
            'password' => '',
        ];
    }

    public function save()
    {
        // merge dynamic password rule
        $rules = $this->rules();

        if ($this->editingId) {
            $rules['form.password'] = ['nullable', 'string', 'min:6'];
        } else {
            $rules['form.password'] = ['required', 'string', 'min:6'];
        }

        $validated = $this->validate($rules);

        try {
            if ($this->editingId) {
                $user = User::findOrFail($this->editingId);
                $user->name = $this->form['name'];
                $user->email = $this->form['email'];
                $user->role = $this->form['role'];

                if (!empty($this->form['password'])) {
                    $user->password = Hash::make($this->form['password']);
                }

                $user->is_instructor = $user->role === 'instructor';
                $user->save();

                session()->flash('success', 'Instructor updated successfully.');
            } else {
                $user = User::create([
                    'name' => $this->form['name'],
                    'email' => $this->form['email'],
                    'role' => $this->form['role'],
                    'password' => Hash::make($this->form['password']),
                    'is_instructor' => $this->form['role'] === 'instructor',
                ]);

                session()->flash('success', 'Instructor created successfully.');
            }

            // emit event for parent/list to refresh
            $this->emitUp('instructorSaved');

            // reset local state
            $this->editingId = null;
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('InstructorForm save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the instructor.');
        }
    }

    public function cancel()
    {
        $this->editingId = null;
        $this->resetForm();
        $this->emitUp('instructorCancelled');
    }

    public function render()
    {
        return view('livewire.admin.instructor.instructor-form');
    }
}
