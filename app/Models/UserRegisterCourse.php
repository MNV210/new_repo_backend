<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegisterCourse extends Model
{
    use HasFactory;

    protected $table = 'user_register_course';

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    // Relationships
    public function user()  
    {
        return $this->belongsTo(User::class);
    }

    public function course()    
    {
        return $this->belongsTo(Course::class);
    }
}
