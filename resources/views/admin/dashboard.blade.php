@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Ringkasan Bisnis Hari Ini')

@section('content')
<style>
    .premium-admin-banner {
        background: linear-gradient(135deg, #0f172a 0%, #064e3b 100%);
        position: relative;
        overflow: hidden;
        border: none;
    }
    .premium-admin-banner::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0) 70%);
        top: -80px;
        right: -40px;
        border-radius: 50%;
    }
    .stat-card-admin {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.8);
    }
    .stat-card-admin:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        border-color: rgba(16, 185, 129, 0.3);
    }
</style>

<!-- Welcome Banner -->
<div class="premium-admin-banner rounded-3xl p-6 md:p-8 text-white shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div class="space-y-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold tracking-wider uppercase backdrop-blur-sm">
            <i class="fa-solid fa-chart-line mr-1.5 text-xs"></i> Enterprise Analytics
        </span>
        <h3 class="text-3xl font-extrabold tracking-tight">Selamat Datang di Poppy Florist Control Room!</h3>
        <p class="text-emerald-200/90 text-sm font-medium">Pantau seluruh kinerja pesanan, omzet harian, dan kesehatan stok logistik toko Anda secara real-time di sini.</p>
    </div>
    @if(auth()->user()->role === 'admin')
    <div class="shrink-0 w-full md:w-auto flex gap-3">
        <a href="{{ route('orders.index') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/20 text-white px-5 py-3 rounded-2xl font-bold backdrop-blur-sm transition-all transform active:scale-95 text-sm">
            <i class="fa-solid fa-receipt mr-2"></i> Kelola Order
        </a>
        <a href="{{ route('admin.settings.index') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-500/20 transition-all transform active:scale-95 text-sm">
            <i class="fa-solid fa-cog mr-2"></i> Pengaturan
        </a>
    </div>
    @endif
</div>

