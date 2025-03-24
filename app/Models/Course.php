<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserRegisterCourse;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'thumbnail',
        'file_url',
        'slug',
        'level',
        'category_id'
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(UserRegisterCourse::class, 'user_register_course')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function learnProgress()
    {
        return $this->hasMany(LearnProgress::class);
    }
} 