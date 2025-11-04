<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Chapter; 


class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            $course->slug = Str::slug($course->title);
        });
    }

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    } 

    public function chapters()
    {
        return $this->hasMany(Chapter::class); 
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    } 
}
