<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'course_id',
        'action_type',
        'action_details',
        'action_time',
    ];

    protected $casts = [
        'action_time' => 'datetime',
        'action_details' => 'array', // Assuming action_details is stored as JSON
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
}
