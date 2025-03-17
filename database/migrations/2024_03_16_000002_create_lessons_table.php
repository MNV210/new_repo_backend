<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('file_url')->nullable();
            $table->string('type')->nullable();
            // $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}; 