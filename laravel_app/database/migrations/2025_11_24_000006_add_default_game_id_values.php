<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add a default GrandArchive game if it doesn't exist
        $game = DB::table('games')->where('slug', 'grandarchive')->first();
        
        if (!$game) {
            DB::table('games')->insert([
                'name' => 'GrandArchive',
                'slug' => 'grandarchive',
                'abbreviation' => 'GA',
                'description' => 'GrandArchive card game',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $gameId = DB::getPdo()->lastInsertId();
        } else {
            $gameId = $game->id;
        }

        // Update all existing cards and sets with the game ID
        DB::table('cards')->whereNull('game_id')->update(['game_id' => $gameId]);
        DB::table('sets')->whereNull('game_id')->update(['game_id' => $gameId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset game_id to null during rollback
        DB::table('games')->where('slug', 'grandarchive')->delete();
    }
};