<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Topics extends Model
{
   use HasFactory;

    protected $fillable = [
        'topic_title',
        'topic_slug',
        'content',
        'video_url',
        'order_index',
        'is_completed',
        'chapter_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($topic) {
            $topic->topic_slug = Str::slug($topic->topic_title);
        });
    }
    public function chapter()
    {
        return $this->belongsTo(Chapter::class); 
    }
}
