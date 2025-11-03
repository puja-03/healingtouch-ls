<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title ('User Dashboard')]
#[Layout ('components.layouts.user')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.user.dashboard');
    }
}