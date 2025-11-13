<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class InstructorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'website',
        'twitter',
        'linkedin',
        'youtube',
        'profile_image',
        'experience_years',
        'education',
        'skills',
        'bio',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
