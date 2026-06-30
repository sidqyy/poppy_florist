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
    Schema::create('arrangement_services', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('min_item');
        $table->integer('max_item')->nullable();
        $table->decimal('price', 15, 2);
        $table->boolean('is_premium')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
};
