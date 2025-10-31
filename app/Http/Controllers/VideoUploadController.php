<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:102400', // max 100MB
        ]);

        $videoFile = $request->file('video');
        // Generate a unique file name
        $fileName = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
        $folder = 'videos'; // Folder inside your DigitalOcean Space

        try {
            $path = Storage::disk('do_spaces')->putFileAs(
                $folder,
                $videoFile,
                $fileName,
                'public' // Makes the file publicly accessible
            );

            // --- 3. Get URL and Respond ---
            $videoUrl = Storage::disk('do_spaces')->url($path);

            return response()->json([
                'message' => 'Video uploaded successfully.',
                'url' => $videoUrl,
                'path' => $path,
            ], 201);

        } catch (\Exception $e) {
            // Handle upload or connection errors
            return response()->json(['message' => 'Video upload failed.', 'error' => $e->getMessage()], 500);
        }
    }
}
