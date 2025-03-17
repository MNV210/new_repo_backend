<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('learn_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('lesson_id');
            $table->integer('course_id');
            $table->text('progress')->nullable();
            // $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            // $table->integer('time_spent')->default(0); // in seconds
            // $table->timestamp('last_accessed_at')->nullable();
            // $table->json('notes')->nullable(); // User's notes for the lesson
            // $table->json('bookmarks')->nullable(); // Bookmarked timestamps in video lessons
            $table->timestamps();

            // Composite unique index to prevent duplicate progress records
            // $table->unique(['user_id', 'course_id', 'lesson_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('learn_progress');
    }
}; 