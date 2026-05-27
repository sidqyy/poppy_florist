<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'store_lat', 'value' => '-6.200000', 'description' => 'Latitude Toko'],
            ['key' => 'store_lng', 'value' => '106.816666', 'description' => 'Longitude Toko'],
            ['key' => 'delivery_fee_per_km', 'value' => '3000', 'description' => 'Tarif Ongkir per Kilometer (Rp)'],
            ['key' => 'delivery_min_fee', 'value' => '15000', 'description' => 'Minimum Ongkir (Rp)'],
            ['key' => 'delivery_max_radius', 'value' => '25', 'description' => 'Batas Maksimal Pengiriman (KM)'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'description' => $setting['description']]
            );
        }
    }
}
