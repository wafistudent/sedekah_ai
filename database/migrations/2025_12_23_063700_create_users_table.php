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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 20)->primary()->comment('Username');
            $table->string('email', 100)->unique()->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('name', 100)->nullable(false);
            $table->string('phone', 20)->nullable();
            $table->string('dana_name', 100)->nullable(false);
            $table->string('dana_number', 20)->nullable(false);
            $table->integer('pin_point')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
