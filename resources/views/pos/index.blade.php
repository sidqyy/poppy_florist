@extends('layouts.pos')

@section('content')
<div class="h-full w-full bg-gray-50 flex items-center justify-center p-6">
    <div class="max-w-6xl w-full">
        <div class="text-center mb-12">
            <h2 id="kioskExitTrigger" class="text-4xl font-extrabold text-gray-800 tracking-tight">Selamat Datang di Poppy Florist</h2>
            <p class="text-xl text-gray-500 mt-2">Silakan pilih jenis transaksi yang akan Anda lakukan saat ini</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Katalog Produk -->
            <a href="{{ route('pos.catalog') }}" class="group bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-florist-200 border-2 border-transparent hover:border-florist-500 transition-all touch-btn flex items-center gap-6">
                <div class="w-24 h-24 bg-florist-100 rounded-2xl flex items-center justify-center text-florist-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-gift text-5xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Katalog Produk</h3>
                    <p class="text-gray-500 text-lg leading-snug">Menjual Buket Jadi, Papan Bunga, atau Parcel yang sudah tersedia di toko.</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-2xl group-hover:text-florist-500 transition-colors"></i>
            </a>

            <!-- Custom Produk -->
            <a href="{{ route('pos.custom') }}" class="group bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-purple-200 border-2 border-transparent hover:border-purple-500 transition-all touch-btn flex items-center gap-6">
                <div class="w-24 h-24 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles text-5xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Custom Produk</h3>
                    <p class="text-gray-500 text-lg leading-snug">Merakit buket baru dari nol atau tambah produk custom pesanan pelanggan.</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-2xl group-hover:text-purple-500 transition-colors"></i>
            </a>

            <!-- Bunga Artificial -->
            @php
                $artificialCat = \App\Models\Category::where('slug', 'bunga-artificial')->first();
            @endphp
            <a href="{{ route('pos.catalog', ['category' => $artificialCat ? $artificialCat->id : '']) }}" class="group bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-blue-200 border-2 border-transparent hover:border-blue-500 transition-all touch-btn flex items-center gap-6">
                <div class="w-24 h-24 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-fan text-5xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Bunga Artificial</h3>
                    <p class="text-gray-500 text-lg leading-snug">Menjual buket atau produk bunga buatan/artificial yang sudah dirangkai jadi.</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-2xl group-hover:text-blue-500 transition-colors"></i>
            </a>

            <!-- Bunga & Bahan Eceran -->
            <a href="{{ route('pos.materials', ['type' => 'flower_fresh']) }}" class="group bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-orange-200 border-2 border-transparent hover:border-orange-500 transition-all touch-btn flex items-center gap-6">
                <div class="w-24 h-24 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-basket-shopping text-5xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Bunga & Bahan Eceran</h3>
                    <p class="text-gray-500 text-lg leading-snug">Menjual bunga batangan segar, boneka, aksesoris, serta packaging secara eceran.</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-2xl group-hover:text-orange-500 transition-colors"></i>
            </a>
        </div>
    </div>
</div>
@endsection
