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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->nullable()->comment('Foreign key to whatsapp_templates(id)');
            $table->string('recipient_phone', 20)->comment('Phone number (format: 6281234567890)');
            $table->string('recipient_name', 255)->nullable()->comment('Recipient name');
            $table->text('message_content')->comment('Final message sent (variables already replaced)');
            $table->enum('status', ['pending', 'queued', 'sent', 'failed'])->default('pending')->comment('Message status');
            $table->text('error_message')->nullable()->comment('Error detail if failed');
            $table->integer('retry_count')->default(0)->comment('Number of retry attempts');
            $table->integer('max_retry')->default(3)->comment('Maximum retry attempts');
            $table->timestamp('sent_at')->nullable()->comment('When successfully sent');
            $table->timestamp('scheduled_at')->nullable()->comment('For future scheduled messages');
            $table->json('metadata')->nullable()->comment('Additional context data (user_id, event_type, etc)');
            $table->boolean('is_manual_resend')->default(false)->comment('Flag for manual admin resend');
            $table->timestamps();
            
            // Indexes
            $table->index('template_id');
            $table->index('recipient_phone');
            $table->index('status');
            $table->index('created_at');
            $table->index('sent_at');
            
            // Foreign keys
            $table->foreign('template_id')->references('id')->on('whatsapp_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
