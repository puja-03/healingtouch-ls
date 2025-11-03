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
            // Max file size validation is handled here
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
        $this->validate(); // Validation using the rules() method

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

            // --- Handle featured image upload (Logic is already correct) ---
            if ($this->featured_image) {
                try {
                    $imageName = time() . '_' . Str::random(10) . '.' . $this->featured_image->getClientOriginalExtension();
                    $folder = 'course-images';
                    
                    $path = Storage::disk('do_spaces')->putFileAs(
                        $folder,
                        $this->featured_image, // Passing UploadedFile object directly
                        $imageName,
                        'public'
                    );
                    
                    $data['featured_image'] = Storage::disk('do_spaces')->url($path);
                    
                    if ($this->courseId && $this->existingImage) {
                        // Attempt to delete old image based on its URL path
                        $oldImagePath = parse_url($this->existingImage, PHP_URL_PATH);
                        if ($oldImagePath) {
                            Storage::disk('do_spaces')->delete(ltrim($oldImagePath, '/'));
                        }
                    }
                    
                    $this->uploadProgress = 50;
                } catch (\Exception $e) {
                    session()->flash('error', 'Image upload failed: ' . $e->getMessage());
                    \Log::error('Image upload failed: ' . $e->getMessage());
                    return;
                }
            }

            // --- Handle video upload (CORRECTED LOGIC) ---
            if ($this->video) {
                try {
                    $videoFile = $this->video; // This is the Livewire UploadedFile object
                    $fileName = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
                    $folder = 'course-videos';

                    // Set upload progress to indicate start
                    $this->uploadProgress = 10;
                    
                    // UPLOAD DIRECTLY to DigitalOcean Spaces
                    $path = Storage::disk('do_spaces')->putFileAs(
                        $folder,
                        $videoFile, // PASS THE FILE OBJECT DIRECTLY
                        $fileName,
                        'public'
                    );

                    if (!$path) {
                        throw new \Exception('Failed to upload video to DigitalOcean. Check credentials.');
                    }
                    
                    // Get the full URL
                    $data['video_url'] = Storage::disk('do_spaces')->url($path);
                    
                    // If we're updating, remove old video
                    if ($this->courseId && $this->video_url) {
                        // Attempt to delete old video based on its URL path
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
            // --- End Video Upload ---

            // Save/Update Course
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
        $this->validate(['featured_image' => 'image|max:1024']);
        $this->temporaryUrl = $this->featured_image->temporaryUrl();
    }

    public function updatedVideo()
    {
        $this->validate(['video' => 'file|mimetypes:video/mp4,video/quicktime|max:102400']);
        
        // Reset progress when new video is selected
        $this->uploadProgress = 0;
        $this->uploadError = null;
    }

    public function render()
    {
        return view('livewire.admin.course-form');
    }
}