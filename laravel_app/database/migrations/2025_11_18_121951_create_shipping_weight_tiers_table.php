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
        Schema::create('shipping_weight_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id'); // Links to shipping_countries table
            $table->string('tier_name'); // e.g., "Up to 0.5kg", "0.5-1kg", "1-2kg"
            $table->decimal('max_weight_kg', 4, 2); // Maximum weight for this tier in kg
            $table->decimal('price', 10, 2);
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('shipping_countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_weight_tiers');
    }
};
