<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('date_of_birth');
            $table->string('gender');
            $table->string('teaching_experience');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('teacher_type');
            $table->string('teacher_qualification');
            $table->string('teacher_title');
            $table->string('employment_id');
            $table->string('date_of_employment');
            $table->string('address');
            $table->string('nationality');
            $table->string('level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
