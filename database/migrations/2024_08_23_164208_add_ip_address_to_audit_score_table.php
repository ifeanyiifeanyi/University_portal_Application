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
        Schema::table('score_audits', function (Blueprint $table) {
            $table->ipAddress('ip_address')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->json('changed_fields')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_audits', function (Blueprint $table) {
            $table->dropColumn('ip_address');
            $table->dropColumn('old_value');
            $table->dropColumn('new_value');
            $table->dropColumn('changed_fields');
            $table->dropForeign(['student_score_id']);


        });
    }
};
