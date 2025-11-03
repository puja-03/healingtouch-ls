<?php

namespace App\Livewire\Instructor;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title ('Instructor Dashboard')]
#[Layout ('components.layouts.instructor')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.instructor.dashboard');
    }
}