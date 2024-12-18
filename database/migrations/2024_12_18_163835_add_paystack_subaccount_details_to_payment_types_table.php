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
            $table->string('paystack_subaccount_code')->nullable();
            $table->decimal('subaccount_percentage', 5, 2)->default(100.00); // Percentage of the payment that goes to subaccount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_types', function (Blueprint $table) {
            //
        });
    }
};
