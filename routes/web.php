<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\VideoUploadsController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\VideoUpload;
use App\Livewire\Admin\CourseList; 
use App\Livewire\Admin\CourseForm;

// Route ::get('/videos/upload', [VideoUploadsController::class, 'create'])->name('video.create');
// Route ::post('/videos/upload', [VideoUploadsController::class, 'store'])->name('video.store');
  Route::get('/', Dashboard::class)->name('admin.dashboard');
    Route::get('/courses', CourseList::class)->name('admin.courses');
    Route::get('/courses/create', CourseForm::class)->name('admin.courses.create');
    Route::get('/courses/{courseId}/edit', CourseForm::class)->name('admin.courses.edit');


