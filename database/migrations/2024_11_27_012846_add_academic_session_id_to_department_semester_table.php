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
        Schema::table('department_semester', function (Blueprint $table) {
            $table->foreignId('academic_session_id')->nullable()->constrained()
            ->onDelete('cascade')->after('semester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_semester', function (Blueprint $table) {
            //
        });
    }
};