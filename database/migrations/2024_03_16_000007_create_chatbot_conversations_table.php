<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('type')->nullable();   
            $table->text('message');
            $table->text('course_id');
            $table->text('lesson_id')->nullable();
            // $table->string('context')->nullable(); // To store conversation context
            // $table->json('metadata')->nullable(); // Additional data about the conversation
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_conversations');
    }
}; 