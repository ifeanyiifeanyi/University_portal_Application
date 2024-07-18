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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('matric_number')->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('state_of_origin');
            $table->string('lga_of_origin');
            $table->string('hometown');
            $table->string('residential_address');
            $table->string('permanent_address');
            $table->string('nationality')->default('Nigerian');
            $table->string('marital_status');
            $table->string('religion');
            $table->string('blood_group');
            $table->string('genotype');
            $table->string('next_of_kin_name');
            $table->string('next_of_kin_relationship');
            $table->string('next_of_kin_phone');
            $table->string('next_of_kin_address');
            $table->string('jamb_registration_number')->nullable();
            $table->year('year_of_admission');
            $table->enum('mode_of_entry', ['UTME', 'Direct Entry', 'Transfer']);
            $table->string('current_level');
            $table->string('cgpa')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
