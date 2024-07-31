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
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('state_of_origin')->nullable();
            $table->string('lga_of_origin')->nullable();
            $table->string('hometown')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('nationality')->default('Nigerian')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_address')->nullable();
            $table->string('jamb_registration_number')->nullable();
            $table->year('year_of_admission')->nullable();
            $table->enum('mode_of_entry', ['UTME', 'Direct Entry', 'Transfer']);
            $table->string('current_level')->nullable();
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
