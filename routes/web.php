<?php
use App\Livewire\Public\Homepage;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\User\Dashboard As UserDashboard;
use App\Livewire\Public\CourseDetail;
use App\Livewire\User\PurchasedCourses;
// use App\Livewire\Public\CourseCheckout;
use App\Livewire\Public\CoursePlayer;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\InstructorMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\PaymentController;
//ADMIN  
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\CourseList; 
use App\Livewire\Admin\CourseForm;
use App\Livewire\Admin\Chapters\ChapterIndex;
use App\Livewire\Admin\Chapters\ChapterForm;
use App\Livewire\Admin\Topics\TopicIndex ;
use App\Livewire\Admin\Topics\TopicForm;
use App\Livewire\Admin\Instructor\InstructorIndex ;
use App\Livewire\Admin\Instructor\InstructorForm ;
//Instructor
use App\Livewire\Instructor\Dashboard As InstructorDashboard;
use App\Livewire\Instructor\Course\CourseIndex as InstructorCourseIndex;
use App\Livewire\Instructor\Course\CourseForm as InstructorCourseForm;
use App\Livewire\Instructor\Chapter\ChapterIndex as InstructorChapterIndex;
use App\Livewire\Instructor\Chapter\ChapterForm as InstructorChapterForm;
use App\Livewire\Instructor\Topic\TopicIndex as InstructorTopicIndex;
use App\Livewire\Instructor\Topic\TopicForm as InstructorTopicForm;
use App\Livewire\Instructor\Profile\ProfileForm;

// Public/auth routesuse App\Livewire\Public\CourseCheckout;

Route::get('/', Homepage::class)->name('home');
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');
Route::get('/courses/{course:slug}', CourseDetail::class)->name('courses.show');
// Razorpay payment routes

Route::get('/course/{course}/checkout', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
Route::post('/create-order', [PaymentController::class, 'createOrder'])->name('payment.create-order');
Route::post('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');

     

Route::post('/logout', function (Request $request) {
	Auth::logout();
	$request->session()->invalidate();
	$request->session()->regenerateToken();
	return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
        Route::get('/', Dashboard::class)->name('admin.dashboard');
        Route::get('/courses', CourseList::class)->name('admin.courses');
        Route::get('/courses/create', CourseForm::class)->name('admin.courses.create');
        Route::get('/courses/{courseId}/edit', CourseForm::class)->name('admin.courses.edit');
        
        // Chapter Routes
        Route::get('/chapters', ChapterIndex::class)->name('admin.chapters');
        Route::get('/chapters/create', ChapterForm::class)->name('admin.chapters.create');
        Route::get('/chapters/{chapter_id}/edit', ChapterForm::class)->name('admin.chapters.edit');

        // Topic Routes
        Route::get('/topics', TopicIndex::class)->name('admin.topics');
        Route::get('/topics/create', TopicForm::class)->name('admin.topics.create');
        Route::get('/topics/{topic_id}/edit', TopicForm::class)->name('admin.topics.edit');
         
        // Instructor Routes
        Route::get('/instructors', InstructorIndex::class)->name('admin.instructors');
        Route::get('/instructors/create', InstructorForm::class)->name('admin.instructors.create');
        Route::get('/instructors/{instructor_id}/edit', InstructorForm::class)->name('admin.instructors.edit');
    });

    // Instructor Routes
    Route::middleware(InstructorMiddleware::class)->prefix('instructor')->group(function () {
        Route::get('/', InstructorDashboard::class)->name('instructor.dashboard');
        Route::get('/courses', InstructorCourseIndex::class)->name('instructor.courses');
        Route::get('/courses/create', InstructorCourseForm::class)->name('instructor.courses.create');
        Route::get('/courses/{course:slug}/edit', InstructorCourseForm::class)->name('instructor.courses.edit');
        
        // Chapter Routes (slug based)
        Route::get('/chapters/{course:slug}', InstructorChapterIndex::class)->name('instructor.chapter');
        Route::get('/chapters/{course:slug}/create', InstructorChapterForm::class)->name('instructor.chapters.create');
        Route::get('/chapters/{chapter:chapter_slug}/edit', InstructorChapterForm::class)->name('instructor.chapters.edit');
        
        // Topic Routes (slug based)
        Route::get('/topics/{chapter:chapter_slug}',InstructorTopicIndex::class)->name('instructor.topic');
        Route::get('/topics/{chapter:chapter_slug}/create', InstructorTopicForm::class)->name('instructor.topics.create');
        Route::get('/topics/{topic:topic_slug}/edit',InstructorTopicForm::class)->name('instructor.topics.edit');

        Route::get('/profile', ProfileForm::class)->name('instructor.profile');
    });

    Route::middleware(UserMiddleware::class)->prefix('user')->group(function () {
        Route::get('/purchased-courses', PurchasedCourses::class)->name('user.courses');
        Route::get('/dashboard',UserDashboard::class)->name('user.dashboard');
        // Route::get('/course/{courseId}/checkout', CourseCheckout::class)->name('user.checkout');
        Route::get('/play/{courseId}', CoursePlayer::class)->name('user.play-course');
    });

});

