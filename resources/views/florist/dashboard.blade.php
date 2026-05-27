@extends('layouts.app')

@section('title', 'Dashboard Florist')
@section('page_title', 'Ringkasan Dapur Produksi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-500 text-sm">Fokus kerja hari ini: merangkai pesanan dan memastikan stok bunga aman.</p>
    <a href="{{ route('kitchen.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">
        <i class="fa-solid fa-kitchen-set mr-2"></i> Buka Dapur
    </a>
</div>

<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center text-red-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Antrian Urgent</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $urgentOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-list-ul"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Pesanan Pending</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $pendingOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-spinner fa-spin"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Sedang Dirangkai</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $processingOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-box-check"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Siap Kirim (Hari Ini)</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $readyOrders }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Action Required -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-bell text-florist-500 mr-2"></i> Segera Dikerjakan</h4>
        @if($urgentOrders > 0)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
            <p class="text-red-700 font-bold mb-1">Terdapat {{ $urgentOrders }} pesanan URGENT!</p>
            <p class="text-sm text-red-600 mb-3">Prioritaskan pesanan ini terlebih dahulu sebelum pesanan regular.</p>
            <a href="{{ route('kitchen.index') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold shadow hover:bg-red-700 transition">Lihat Pesanan Urgent</a>
        </div>
        @elseif($pendingOrders > 0)
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl mb-4">
            <p class="text-blue-700 font-bold mb-1">Ada {{ $pendingOrders }} pesanan menunggu.</p>
            <p class="text-sm text-blue-600 mb-3">Cek antrian dan mulai kerjakan pesanan sesuai jadwal pengiriman.</p>
            <a href="{{ route('kitchen.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow hover:bg-blue-700 transition">Buka Antrian</a>
        </div>
        @else
        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <i class="fa-solid fa-mug-hot text-gray-400 text-4xl mb-3"></i>
            <p class="font-bold text-gray-600">Antrian Kosong</p>
            <p class="text-sm text-gray-500">Anda bisa bersantai sejenak.</p>
        </div>
        @endif
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
        <h4 class="font-bold text-red-600 mb-4 border-b border-red-100 pb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Peringatan: Stok Menipis (< 10)</h4>
        
        <p class="text-sm text-gray-500 mb-4">Harap segera lapor ke admin untuk belanja material berikut agar produksi tidak terhambat.</p>

        @if($lowStockMaterials->count() > 0)
        <ul class="space-y-3">
            @foreach($lowStockMaterials as $mat)
            <li class="flex justify-between items-center p-3 bg-red-50 rounded-xl border border-red-100">
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $mat->name }}</p>
                    <p class="text-xs text-gray-500">{{ $mat->category->name ?? 'Tanpa Kategori' }}</p>
                </div>
                <span class="px-2 py-1 bg-red-500 text-white font-bold text-xs rounded-md">{{ $mat->stock }} {{ $mat->unit }}</span>
            </li>
            @endforeach
        </ul>
        @else
        <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
            <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
            <p class="text-sm text-green-700 font-medium">Stok material aman. Belum ada yang perlu dibeli.</p>
        </div>
        @endif
    </div>
</div>
@endsection
