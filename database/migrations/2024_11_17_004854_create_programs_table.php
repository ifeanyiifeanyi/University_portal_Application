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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('class_schedule_type', [
                'morning',
                'evening',
                'weekend',
                'flexible',
                'hybrid'
            ])->default('flexible');
            $table->enum('duration_type', ['years', 'semesters', 'months'])->default('years');
            $table->integer('duration_value')->default(4);
            $table->decimal('attendance_requirement', 5, 2)->default(75.00);
            $table->decimal('tuition_fee_multiplier', 8, 2)->default(1.00);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
