@extends('layouts.app')

@section('title', 'Dashboard Florist')
@section('page_title', 'Ringkasan Dapur Produksi')

@section('content')
<!-- Custom Modern CSS for Florist Dashboard -->
<style>
    .premium-gradient-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
        position: relative;
        overflow: hidden;
        border: none;
    }
    .premium-gradient-card::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.15) 0%, rgba(236, 72, 153, 0) 70%);
        top: -100px;
        right: -50px;
        border-radius: 50%;
    }
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.8);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        border-color: rgba(236, 72, 153, 0.3);
    }
    .live-dot {
        position: relative;
    }
    .live-dot::before {
        content: '';
        position: absolute;
        left: -12px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        background-color: #22c55e;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: pulse-dot 1.5s infinite;
    }
    @keyframes pulse-dot {
        0% {
            transform: translateY(-50%) scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        70% {
            transform: translateY(-50%) scale(1);
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
        }
        100% {
            transform: translateY(-50%) scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }
</style>

<!-- Welcome & Action Banner -->
<div class="premium-gradient-card rounded-3xl p-6 md:p-8 text-white shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div class="space-y-2">
        @php
            $hour = date('H');
            $greeting = 'Selamat Pagi';
            if ($hour >= 11 && $hour < 15) {
                $greeting = 'Selamat Siang';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat Sore';
            } elseif ($hour >= 18 || $hour < 5) {
                $greeting = 'Selamat Malam';
            }
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 text-xs font-semibold tracking-wider uppercase backdrop-blur-sm">
            <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-xs"></i> Artistry & Creation
        </span>
        <h3 class="text-3xl font-extrabold tracking-tight">{{ $greeting }}, {{ auth()->user()->name ?? 'Florist' }}!</h3>
        <p class="text-indigo-200/90 text-sm font-medium">Hari ini adalah waktu yang sempurna untuk merangkai keindahan. Mari pastikan setiap pesanan rampung tepat waktu!</p>
    </div>
    <div class="shrink-0 w-full md:w-auto">
        <a href="{{ route('kitchen.index') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-pink-500/20 transition-all transform active:scale-95 group">
            <i class="fa-solid fa-kitchen-set mr-2 text-lg group-hover:rotate-12 transition-transform"></i> Buka Antrian Dapur
            <i class="fa-solid fa-arrow-right ml-2 text-sm opacity-70 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
</div>

<!-- Modern Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-red-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-bolt-lightning text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Antrian Urgent</p>
            <h3 class="text-2xl font-black text-gray-800 flex items-baseline">
                {{ $urgentOrders }}
                @if($urgentOrders > 0)
                <span class="ml-2 text-xs font-bold text-red-500 px-2 py-0.5 bg-red-100 rounded-full animate-bounce">Butuh Tindakan!</span>
                @endif
            </h3>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-folder-open text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Pesanan Pending</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $pendingOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-hourglass-half text-xl animate-spin" style="animation-duration: 6s;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Sedang Dirangkai</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $processingOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-green-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-circle-check text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Siap Kirim (Hari Ini)</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $readyOrders }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Live Antrian Dapur (7 Columns) -->
    <div class="lg:col-span-7 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-5 border-b border-gray-50 pb-3">
                <div>
                    <h4 class="font-extrabold text-gray-800 text-lg flex items-center">
                        <span class="live-dot pl-4 mr-2 font-black text-gray-800">Antrian Terdekat Dapur</span>
                    </h4>
                    <p class="text-xs text-gray-400 font-medium">Pesanan yang perlu dirangkai segera hari ini.</p>
                </div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full">Real-time</span>
            </div>

            @if($nextOrders->count() > 0)
            <div class="space-y-4">
                @foreach($nextOrders as $order)
                <div class="p-4 rounded-2xl border-2 {{ $order->is_urgent ? 'border-red-100 bg-red-50/30' : 'border-gray-100 bg-gray-50/20' }} hover:border-pink-200 transition duration-300 flex justify-between items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                            <span class="px-2 py-0.5 bg-white border border-gray-200 text-[10px] font-black text-gray-600 rounded-md tracking-wider">
                                {{ $order->order_number }}
                            </span>
                            @if($order->is_urgent)
                            <span class="px-2 py-0.5 bg-red-500 text-white text-[9px] font-black rounded-md animate-pulse uppercase tracking-wider">
                                Urgent 🔥
                            </span>
                            @endif
                            <span class="text-xs font-bold {{ $order->status == 'processing' ? 'text-amber-600 bg-amber-50' : 'text-gray-500 bg-gray-100' }} px-2 py-0.5 rounded-full">
                                {{ $order->status == 'processing' ? 'Sedang Dirangkai' : 'Menunggu' }}
                            </span>
                        </div>
                        <h5 class="font-bold text-gray-800 text-sm truncate">{{ $order->customer_name }}</h5>
                        
                        <p class="text-xs text-pink-600 font-bold mt-1 truncate">
                            <i class="fa-solid fa-gift mr-1"></i> 
                            {{ $order->product_name ?: ($order->items->first()->product_name ?? 'Custom Bouquet') }}
                        </p>
                    </div>
                    <div class="text-right shrink-0 flex flex-col items-end gap-2">
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Deadline Kirim</span>
                            <span class="text-xs font-black text-gray-700">
                                <i class="fa-regular fa-clock mr-1 text-gray-400"></i>
                                {{ $order->scheduled_at ? $order->scheduled_at->format('H:i') : 'Flexible' }}
                            </span>
                        </div>
                        <a href="{{ route('orders.show', $order->id) }}" class="px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 rounded-xl transition shadow-sm">
                            Kelola
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i class="fa-solid fa-mug-hot text-gray-300 text-2xl"></i>
                </div>
                <h5 class="font-bold text-gray-700 text-sm">Dapur Bersih & Rapi!</h5>
                <p class="text-xs text-gray-400 mt-1 px-4">Semua antrian pesanan online maupun offline saat ini sudah selesai dikerjakan.</p>
            </div>
            @endif
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-50 text-center">
            <a href="{{ route('kitchen.index') }}" class="text-xs font-black text-pink-600 hover:text-pink-700 uppercase tracking-widest flex items-center justify-center gap-1.5 group">
                Lihat Seluruh Antrian Dapur 
                <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
    </div>

    <!-- Peringatan Stok Menipis (5 Columns) -->
    <div class="lg:col-span-5 bg-white rounded-3xl shadow-sm border border-red-50 p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-5 border-b border-red-50 pb-3">
                <div>
                    <h4 class="font-extrabold text-red-600 text-lg flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2 text-red-500"></i> Stok Menipis (< 10)
                    </h4>
                    <p class="text-xs text-gray-400 font-medium">Bahan produksi kritis yang harus segera di-restock.</p>
                </div>
                <i class="fa-solid fa-circle-exclamation text-red-400 text-lg"></i>
            </div>

            @if($lowStockMaterials->count() > 0)
            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                @foreach($lowStockMaterials as $mat)
                <div class="flex justify-between items-center p-3 bg-red-50/40 rounded-2xl border border-red-100/50 hover:bg-red-50 transition duration-200">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 text-sm truncate">{{ $mat->name }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">
                            {{ $mat->type == 'flower_fresh' ? '🌸 Bunga Segar' : ($mat->type == 'wrapping' ? '🎁 Wrapping' : '🎗️ Aksesoris') }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 bg-red-500 text-white font-black text-xs rounded-xl shadow-sm shrink-0">
                        {{ $mat->stock }} {{ $mat->unit }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-12 text-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
                </div>
                <h5 class="font-bold text-green-800 text-sm">Gudang Aman!</h5>
                <p class="text-xs text-gray-400 mt-1 px-4">Semua bahan baku bunga segar dan aksesoris wrapping saat ini memiliki stok yang aman.</p>
            </div>
            @endif
        </div>

        <div class="mt-6 pt-4 border-t border-red-50 text-center">
            <p class="text-xs text-red-500 font-medium"><i class="fa-solid fa-info-circle mr-1"></i> Segera lapor Admin/Owner untuk pembelanjaan baru.</p>
        </div>
    </div>
</div>
@endsection
