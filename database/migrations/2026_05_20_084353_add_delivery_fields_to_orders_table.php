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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_distance', 8, 2)->nullable()->after('delivery_address');
            $table->decimal('delivery_fee', 12, 2)->default(0)->after('delivery_distance');
            $table->string('delivery_lat')->nullable()->after('delivery_fee');
            $table->string('delivery_lng')->nullable()->after('delivery_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_distance', 'delivery_fee', 'delivery_lat', 'delivery_lng']);
        });
    }
};
