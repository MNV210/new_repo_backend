<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('teacher_id');
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->string('file_url')->nullable();
            $table->string('slug')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}; 