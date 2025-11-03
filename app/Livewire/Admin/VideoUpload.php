<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class VideoUpload extends Component
{
    use WithFileUploads;

    public $video;
    public $title;
    public $description;
    public $course_id;
    public $uploadProgress = false;

    protected $rules = [
        'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:102400',
        'title' => 'required|min:3',
        'description' => 'nullable',
        'course_id' => 'required|exists:courses,id'
    ];

    public function save()
    {
        $this->validate();

        try {
            $this->uploadProgress = true;

            $videoFile = $this->video;
            $fileName = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
            $folder = 'videos';

            $path = Storage::disk('do_spaces')->putFileAs(
                $folder,
                $videoFile,
                $fileName,
                'public'
            );

            $videoUrl = Storage::disk('do_spaces')->url($path);

            // Update the course with video information
            $course = Course::find($this->course_id);
            $course->update([
                'video_url' => $videoUrl,
                'title' => $this->title,
                'description' => $this->description
            ]);

            session()->flash('message', 'Video uploaded successfully!');
            $this->reset(['video', 'title', 'description']);
            $this->uploadProgress = false;

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload video: ' . $e->getMessage());
            $this->uploadProgress = false;
        }
    }

    public function render()
    {
        return view('livewire.admin.video-upload', [
            'courses' => Course::all()
        ])->layout('layouts.admin');
    }
}