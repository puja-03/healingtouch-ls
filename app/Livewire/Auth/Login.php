<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout; 

#[Title ('Login')]
#[Layout ('components.layouts.app')]
class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;
    public $isLoading = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function authenticate()
    {
        $this->isLoading = true;
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            $user = Auth::user();
            
            // Determine default redirect based on role
            if ($user->isAdmin()) {
                $default = route('admin.dashboard');
            } elseif ($user->isInstructor()) {
                $default = route('instructor.dashboard');
            } else {
                $default = route('user.dashboard');
            }
            return redirect()->intended($default);
        }

        $this->addError('email', 'The provided credentials do not match our records.');
            $this->isLoading = false;

    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}