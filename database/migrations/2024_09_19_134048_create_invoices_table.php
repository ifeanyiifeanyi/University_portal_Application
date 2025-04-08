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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('payment_type_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->string('level');
            $table->foreignId('academic_session_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->foreignId('payment_method_id')->constrained();
            $table->string('status')->default('pending')->comment('pending, paid, processing, partial, rejected, failed, cancelled, refunded');
            $table->string('invoice_number')->unique();
            $table->timestamps();
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
