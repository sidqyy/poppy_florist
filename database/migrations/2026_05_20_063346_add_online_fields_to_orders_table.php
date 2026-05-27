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
            $table->string('reference_image')->nullable()->after('notes');
            $table->decimal('budget', 12, 2)->default(0)->after('reference_image');
            $table->enum('source', ['offline', 'online'])->default('offline')->after('budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reference_image', 'budget', 'source']);
        });
    }
};
