<?php

namespace App\Livewire\Instructor\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\InstructorProfile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Instructor Profile')]
#[Layout('components.layouts.instructor')]
class ProfileForm extends Component
{
    use WithFileUploads;

    // Form fields
    public $specialization, $website, $twitter, $linkedin, $youtube;
    public $experience_years, $education, $skills, $bio, $certifications;
    public $profile_image, $existingImage;

    public function mount()
    {
        $profile = InstructorProfile::where('user_id', auth()->id())->first();

        if ($profile) {
            $this->fill($profile->toArray());
            $this->existingImage = $profile->profile_image;
            $this->skills = is_array($profile->skills) ? implode(', ', $profile->skills) : $profile->skills;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'specialization' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'youtube' => 'nullable|url',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
            'bio' => 'nullable|string',
            'certifications' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $profile = InstructorProfile::firstOrNew(['user_id' => auth()->id()]);

        // Handle image upload to DigitalOcean
        if ($this->profile_image) {
            if ($profile->profile_image) {
                Storage::disk('do_spaces')->delete(str_replace(Storage::disk('do_spaces')->url(''), '', $profile->profile_image));
            }

            $path = $this->profile_image->store('instructor-profiles', 'do_spaces');
            $validated['profile_image'] = Storage::disk('do_spaces')->url($path);
        } else {
            $validated['profile_image'] = $profile->profile_image;
        }

        // Convert comma-separated string to array
        $validated['skills'] = $this->skills
            ? array_map('trim', explode(',', $this->skills))
            : [];

        $profile->fill($validated);
        $profile->save();

        $this->existingImage = $profile->profile_image;
        session()->flash('success', 'Profile saved successfully!');
    }

    public function render()
    {
        return view('livewire.instructor.profile.profile-form');
    }
}
