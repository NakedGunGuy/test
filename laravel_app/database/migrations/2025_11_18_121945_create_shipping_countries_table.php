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
        Schema::create('shipping_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_code')->unique(); // ISO 3166-1 alpha-2 (e.g., 'US', 'GB', 'DE')
            $table->string('country_name');
            $table->integer('estimated_days_min')->default(7);
            $table->integer('estimated_days_max')->default(14);
            $table->boolean('is_enabled')->default(true); // 0 = disabled, 1 = enabled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_countries');
    }
};