<!-- Modern Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5 mb-8">
    
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-wallet text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Pendapatan Hari Ini</p>
            <h3 class="text-sm font-black text-gray-800 truncate">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-shopping-bag text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Total Pesanan</p>
            <h3 class="text-xl font-black text-gray-800">{{ $todayOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-hourglass-half text-lg animate-spin" style="animation-duration: 8s;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Sedang Diproses</p>
            <h3 class="text-xl font-black text-gray-800">{{ $processingOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-500 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-bolt-lightning text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Pesanan Urgent</p>
            <h3 class="text-xl font-black text-gray-800 flex items-center gap-1.5">
                {{ $urgentOrders }}
                @if($urgentOrders > 0)
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block animate-ping"></span>
                @endif
            </h3>
        </div>
    </div>

    <!-- Stat Card 5 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-rotate text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Aktif Disewa</p>
            <h3 class="text-xl font-black text-gray-800">{{ $activeRentals }}</h3>
        </div>
    </div>

    <!-- Stat Card 6 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-admin flex items-center">
        <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500 text-xl mr-3 shrink-0 shadow-inner">
            <i class="fa-solid fa-calendar-day text-lg"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5 truncate">Kembali Hari Ini</p>
            <h3 class="text-xl font-black text-gray-800">{{ $rentalsDueToday }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Kolom Kiri (8 Columns) -->
    <div class="lg:col-span-8 space-y-8">
        
        <!-- Top Products -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-5 border-b border-gray-50 pb-3">
                <div>
                    <h4 class="font-extrabold text-gray-800 text-lg flex items-center">
                        <i class="fa-solid fa-fire-flame-curved text-amber-500 mr-2"></i> 5 Produk Terlaris Bulan Ini
                    </h4>
                    <p class="text-xs text-gray-400 font-medium">Berdasarkan total volume penjualan produk standar katalog.</p>
                </div>
            </div>
            
            @if($topProducts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3 font-extrabold">Nama Produk</th>
                            <th class="pb-3 font-extrabold text-center">Terjual</th>
                            <th class="pb-3 font-extrabold text-right">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topProducts as $top)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 font-bold text-gray-700 text-sm">{{ $top->product_name }}</td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-1 bg-pink-50 text-pink-600 rounded-full text-xs font-black">
                                    {{ $top->total_sales }}x
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-emerald-600 text-sm">Rp {{ number_format($top->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-8 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <p class="text-gray-400 text-sm">Belum ada data penjualan tercatat bulan ini.</p>
            </div>
            @endif
        </div>

        <!-- Performa Saluran & Distribusi Pembayaran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Marketing/Channel Performance -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div>
                    <h4 class="font-extrabold text-gray-800 text-base mb-4 border-b border-gray-50 pb-2">
                        <i class="fa-solid fa-chart-pie text-purple-500 mr-2"></i> Performa Saluran Bulan Ini
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex justify-between items-center">
                            <div>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Offline (Walk-in / POS)</span>
                                <h3 class="text-lg font-black text-gray-800 mt-0.5">Rp {{ number_format($monthlyOfflineRevenue, 0, ',', '.') }}</h3>
                            </div>
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100">{{ $offlineOrders }} Order</span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex justify-between items-center">
                            <div>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Online (WhatsApp / IG)</span>
                                <h3 class="text-lg font-black text-gray-800 mt-0.5">Rp {{ number_format($monthlyOnlineRevenue, 0, ',', '.') }}</h3>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">{{ $onlineOrders }} Order</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-extrabold text-gray-800 text-base mb-4 border-b border-gray-50 pb-2">
                    <i class="fa-solid fa-money-bill-transfer text-blue-500 mr-2"></i> Metode Pembayaran Terverifikasi
                </h4>
                <div class="space-y-3 max-h-[160px] overflow-y-auto pr-1">
                    @forelse($paymentMethods as $pay)
                    <div class="flex justify-between items-center p-2.5 bg-blue-50/20 rounded-xl border border-blue-100/30">
                        <div>
                            <span class="text-xs font-black text-blue-600 block uppercase tracking-wider">{{ $pay->payment_method }}</span>
                            <span class="text-xs text-gray-400 mt-0.5 block">{{ $pay->count }} Transaksi</span>
                        </div>
                        <span class="text-sm font-black text-gray-800">Rp {{ number_format($pay->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <div class="py-8 text-center text-gray-400 text-xs">Belum ada pembayaran diverifikasi bulan ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Bahan Baku Terpakai Hari Ini -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-extrabold text-gray-800 text-lg mb-4 border-b border-gray-50 pb-2 flex items-center">
                <i class="fa-solid fa-leaf text-emerald-500 mr-2"></i> Bahan Baku Terpakai Hari Ini
            </h4>
            @if($materialsUsedToday->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($materialsUsedToday as $mat)
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col justify-between gap-1 shadow-sm">
                    <span class="text-xs text-gray-500 font-bold truncate">{{ $mat->material_name }}</span>
                    <span class="text-sm font-black text-emerald-600 self-start px-2 py-0.5 bg-emerald-50 border border-emerald-100 rounded-lg">
                        {{ $mat->total_qty }} pcs
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-6 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <p class="text-gray-400 text-xs">Belum ada bahan baku yang terpakai untuk pesanan hari ini.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Kolom Kanan (4 Columns) -->
    <div class="lg:col-span-4 space-y-8">
        
        <!-- Peringatan Stok Kritis -->
        <div class="bg-white rounded-3xl shadow-sm border border-red-50 p-6 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4 border-b border-red-50 pb-3">
                    <h4 class="font-extrabold text-red-600 text-lg flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2 text-red-500 animate-pulse"></i> Stok Kritis (< 10)
                    </h4>
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-lg"></i>
                </div>
                
                @if($lowStockMaterials->count() > 0)
                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                    @foreach($lowStockMaterials as $mat)
                    <div class="flex justify-between items-center p-3 bg-red-50/40 rounded-2xl border border-red-100/50">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-sm truncate">{{ $mat->name }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">{{ $mat->category->name ?? 'Logistik' }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-red-500 text-white font-black text-xs rounded-lg shadow-sm shrink-0">
                            {{ $mat->stock }} {{ $mat->unit }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @if(auth()->user()->role === 'admin')
                <div class="mt-4">
                    <a href="{{ route('admin.materials.index') }}" class="text-xs font-black text-red-500 hover:text-red-700 uppercase tracking-widest flex items-center gap-1">
                        Kelola Inventaris <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                @endif
                @else
                <div class="py-8 text-center bg-green-50/30 rounded-2xl border border-dashed border-green-200">
                    <i class="fa-solid fa-circle-check text-green-500 text-3xl mb-2"></i>
                    <p class="text-xs text-green-800 font-bold">Stok Seluruh Material Aman</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Jalan Pintas Interaktif -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-extrabold text-gray-800 text-lg mb-4 border-b border-gray-50 pb-2">Jalan Pintas Panel</h4>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('orders.index') }}" class="p-4 text-center bg-gray-50 hover:bg-pink-50/50 border border-gray-100 hover:border-pink-200 rounded-2xl transition duration-300 group">
                    <i class="fa-solid fa-receipt block text-2xl text-pink-500 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-gray-700">Daftar Order</span>
                </a>
                <a href="{{ route('kitchen.index') }}" class="p-4 text-center bg-gray-50 hover:bg-yellow-50/50 border border-gray-100 hover:border-yellow-200 rounded-2xl transition duration-300 group">
                    <i class="fa-solid fa-kitchen-set block text-2xl text-yellow-500 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-gray-700">Dapur Florist</span>
                </a>
                
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.promos.index') }}" class="p-4 text-center bg-gray-50 hover:bg-purple-50/50 border border-gray-100 hover:border-purple-200 rounded-2xl transition duration-300 group">
                    <i class="fa-solid fa-tags block text-2xl text-purple-500 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-xs font-bold text-gray-700">Kelola Promo</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="p-4 text-center bg-gray-50 hover:bg-gray-100 border border-gray-100 hover:border-gray-300 rounded-2xl transition duration-300 group">
                    <i class="fa-solid fa-cog block text-2xl text-gray-500 mb-2 group-hover:rotate-45 transition-transform"></i>
                    <span class="text-xs font-bold text-gray-700">Pengaturan</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
