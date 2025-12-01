<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chapters;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Topics extends Model
{
   use HasFactory;

    protected $fillable = [
        'chapters_id',
        'topic_title',
        'topic_slug',
        'content',
        'video_url',
        'order_index',
        'is_completed',
        'attachments'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($topic) {
            $topic->topic_slug = Str::slug($topic->topic_title);
        });
    }
    public function getRouteKeyName()
    {
        return 'topic_slug';
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
          
    }
    public function chapter()
    {
        return $this->belongsTo(Chapters::class, 'chapters_id');
    }
}
