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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('Unique identifier (e.g., welcome_new_member)');
            $table->string('name', 255)->comment('Display name (e.g., Selamat Datang Member Baru)');
            $table->enum('category', ['member', 'commission', 'withdrawal', 'admin', 'general'])->comment('Template category');
            $table->string('subject', 255)->nullable()->comment('Subject/title');
            $table->text('content')->comment('Template content with {{variables}}');
            $table->json('variables')->nullable()->comment('Array of available variables');
            $table->boolean('is_active')->default(true)->comment('Is template active');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Foreign key to users(id)');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category');
            $table->index('is_active');
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
