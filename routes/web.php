<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\VideoUploadsController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\VideoUpload;
use App\Livewire\Admin\CourseList; 
use App\Livewire\Admin\CourseForm;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

// Public/auth routes
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');

Route::post('/logout', function (Request $request) {
	Auth::logout();
	$request->session()->invalidate();
	$request->session()->regenerateToken();
	return redirect()->route('login');
})->name('logout');

// Admin routes - require authentication
Route::middleware('auth')->group(function () {
	Route::get('/', Dashboard::class)->name('admin.dashboard');
	Route::get('/courses', CourseList::class)->name('admin.courses');
	Route::get('/courses/create', CourseForm::class)->name('admin.courses.create');
	Route::get('/courses/{courseId}/edit', CourseForm::class)->name('admin.courses.edit');
});


