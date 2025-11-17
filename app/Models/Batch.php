<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

class Batch extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'course_id',      
        'batch_name',  
        'start_date',    
        'end_date',       
        'total_seats',    
        'available_seats' 
    ];

    // Automatic date casting
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    
    // Relationship: Batch belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // // Relationship: Many-to-Many with Users through course_student table
    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'course_student', 'batch_id', 'user_id')
    //                 ->withPivot('course_id', 'is_subs')
    //                 ->join('courses', 'courses.id', '=', 'course_student.course_id')
    //                 ->select('users.*', 'courses.course_type')
    //                 ->withTimestamps();
    // }
}