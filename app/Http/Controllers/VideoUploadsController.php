<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadsController extends Controller
{
     
    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:102400', // max 100MB
        ]);

        $videoFile = $request->file('video');
        $fileName = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
        $folder = 'videos';

        try {
            // 2. Upload using Storage Facade
            $path = Storage::disk('do_spaces')->putFileAs(
                $folder,
                $videoFile,
                $fileName,
                'public'
            );

            $videoUrl = Storage::disk('do_spaces')->url($path);
            return redirect()
                ->route('video.create') 
                ->with('success', 'Video uploaded successfully!');     // URL: ' . $videoUrl


        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Video upload failed. Error: ' . $e->getMessage());
        }
    }
}
