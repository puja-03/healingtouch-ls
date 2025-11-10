<?php

namespace App\Livewire\Admin\Instructor;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Instructor Index')]
#[Layout('components.layouts.admin')]
class InstructorIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editingId = null;
    public $form = [
        'name' => '',
        'email' => '',
        'role' => 'instructor',
        'password' => '',
    ];

    public $confirmingDelete = false;
    public $deletingId = null;

    protected $paginationTheme = 'tailwind';

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
            // password rule applied dynamically in save()
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->form = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => '', // leave empty unless changing
        ];
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    protected function resetForm()
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
        // Merge rules and dynamic password rule
        $rules = $this->rules();

        if ($this->editingId) {
            // editing: password optional
            $rules['form.password'] = ['nullable', 'string', 'min:6'];
        } else {
            // creating: password required
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

                // maintain instructor flags if present
                if ($user->role === 'instructor') {
                    $user->is_instructor = true;
                } else {
                    $user->is_instructor = false;
                }

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

            $this->showForm = false;
            $this->resetForm();
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error('Instructor save error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the instructor.');
        }
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
            $user = User::findOrFail($id);
            $user->delete();

            session()->flash('success', 'Instructor deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Instructor delete error: ' . $e->getMessage());
            session()->flash('error', 'Unable to delete instructor.');
        }

        $this->confirmingDelete = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query()->where('role', 'instructor');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $instructors = $query->orderBy('name')->paginate(10);

        return view('livewire.admin.instructor.instructor-index', [
            'instructors' => $instructors,
        ]);
    }
}
