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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_rentable')->default(false);
            $table->integer('rental_price_per_day')->nullable();
            $table->boolean('has_flexible_components')->default(false);
            $table->integer('max_flexible_components')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_rentable',
                'rental_price_per_day',
                'has_flexible_components',
                'max_flexible_components'
            ]);
        });
    }
};
