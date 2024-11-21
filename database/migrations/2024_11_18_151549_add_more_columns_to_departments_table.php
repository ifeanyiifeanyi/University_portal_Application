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

            $table->string('phone')->nullable()->after('name');
            $table->string('email')->nullable()->after('phone');

            $table->unsignedBigInteger('program_id')->nullable()->after('faculty_id');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');

            $table->unsignedBigInteger('department_head_id')->nullable();
            $table->foreign('department_head_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('chairperson_id')->nullable();
            $table->foreign('chairperson_id')->references('id')->on('users')->onDelete('cascade');

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
