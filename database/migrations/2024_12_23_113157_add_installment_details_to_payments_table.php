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

            if (!Schema::hasColumn('payments', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('payments', 'current_transaction_amount')) {
                $table->decimal('current_transaction_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('payments', 'installment_status')) {
                $table->enum('installment_status', ['pending', 'partial', 'completed'])->nullable();
            }
            if (!Schema::hasColumn('payments', 'next_installment_date')) {
                $table->timestamp('next_installment_date')->nullable();
            }
            if (!Schema::hasColumn('payments', 'installment_config_id')) {
                $table->foreignId('installment_config_id')->nullable()->constrained('payment_installment_configs');
            }




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('remaining_amount');
            $table->dropColumn('current_transaction_amount');
            $table->dropColumn('installment_status');
            $table->dropColumn('next_installment_date');
            $table->dropColumn('installment_config_id');
        });
    }
};
