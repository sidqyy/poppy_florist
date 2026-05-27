<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate existing data
        $products = DB::table('products')->whereNotNull('category_id')->get();
        foreach ($products as $product) {
            DB::table('category_product')->insert([
                'category_id' => $product->category_id,
                'product_id' => $product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop category_id column if possible, but safely drop foreign first.
        Schema::table('products', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['category_id']);
            }
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        });

        $relations = DB::table('category_product')->get();
        foreach ($relations as $rel) {
            DB::table('products')->where('id', $rel->product_id)->update(['category_id' => $rel->category_id]);
        }

        Schema::dropIfExists('category_product');
    }
};
