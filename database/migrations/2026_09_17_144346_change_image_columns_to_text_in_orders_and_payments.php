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
            $table->text('reference_image')->nullable()->change();
            $table->text('payment_proof')->nullable()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->text('proof_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference_image')->nullable()->change();
            $table->string('payment_proof')->nullable()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->change();
        });
    }
};
