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
        Schema::table('departments', function (Blueprint $table) {

            if (!Schema::hasColumn('departments', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }

            if (!Schema::hasColumn('departments', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }



            // Repeat for other columns
            if (!Schema::hasColumn('departments', 'program_id')) {
                $table->foreignId('program_id')->nullable();
            }


            // Repeat for other columns
            if (!Schema::hasColumn('departments', 'department_head_id')) {
                $table->unsignedBigInteger('department_head_id')->nullable();
                $table->foreign('department_head_id')->references('id')->on('users')->onDelete('cascade');
            }


            if (!Schema::hasColumn('departments', 'chairperson_id')) {
                $table->unsignedBigInteger('chairperson_id')->nullable();
                $table->foreign('chairperson_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            //
        });
    }
};
