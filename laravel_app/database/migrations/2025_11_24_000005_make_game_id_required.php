<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure all existing records have game_id
        $grandArchiveId = DB::table('games')->where('slug', 'grandarchive')->value('id');

        if ($grandArchiveId) {
            // Update any remaining NULL values
            DB::table('cards')->whereNull('game_id')->update(['game_id' => $grandArchiveId]);
            DB::table('sets')->whereNull('game_id')->update(['game_id' => $grandArchiveId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here - just ensure data remains consistent
    }
};