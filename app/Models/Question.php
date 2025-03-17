<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question1',
        'question2',
        'question3',
        'question4',
        'correct_answer'
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
} 