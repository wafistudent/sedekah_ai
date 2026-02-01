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
        Schema::create('materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'youtube']); // pdf or youtube
            $table->text('content'); // file path for PDF or YouTube URL
            $table->enum('access_type', ['all', 'marketing_only', 'non_marketing_only'])->default('all');
            $table->integer('order')->default(0); // for sorting
            $table->timestamps();
            
            $table->index('access_type');
            $table->index('type');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
