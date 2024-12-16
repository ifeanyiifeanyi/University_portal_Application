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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_schedule_id')->nullable()->constrained();
            $table->boolean('is_installment')->default(false);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->decimal('total_penalty_amount', 10, 2)->default(0);
            $table->date('next_installment_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_schedule_id']);
            $table->dropColumn([
                'payment_schedule_id',
                'is_installment',
                'total_amount',
                'paid_amount',
                'remaining_amount',
                'total_penalty_amount',
                'next_installment_date'
            ]);
        });
    }
};
