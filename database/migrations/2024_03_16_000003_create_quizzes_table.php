<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            // $table->integer('passing_score')->default(70); // percentage
            // $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}; 