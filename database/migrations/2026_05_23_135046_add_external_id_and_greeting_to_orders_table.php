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
            $table->string('external_id')->nullable()->after('id')->comment('ID Pesanan Eksternal (WA/IG/Web)');
            $table->text('greeting_card')->nullable()->after('notes')->comment('Kartu ucapan');
            $table->string('payment_proof')->nullable()->after('payment_status')->comment('Path bukti pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['external_id', 'greeting_card', 'payment_proof']);
        });
    }
};
