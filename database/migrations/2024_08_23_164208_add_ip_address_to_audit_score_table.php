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
            if (!Schema::hasColumn('score_audits', 'ip_address')) {
                $table->ipAddress('ip_address')->nullable();
            }
            if (!Schema::hasColumn('score_audits', 'old_value')) {
                $table->json('old_value')->nullable();
            }
            if (!Schema::hasColumn('score_audits', 'new_value')) {
                $table->json('new_value')->nullable();
            }
            if (!Schema::hasColumn('score_audits', 'changed_fields')) {
                $table->json('changed_fields')->nullable();
            }
            // $table->ipAddress('ip_address')->nullable();
            // $table->json('old_value')->nullable();
            // $table->json('new_value')->nullable();
            // $table->json('changed_fields')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_audits', function (Blueprint $table) {
            // $table->dropColumn('ip_address');
            // $table->dropColumn('old_value');
            // $table->dropColumn('new_value');
            // $table->dropColumn('changed_fields');
            // $table->dropForeign(['student_score_id']);
            if (Schema::hasColumn('score_audits', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            if (Schema::hasColumn('score_audits', 'old_value')) {
                $table->dropColumn('old_value');
            }
            if (Schema::hasColumn('score_audits', 'new_value')) {
                $table->dropColumn('new_value');
            }
            if (Schema::hasColumn('score_audits', 'changed_fields')) {
                $table->dropColumn('changed_fields');
            }
        });
    }
};
