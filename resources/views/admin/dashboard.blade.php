@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Ringkasan Bisnis Hari Ini')

@section('content')
<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Pendapatan Hari Ini</p>
            <h3 class="text-xl font-bold text-gray-800">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-shopping-bag"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Pesanan</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $todayOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Sedang Diproses</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $processingOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center text-red-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Pesanan Urgent</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $urgentOrders }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Kolom Kiri: Statistik & Kinerja -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Top Products -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-star text-yellow-500 mr-2"></i> 5 Produk Terlaris Bulan Ini</h4>
            @if($topProducts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-500 text-sm border-b border-gray-100">
                            <th class="pb-2 font-medium">Nama Produk</th>
                            <th class="pb-2 font-medium text-center">Terjual</th>
                            <th class="pb-2 font-medium text-right">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topProducts as $top)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 font-bold text-gray-700">{{ $top->product_name }}</td>
                            <td class="py-3 text-center"><span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">{{ $top->total_sales }}x</span></td>
                            <td class="py-3 text-right font-medium text-green-600">Rp {{ number_format($top->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-sm text-center py-4">Belum ada data penjualan bulan ini.</p>
            @endif
        </div>

        <!-- Marketing Performance -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-chart-pie text-purple-500 mr-2"></i> Performa Saluran Bulan Ini</h4>
            <div class="flex flex-col md:flex-row gap-6 items-center justify-around py-4">
                <div class="text-center">
                    <p class="text-gray-500 text-sm mb-1">Offline (Walk-in)</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthlyOfflineRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-blue-500 mt-1 font-bold">{{ $offlineOrders }} Order Hari Ini</p>
                </div>
                <div class="h-16 w-px bg-gray-200 hidden md:block"></div>
                <div class="text-center">
                    <p class="text-gray-500 text-sm mb-1">Online (Marketing)</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthlyOnlineRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-green-500 mt-1 font-bold">{{ $onlineOrders }} Order Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Peringatan & Notifikasi -->
    <div class="space-y-8">
        <!-- Low Stock Alert -->
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
            <h4 class="font-bold text-red-600 mb-4 border-b border-red-100 pb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Stok Menipis (< 10)</h4>
            
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
            @if(auth()->user()->role === 'admin')
            <div class="mt-4">
                <a href="{{ route('admin.materials.index') }}" class="text-sm font-bold text-red-500 hover:text-red-700">Kelola Inventaris &rarr;</a>
            </div>
            @endif
            @else
            <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                <p class="text-sm text-green-700 font-medium">Semua stok material dalam kondisi aman.</p>
            </div>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Jalan Pintas</h4>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('orders.index') }}" class="p-3 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                    <i class="fa-solid fa-receipt block text-xl text-florist-500 mb-1"></i>
                    <span class="text-xs font-bold text-gray-700">Daftar Pesanan</span>
                </a>
                <a href="{{ route('kitchen.index') }}" class="p-3 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                    <i class="fa-solid fa-kitchen-set block text-xl text-yellow-500 mb-1"></i>
                    <span class="text-xs font-bold text-gray-700">Dapur Florist</span>
                </a>
                
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.promos.index') }}" class="p-3 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                    <i class="fa-solid fa-tags block text-xl text-purple-500 mb-1"></i>
                    <span class="text-xs font-bold text-gray-700">Kelola Promo</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="p-3 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                    <i class="fa-solid fa-cog block text-xl text-gray-500 mb-1"></i>
                    <span class="text-xs font-bold text-gray-700">Pengaturan</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
