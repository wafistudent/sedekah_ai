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
        Schema::create('network', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('member_id', 20)->unique()->nullable(false);
            $table->string('sponsor_id', 20)->nullable();
            $table->string('upline_id', 20)->nullable();
            $table->boolean('is_marketing')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sponsor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('upline_id')->references('id')->on('users')->onDelete('set null');

            $table->index('member_id');
            $table->index('sponsor_id');
            $table->index('upline_id');
            $table->index('is_marketing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network');
    }
};
