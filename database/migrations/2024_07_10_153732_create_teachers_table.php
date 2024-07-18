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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('teaching_experience')->nullable()->comment("eg. years of experience");
            $table->string('teacher_type')->nullable()->comment('eg. fulltime, part time, auxillary teacher');
            $table->string('teacher_qualification')->nullable()->comment('eg. master of computer science, PHD Software Engineering');
            $table->string('teacher_title')->nullable()->comment("eg. mr. mrs. Doctor. prof.");
            $table->string('office_hours')->nullable();
            $table->string('office_address')->nullable();
            $table->text('biography')->nullable();
            $table->json('certifications')->nullable();
            $table->json('publications')->nullable();
            $table->string('number_of_awards')->nullable();
            $table->string('employment_id')->nullable();
            $table->string('date_of_employment')->nullable();
            $table->string('address')->nullable();
            $table->string('nationality')->nullable();
            $table->string('level')->nullable()->comment("eg. senior lecturer, junior lecturer, technician etc ...");
            $table->softDeletes();
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
