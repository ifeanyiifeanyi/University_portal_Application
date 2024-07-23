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
        Schema::create('course_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('semester_id');
            $table->integer('level');

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');

            // $table->unique(['course_id', 'department_id', 'semester_id', 'level']);
            $table->unique(['course_id', 'department_id', 'semester_id', 'level'], 'unique_course_assignment');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assignments');
    }
};
