<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

///laravel se video uploade 
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\VideoUploadsController;
//ADMIN  
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\CourseList; 
use App\Livewire\Admin\CourseForm;

//Instructor
use App\Livewire\Instructor\Dashboard As InstructorDashboard;

//Public 
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\User\Dashboard As UserDashboard;

// Public/auth routes
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');

Route::post('/logout', function (Request $request) {
	Auth::logout();
	$request->session()->invalidate();
	$request->session()->regenerateToken();
	return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->prefix('admin')->group(function () {
        Route::get('/', Dashboard::class)->name('admin.dashboard');
        Route::get('/courses', CourseList::class)->name('admin.courses');
        Route::get('/courses/create', CourseForm::class)->name('admin.courses.create');
        Route::get('/courses/{courseId}/edit', CourseForm::class)->name('admin.courses.edit');
    });

    // Instructor Routes
    Route::middleware(\App\Http\Middleware\InstructorMiddleware::class)->prefix('instructor')->group(function () {
        Route::get('/', InstructorDashboard::class)->name('instructor.dashboard');
    });

    // User Routes
    Route::get('/dashboard', UserDashboard::class)->name('user.dashboard');

    // Default route for authenticated users
    Route::get('/', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    });

    // User Routes
    Route::get('/dashboard',UserDashboard::class)->name('user.dashboard');
});


