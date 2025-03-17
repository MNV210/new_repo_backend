<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_register_course', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('course_id');
            // $table->timestamp('enrolled_at');
            // $table->timestamp('last_accessed_at')->nullable();
            // $table->integer('progress_percentage')->default(0);
            // $table->boolean('is_completed')->default(false);
            // $table->timestamp('completed_at')->nullable();
            // $table->json('completed_lessons')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_register_course');
    }
}; 