<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Topics;

class Chapters extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'chapter_title',
        'chapter_slug',
        'order_index',
        'user_id',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($chapter) {
            $chapter->chapter_slug = Str::slug($chapter->chapter_title);
        });
    }
    public function getRouteKeyName()
    {
        return 'chapter_slug';
    }

    public function course()
    {
        return $this->belongsTo(Course::class); 
    }

    public function topics()
    {
        return $this->hasMany(Topics::class); 
    }
}
