<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Course Form')]
class CourseForm extends Component
{
    use WithFileUploads;

    public $courseId;
    public $title;
    public $description;
    public $price = 0;
    public $is_published = false;
    public $featured_image;
    public $video;
    public $video_url;
    public $content;
    public $order = 0;
    public $temporaryUrl;
    public $existingImage;
    public $uploadProgress = 0;
    public $uploadError = null;
    protected $maxFileSize = 102400; // 100MB in kilobytes

    protected function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'featured_image' => $this->courseId ? 'nullable|image|max:1024' : 'nullable|image|max:1024',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:102400',
            'video_url' => 'nullable',
            'content' => 'nullable',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean'
        ];
    }

    public function mount($courseId = null)
    {
        if ($courseId) {
            $this->courseId = $courseId;
            $course = Course::findOrFail($courseId);
            $this->title = $course->title;
            $this->description = $course->description;
            $this->price = $course->price;
            $this->is_published = $course->is_published;
            $this->video_url = $course->video_url;
            $this->content = $course->content;
            $this->order = $course->order;
            $this->existingImage = $course->featured_image;
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'featured_image' => $this->courseId ? 'nullable|image|max:1024' : 'nullable|image|max:1024',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:102400', // max 100MB
            'content' => 'nullable',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean'
        ]);
        
        try {
            $this->uploadProgress = 0;
            
            $data = [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'price' => $this->price,
                'is_published' => $this->is_published,
                'content' => $this->content,
                'order' => $this->order,
            ];

            // Handle featured image upload
            if ($this->featured_image) {
                try {
                    $imageName = time() . '_' . Str::random(10) . '.' . $this->featured_image->getClientOriginalExtension();
                    $folder = 'course-images';
                    
                    $path = Storage::disk('do_spaces')->putFileAs(
                        $folder,
                        $this->featured_image,
                        $imageName,
                        'public'
                    );
                    
                    $data['featured_image'] = Storage::disk('do_spaces')->url($path);
                    
                    if ($this->courseId && $this->existingImage) {
                        Storage::disk('do_spaces')->delete($this->existingImage);
                    }
                    
                    $this->uploadProgress = 50;
                } catch (\Exception $e) {
                    session()->flash('error', 'Image upload failed: ' . $e->getMessage());
                    return;
                }
            }

            // Handle video upload with proper error handling and validation
            if ($this->video) {
                try {
                    // Validate video size
                    if ($this->video->getSize() > $this->maxFileSize * 1024) {
                        throw new \Exception('Video file size exceeds the maximum limit of 100MB');
                    }

                    $videoFile = $this->video;
                    $fileName = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
                    
                    // Ensure the folder exists and is properly named
                    $folder = 'course-videos';

                    // Set upload progress to indicate start
                    $this->uploadProgress = 10;

                    // First store locally to ensure file is valid
                    $localPath = $videoFile->storeAs('temp', $fileName, 'local');
                    $this->uploadProgress = 30;

                    if (!Storage::disk('local')->exists($localPath)) {
                        throw new \Exception('Failed to process video file');
                    }

                    // Now upload to DigitalOcean Spaces
                    $this->uploadProgress = 50;
                    
                    $path = Storage::disk('do_spaces')->putFileAs(
                        $folder,
                        Storage::disk('local')->path($localPath),
                        $fileName,
                        'public'
                    );

                    if (!$path) {
                        throw new \Exception('Failed to upload to DigitalOcean');
                    }

                    // Clean up local temp file
                    Storage::disk('local')->delete($localPath);
                    
                    // Get the full URL
                    $data['video_url'] = Storage::disk('do_spaces')->url($path);
                    
                    // If we're updating, remove old video
                    if ($this->courseId && $this->video_url) {
                        $oldPath = parse_url($this->video_url, PHP_URL_PATH);
                        if ($oldPath) {
                            Storage::disk('do_spaces')->delete(ltrim($oldPath, '/'));
                        }
                    }

                    $this->uploadProgress = 100;
                    session()->flash('success', 'Video uploaded successfully!');
                } catch (\Exception $e) {
                    \Log::error('Video upload failed: ' . $e->getMessage());
                    session()->flash('error', 'Video upload failed: ' . $e->getMessage());
                    $this->uploadProgress = 0;
                    return;
                }
            }

            if ($this->courseId) {
                Course::find($this->courseId)->update($data);
                session()->flash('success', 'Course updated successfully with all uploads.');
            } else {
                Course::create($data);
                session()->flash('success', 'Course created successfully with all uploads.');
            }

            $this->uploadProgress = false;
            return redirect()->route('admin.courses');

        } catch (\Exception $e) {
            $this->uploadProgress = false;
            session()->flash('error', 'Failed to save course: ' . $e->getMessage());
            return;
        }
    }

    public function updatedFeaturedImage()
    {
        $this->validate([
            'featured_image' => 'image|max:1024'
        ]);
        $this->temporaryUrl = $this->featured_image->temporaryUrl();
    }

    public function updatedVideo()
    {
        $this->validate([
            'video' => 'file|mimetypes:video/mp4,video/quicktime|max:102400'
        ]);
        
        // Reset progress when new video is selected
        $this->uploadProgress = 0;
        $this->uploadError = null;
    }
    public function render()
    {
        return view('livewire.admin.course-form')->layout('layouts.admin');
    }
}