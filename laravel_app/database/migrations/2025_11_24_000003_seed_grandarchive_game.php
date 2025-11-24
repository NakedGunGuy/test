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
        // Create the GrandArchive game entry
        DB::table('games')->insert([
            'name' => 'GrandArchive',
            'slug' => 'grandarchive',
            'abbreviation' => 'GA',
            'description' => 'GrandArchive card game',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gameId = DB::getPdo()->lastInsertId();

        // Update existing cards to assign them to GrandArchive
        DB::table('cards')->update(['game_id' => $gameId]);

        // Update existing sets to assign them to GrandArchive
        DB::table('sets')->update(['game_id' => $gameId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('games')->where('slug', 'grandarchive')->delete();
        DB::table('cards')->whereNotNull('game_id')->update(['game_id' => null]);
        DB::table('sets')->whereNotNull('game_id')->update(['game_id' => null]);
    }
};