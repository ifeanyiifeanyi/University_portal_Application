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
        Schema::create('payment_installment_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_type_id')->constrained()->onDelete('cascade');
            $table->integer('number_of_installments')->default(2);
            $table->decimal('minimum_first_payment_percentage', 5, 2)->default(60.00);
            $table->integer('interval_days')->default(30);
            $table->decimal('late_fee_amount', 10, 2)->default(0.00);
            $table->enum('late_fee_type', ['fixed', 'percentage'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_installment_configs');
    }
};
