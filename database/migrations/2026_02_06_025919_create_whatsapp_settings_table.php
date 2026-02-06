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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('Setting key');
            $table->text('value')->comment('Setting value');
            $table->string('type', 20)->default('text')->comment('Value type (text, number, boolean, json)');
            $table->text('description')->nullable()->comment('Human-readable description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
