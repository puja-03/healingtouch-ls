<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoUploadController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// routes/api.php

Route::post('/video/upload', [VideoUploadController::class, 'store']);
