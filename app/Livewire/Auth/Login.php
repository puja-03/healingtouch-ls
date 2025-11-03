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

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function authenticate()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            $user = Auth::user();
            
            // Strict role checking - no fallback to prevent incorrect access
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            if ($user->isInstructor()) {
                return redirect()->route('instructor.dashboard');
            }
            
            if ($user->isUser()) {
                return redirect()->route('user.dashboard');
            }
            
            // If no valid role, log them out
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Invalid user role. Please contact administrator.');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
