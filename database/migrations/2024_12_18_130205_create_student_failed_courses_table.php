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
        Schema::create('student_failed_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->nullable();
            $table->foreignId('course_id')->constrained()->nullable();
            $table->foreignId('academic_session_id')->constrained()->nullable();
            $table->foreignId('semester_id')->constrained()->nullable();
            $table->foreignId('department_id')->constrained()->nullable();
            $table->foreignId('student_score_id')->constrained()->nullable();
            $table->boolean('is_retaken')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_failed_courses');
    }
};
