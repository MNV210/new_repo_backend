<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('lesson_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('action_type')->nullable(); // e.g., 'completed', 'started', etc.
            $table->text('action_details')->nullable(); // JSON or text for additional details
            $table->timestamp('action_time')->useCurrent(); // Time of the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_histories');
    }
};
