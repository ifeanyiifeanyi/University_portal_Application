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
        Schema::table('payment_types', function (Blueprint $table) {
            $table->enum('payment_period', ['semester', 'session'])->default('semester');
            $table->date('due_date')->nullable();
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->enum('late_fee_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('grace_period_days')->default(0);
            $table->boolean('is_recurring')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn([
                'payment_period',
                'due_date',
                'late_fee_amount',
                'late_fee_type',
                'grace_period_days',
                'is_recurring'
            ]);
        });
    }
};
