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
    Schema::table('materials', function (Blueprint $table) {
        $table->decimal('price_stem', 12, 2)->default(0)->after('price'); // harga batangan
        $table->decimal('price_arrangement', 12, 2)->default(0)->after('price_stem'); // harga rangkaian
    });
}

public function down(): void
{
    Schema::table('materials', function (Blueprint $table) {
        $table->dropColumn(['price_stem', 'price_arrangement']);
    });
}
};
