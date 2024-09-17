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
        Schema::table('time_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('time_tables', 'class_duration')) {
                $table->unsignedBigInteger('class_duration')->nullable()->after('end_time');
            }

            if (!Schema::hasColumn('time_tables', 'is_current')) {
                $table->boolean('is_current')->default(true)->after('class_duration');
            }

            if (!Schema::hasColumn('time_tables', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_current');
            }

            if (!Schema::hasColumn('time_tables', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('time_tables', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('updated_by');
            }

            if (!Schema::hasColumn('time_tables', 'archived_by')) {
                $table->unsignedBigInteger('archived_by')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('time_tables', 'class_date')) {
                $table->date('class_date')->nullable()->after('is_current');
            }

            if (!Schema::hasColumn('time_tables', 'status')) {
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'archived'])->default('draft');
            }

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('archived_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_tables', function (Blueprint $table) {
            //
        });
    }
};
