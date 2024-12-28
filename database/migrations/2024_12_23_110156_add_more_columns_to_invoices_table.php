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
        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('payments', 'is_installment')) {
                $table->boolean('is_installment')->default(false);
            }
            // if (!Schema::hasColumn('payments', 'next_transaction_amount')) {
            //     $table->decimal('next_transaction_amount', 10, 2)->default(0);
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('is_installment');
            // $table->dropColumn('next_transaction_amount');
        });
    }
};
