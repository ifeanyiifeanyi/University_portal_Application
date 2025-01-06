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
             // Drop the foreign key constraint
             $table->dropForeign('payments_admin_id_foreign');

             // Modify admin_id to be nullable
             $table->unsignedBigInteger('admin_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add back the foreign key constraint if needed to rollback
            $table->unsignedBigInteger('admin_id')->nullable(false)->change();
            $table->foreign('admin_id')->references('id')->on('admins');
        });
    }
};
