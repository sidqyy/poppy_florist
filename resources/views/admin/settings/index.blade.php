@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')

@section('content')
<div class="mb-6">
    <p class="text-gray-500 text-sm">Kelola pengaturan dasar aplikasi seperti tarif ongkos kirim dan koordinat toko.</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 font-medium">
    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-center mb-2">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i>
            <h3 class="font-bold text-red-800">Gagal Menyimpan Pengaturan</h3>
        </div>
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Ongkos Kirim -->
        <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-truck-fast text-florist-500 mr-2"></i> Pengaturan Ongkos Kirim</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarif per Kilometer (Rp)</label>
                    <input type="number" name="delivery_fee_per_km" value="{{ \App\Models\Setting::get('delivery_fee_per_km', 3000) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Ongkir (Rp)</label>
                    <input type="number" name="delivery_min_fee" value="{{ \App\Models\Setting::get('delivery_min_fee', 15000) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Jika perhitungan jarak × tarif < minimum ongkir, maka nilai ini yang ditagihkan.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Maksimal Pengiriman (KM)</label>
                    <input type="number" name="delivery_max_radius" value="{{ \App\Models\Setting::get('delivery_max_radius', 25) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Radius pengantaran maksimal. Melebihi batas ini sistem akan menolak pengiriman.</p>
                </div>
            </div>
        </div>

        <!-- Koordinat Toko -->
        <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-location-dot text-red-500 mr-2"></i> Koordinat Toko (Base GPS)</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="text" name="store_lat" value="{{ \App\Models\Setting::get('store_lat', '-6.200000') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="text" name="store_lng" value="{{ \App\Models\Setting::get('store_lng', '106.816666') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>
                
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <p class="text-sm text-blue-800 font-medium">Persiapan Integrasi Masa Depan 🚀</p>
                    <p class="text-xs text-blue-600 mt-1">Sistem ini sudah disiapkan untuk integrasi Google Maps Directions API ke depannya.</p>
                </div>
            </div>
        </div>
        
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit" class="px-6 py-3 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95">
            <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
