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
        Schema::create('marketing_pins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 8)->unique()->comment('Format: sedXXXX');
            
            // Admin who generated the PIN
            $table->string('admin_id', 20)->nullable(false);
            
            // Designated member (tracking only, not restriction)
            $table->string('designated_member_id', 20)->nullable();
            
            // Member who used this PIN for registration
            $table->string('redeemed_by_member_id', 20)->nullable();
            
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('designated_member_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('redeemed_by_member_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for performance
            $table->index('status');
            $table->index('admin_id');
            $table->index('designated_member_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_pins');
    }
};
