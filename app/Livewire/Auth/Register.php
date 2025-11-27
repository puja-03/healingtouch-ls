<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout; 

#[Title ('Register')]
#[Layout ('components.layouts.app')]
class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $terms = false;
    public $isLoading = false;


    protected function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', 
            'terms' => 'accepted'
        ];
    }

    public function register()
    {
        $this->isLoading = true;
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'user', 
            'is_user' => true,
            'is_admin' => false,
            'is_instructor' => false,
        ]);

        Auth::login($user);
        $this->isLoading = false;

        return redirect()->route('user.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
