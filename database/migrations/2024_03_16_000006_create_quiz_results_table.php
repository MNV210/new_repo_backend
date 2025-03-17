<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('quiz_id');
            // $table->integer('score');
            $table->integer('total_questions');
            $table->integer('correct_answers');
            // $table->integer('time_spent')->nullable(); // in seconds
            $table->boolean('passed')->nullable();
            $table->json('answers_detail')->nullable(); // Store detailed answers
            // $table->timestamp('started_at')->nullable();
            // $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_results');
    }
}; 