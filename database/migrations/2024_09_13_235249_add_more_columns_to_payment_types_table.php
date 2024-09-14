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
            $table->foreignId('academic_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropForeign(['academic_session_id']);
            $table->dropForeign(['semester_id']);
            $table->dropColumn(['academic_session_id', 'semester_id']);
        });
    }
};
