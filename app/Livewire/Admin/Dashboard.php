<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Course;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Admin Dashboard')]
#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        $totalCourses = Course::count();
        $publishedCourses = Course::where('is_published', true)->count();
        $coursesWithVideos = Course::whereNotNull('video_url')->count();
        
        return view('livewire.admin.dashboard', [
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'coursesWithVideos' => $coursesWithVideos,
            'recentCourses' => Course::latest()->take(5)->get()
        ]);
    }
}