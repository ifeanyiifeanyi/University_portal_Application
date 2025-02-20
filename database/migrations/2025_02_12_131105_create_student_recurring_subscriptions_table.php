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
        Schema::create('student_recurring_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('recurring_payment_plan_id')->constrained()->onDelete('cascade')->index('recurring_payment_plan_index');
            $table->decimal('amount_per_month', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->integer('number_of_payments')->nullable(); // n
            $table->string('payment_method')->nullable(); // n
            $table->decimal('balance', 10, 2)->nullable();
            $table->date('start_date');
            $table->boolean('is_active')->default(true);
            $table->json('payment_history')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_recurring_subscriptions');
    }
};
