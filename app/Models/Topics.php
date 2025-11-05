<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Chapters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'is_completed'
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
    public function chapter()
    {
        return $this->belongsTo(Chapters::class); 
    }
}
