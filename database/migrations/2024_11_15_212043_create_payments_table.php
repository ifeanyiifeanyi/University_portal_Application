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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('level');
            $table->string('transaction_reference');
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'partial',
                'rejected',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->string('invoice_number')->nullable();
            $table->foreignId('admin_id')->constrained()->nullable();
            $table->text('admin_comment')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
