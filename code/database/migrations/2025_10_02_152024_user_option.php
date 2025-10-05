<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tables:
     * - option
     * - user_option
     */
    public function up(): void
    {
        Schema::create('option', function (Blueprint $table) {
            $table->id('option_id');
            $table->string('option_name')->unique();
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('user_option', function (Blueprint $table) {
            $table->id('user_option_id');
            $table->foreignId('user_id') // Foreign key to user table
                ->constrained('users', 'user_id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('option_id') // Foreign key to option table
                ->constrained('option', 'option_id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_option');
        Schema::dropIfExists('option');
    }
};
