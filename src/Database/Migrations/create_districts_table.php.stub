<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('turkey-geo.tables.districts', 'districts'), function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('city_id');
            $table->string('name', 100);
            $table->timestamps();

            // Indexes for faster searches and relationships
            $table->index('city_id');
            $table->index('name');

            // Foreign key constraint
            $table->foreign('city_id')
                ->references('id')
                ->on(config('turkey-geo.tables.cities', 'cities'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('turkey-geo.tables.districts', 'districts'));
    }
};
