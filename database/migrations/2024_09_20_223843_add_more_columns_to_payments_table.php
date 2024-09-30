<?php

use Composer\Semver\Constraint\Constraint;
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
            $table->string('invoice_number')->nullable()->after('transaction_reference');
            $table->string('payment_proof')->nullable();
            $table->foreignId('admin_id')->constrained()->nullable();
            $table->text('admin_comment')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->foreign('invoice_number')->references('invoice_number')->on('invoices');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_number']);
            $table->dropColumn('invoice_number');
            $table->dropColumn('payment_proof');
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            $table->dropColumn('admin_comment');
            $table->dropColumn('is_manual');
            
        });
    }
};
