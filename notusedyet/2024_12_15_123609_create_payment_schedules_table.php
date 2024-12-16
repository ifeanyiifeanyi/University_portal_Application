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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_type_id')->constrained();
            $table->foreignId('academic_session_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->date('due_date');
            $table->integer('grace_period_days');
            $table->decimal('late_penalty_percentage', 5, 2);
            $table->decimal('minimum_first_installment_percentage', 5, 2);
            $table->integer('maximum_installments');
            $table->integer('installment_interval_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
