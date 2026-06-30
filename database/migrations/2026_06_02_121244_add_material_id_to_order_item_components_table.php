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
    Schema::table('order_item_components', function (Blueprint $table) {
        $table->foreignId('material_id')
            ->nullable()
            ->after('order_item_id')
            ->constrained('materials')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('order_item_components', function (Blueprint $table) {
        $table->dropForeign(['material_id']);
        $table->dropColumn('material_id');
    });
}
};
