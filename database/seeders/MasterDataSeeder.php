<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori Acara
        $categories = [
            ['name' => 'Ulang Tahun', 'slug' => 'ulang-tahun', 'description' => 'Bucket untuk hadiah ulang tahun'],
            ['name' => 'Wisuda', 'slug' => 'wisuda', 'description' => 'Bucket untuk perayaan wisuda'],
            ['name' => 'Wedding / Anniversary', 'slug' => 'wedding', 'description' => 'Bunga papan atau bucket pernikahan'],
            ['name' => 'Duka Cita', 'slug' => 'duka-cita', 'description' => 'Bunga papan duka cita'],
        ];
        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }

        // Settings
        $settings = [
            ['key' => 'store_lat', 'value' => '-3.320556', 'description' => 'Garis Lintang Toko (Banjarmasin)'],
            ['key' => 'store_lng', 'value' => '114.587222', 'description' => 'Garis Bujur Toko (Banjarmasin)'],
            ['key' => 'store_address', 'value' => 'Jl. Anang Adenansi No.2, Kamboja, Banjarmasin', 'description' => 'Alamat Lengkap Toko'],
        ];
        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }

        // Materials
        $materials = [
            ['name' => 'Mawar Merah (Fresh)', 'type' => 'flower_fresh', 'unit' => 'tangkai', 'price' => 15000, 'stock' => 100],
            ['name' => 'Mawar Putih (Fresh)', 'type' => 'flower_fresh', 'unit' => 'tangkai', 'price' => 15000, 'stock' => 100],
            ['name' => 'Kertas Cellophane Pink', 'type' => 'wrapping', 'unit' => 'lembar', 'price' => 5000, 'stock' => 50],
            ['name' => 'Pita Satin Merah 2cm', 'type' => 'ribbon', 'unit' => 'meter', 'price' => 2000, 'stock' => 20],
            ['name' => 'Boneka Teddy Bear Mini', 'type' => 'doll', 'unit' => 'pcs', 'price' => 25000, 'stock' => 30],
            ['name' => 'Kartu Ucapan Standard', 'type' => 'greeting_card', 'unit' => 'pcs', 'price' => 3000, 'stock' => 200],
            ['name' => 'Jasa Rangkai Basic', 'type' => 'service', 'unit' => 'jasa', 'price' => 35000, 'stock' => 9999],
        ];
        foreach ($materials as $mat) {
            \App\Models\Material::create($mat);
        }

        // Dummy Product
        $product = \App\Models\Product::create([
            'name' => 'Bucket Mawar Merah Basic',
            'description' => 'Bucket simpel 5 mawar merah',
            'total_price' => 0 // Akan dihitung nanti
        ]);
        $product->categories()->attach(1);

        // Product Components
        $components = [
            ['material_id' => 1, 'qty' => 5], // 5 Mawar = 75k
            ['material_id' => 3, 'qty' => 3], // 3 Wrapping = 15k
            ['material_id' => 4, 'qty' => 2], // 2m Pita = 4k
            ['material_id' => 7, 'qty' => 1], // 1 Jasa = 35k
        ];

        $totalPrice = 0;
        foreach ($components as $comp) {
            $material = \App\Models\Material::find($comp['material_id']);
            $subtotal = $comp['qty'] * $material->price;
            $totalPrice += $subtotal;

            \App\Models\ProductComponent::create([
                'product_id' => $product->id,
                'material_id' => $material->id,
                'qty' => $comp['qty'],
                'unit_price' => $material->price,
                'subtotal' => $subtotal
            ]);
        }
        
        $product->update(['total_price' => $totalPrice]); // 129k
    }
}
