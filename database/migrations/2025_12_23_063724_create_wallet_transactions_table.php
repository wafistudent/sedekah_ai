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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id')->nullable(false);
            $table->enum('type', ['credit', 'debit'])->nullable(false);
            $table->decimal('amount', 15, 2)->nullable(false);
            $table->decimal('balance_before', 15, 2)->nullable(false);
            $table->decimal('balance_after', 15, 2)->nullable(false);
            $table->enum('reference_type', ['commission', 'withdrawal', 'registration_fee', 'adjustment'])->nullable(false);
            $table->uuid('reference_id')->nullable();
            $table->string('from_member_id', 20)->nullable();
            $table->integer('level')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('from_member_id')->references('id')->on('users')->onDelete('set null');

            $table->index('wallet_id');
            $table->index('type');
            $table->index('reference_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
