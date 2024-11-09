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
        Schema::table('users', function (Blueprint $table) {
            $table->string('recovery_link')->nullable();
            $table->timestamp('recovery_link_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('recovery_code');
            $table->dropColumn('recovery_code_expires_at');
            $table->dropColumn('recovery_link');
            $table->dropColumn('recovery_link_expires_at');
        });
    }
};
