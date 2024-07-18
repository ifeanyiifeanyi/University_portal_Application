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
        Schema::create('admins', function (Blueprint $table) {
            $table->id('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['superAdmin', 'admin', 'staff'])->default('admin');
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
