<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('quiz_id');
            $table->text('question_text');
            $table->text('question1');
            $table->text('question2');
            $table->text('question3');
            $table->text('question4')->nullable();
            $table->text('correct_answer');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}; 