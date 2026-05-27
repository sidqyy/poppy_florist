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
            $table->boolean('is_urgent')->default(false)->after('status');
            $table->integer('estimated_time')->nullable()->after('is_urgent');
            $table->text('florist_notes')->nullable()->after('notes');
            $table->timestamp('started_at')->nullable()->after('florist_notes');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_urgent', 'estimated_time', 'florist_notes', 'started_at', 'completed_at']);
        });
    }
};
