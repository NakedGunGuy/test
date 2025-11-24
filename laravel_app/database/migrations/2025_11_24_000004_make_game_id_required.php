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
        // Add index to game_id columns for better performance
        Schema::table('cards', function (Blueprint $table) {
            $table->index('game_id');
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex(['game_id']);
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->dropIndex(['game_id']);
        });
    }
};