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
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('level');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('academic_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved','rejected'])->default('pending');
            // add this status too
            $table->unsignedBigInteger('semester_regid');
            $table->foreign('semester_regid')->references('id')->on('semester_registrations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
