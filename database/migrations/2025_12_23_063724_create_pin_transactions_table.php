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
        Schema::create('pin_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('member_id', 20)->nullable(false);
            $table->enum('type', ['purchase', 'transfer', 'reedem'])->nullable(false);
            $table->string('target_id', 20)->nullable();
            $table->integer('point')->nullable(false);
            $table->integer('before_point')->nullable(false);
            $table->integer('after_point')->nullable(false);
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target_id')->references('id')->on('users')->onDelete('set null');

            $table->index('member_id');
            $table->index('target_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pin_transactions');
    }
};
