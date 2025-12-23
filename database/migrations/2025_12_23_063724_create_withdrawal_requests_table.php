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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id', 20)->nullable(false);
            $table->decimal('amount', 15, 2)->nullable(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('bank_account', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('processed_by', 20)->nullable();
            $table->timestamp('requested_at')->nullable(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            $table->index('user_id');
            $table->index('status');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
