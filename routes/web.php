<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\VideoUploadsController;

// Route::post('/', function (Request $request) {
//     $file = $request->file(key:'image');
//     $path = $file->storePubliclyAs(path:'youtube-tutorials/'.$file->getClientOriginalName());
//     // $path = Storage::putFileAs('youtube-tutorials', $file, $file->getClientOriginalName());
//     // Storage::setVisibility($path, 'public');
//     // Storage::delete($path);
//     return response()->json([
//         'path' => $path,
//         'url' => Storage::url($path)
//     ]);
// })->withoutMiddleware([VerifyCsrfToken::class]);


// routes/web.php


Route::get('/upload-video', [VideoUploadsController::class, 'create'])->name('video.create');
Route::post('/upload-video', [VideoUploadsController::class, 'store'])->name('video.store');