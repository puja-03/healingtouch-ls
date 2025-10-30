<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::post('/', function (Request $request) {
    $file = $request->file('image');
    $path = Storage::putFile('youtube-tutorials', $file);
    Storage::setVisibility($path, 'public');
    return response()->json([
        'path' => $path,
        'url' => Storage::url($path)
    ]);
})->withoutMiddleware([VerifyCsrfToken::class]);
