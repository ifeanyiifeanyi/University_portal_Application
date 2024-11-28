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
        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('ticket_questions')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained();
            $table->text('response');
            $table->boolean('is_ai_response')->default(false);
            $table->string('email_message_id')->unique(); // For email threading
            $table->string('in_reply_to')->nullable(); // Reference to question's message_id
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_responses');
    }
};
