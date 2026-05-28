@extends('layouts.app')

@section('title', 'Dashboard Marketing')
@section('page_title', 'Performa Kampanye & Online Order')

@section('content')
<style>
    .premium-marketing-banner {
        background: linear-gradient(135deg, #3b0764 0%, #db2777 100%);
        position: relative;
        overflow: hidden;
        border: none;
    }
    .premium-marketing-banner::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(219, 39, 119, 0.15) 0%, rgba(219, 39, 119, 0) 70%);
        top: -80px;
        right: -40px;
        border-radius: 50%;
    }
    .stat-card-marketing {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.8);
    }
    .stat-card-marketing:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        border-color: rgba(219, 39, 119, 0.3);
    }
</style>

<!-- Welcome & Actions Banner -->
<div class="premium-marketing-banner rounded-3xl p-6 md:p-8 text-white shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div class="space-y-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 text-xs font-semibold tracking-wider uppercase backdrop-blur-sm">
            <i class="fa-solid fa-bullhorn mr-1.5 text-xs"></i> Outreach & Sales
        </span>
        <h3 class="text-3xl font-extrabold tracking-tight">Selamat Datang, Tim Kreatif Marketing!</h3>
        <p class="text-pink-100/90 text-sm font-medium">Kembangkan pasar Poppy Florist, layani obrolan pelanggan dengan antusias, dan input pesanan online secara terorganisir.</p>
    </div>
    <div class="shrink-0 w-full md:w-auto">
        <a href="{{ route('orders.online.create') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-purple-950 hover:bg-pink-50 px-6 py-3.5 rounded-2xl font-bold shadow-lg transition-all transform active:scale-95 group">
            <i class="fa-solid fa-plus mr-2 text-lg text-pink-600 group-hover:scale-110 transition-transform"></i> Input Order Online
        </a>
    </div>
</div>

<!-- Modern Overview Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-marketing flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-globe text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Total Lead Hari Ini</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $todayOnlineOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-marketing flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-hourglass-half text-xl animate-pulse"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Menunggu Diproses</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $pendingOnlineOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-marketing flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-calendar-check text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Total Order (Bulan Ini)</p>
            <h3 class="text-2xl font-black text-gray-800">{{ $monthlyOnlineOrders }}</h3>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="bg-white rounded-2xl p-5 shadow-sm stat-card-marketing flex items-center">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 text-2xl mr-4 shrink-0 shadow-inner">
            <i class="fa-solid fa-wallet text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Omzet Hari Ini</p>
            <h3 class="text-lg font-black text-emerald-600">Rp {{ number_format($todayOnlineRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Fokus Hari Ini / Focus Board -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <h4 class="font-extrabold text-gray-800 text-lg mb-4 border-b border-gray-50 pb-2">
                <i class="fa-solid fa-bullseye text-red-500 mr-2"></i> Fokus Hari Ini
            </h4>
            
            @if($pendingOnlineOrders > 0)
            <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-2xl">
                <p class="text-amber-800 font-bold mb-1.5 flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-500"></i> Ada {{ $pendingOnlineOrders }} order online menunggu dirangkai florist!
                </p>
                <p class="text-xs text-amber-700/90 mb-4 leading-relaxed">Pantau terus status pengerjaan pesanan online ini agar tim Dapur/Florist tidak terlambat memproses dan mengirimkan bunga ke customer.</p>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Lacak Status Pesanan
                </a>
            </div>
            @else
            <div class="py-8 text-center bg-green-50/20 rounded-2xl border border-dashed border-green-200">
                <i class="fa-solid fa-circle-check text-green-500 text-3xl mb-2"></i>
                <p class="text-sm font-bold text-green-800">Kerja Hebat! Semua Order Terkendali</p>
                <p class="text-xs text-gray-400 mt-1">Tidak ada pesanan online yang terbengkalai atau terlambat hari ini.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Jalan Pintas Interaktif -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-extrabold text-gray-800 text-lg mb-4 border-b border-gray-50 pb-2">Jalan Pintas Panel</h4>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('custom.index') }}" class="p-5 text-center bg-gray-50 hover:bg-blue-50/50 border border-gray-100 hover:border-blue-200 rounded-2xl transition duration-300 group">
                <i class="fa-solid fa-calculator block text-3xl text-blue-500 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold text-gray-700 block">Kalkulator Custom</span>
                <span class="text-[10px] text-gray-400 mt-1 block">Rakit Bouquet Bebas</span>
            </a>
            <a href="{{ route('orders.index') }}" class="p-5 text-center bg-gray-50 hover:bg-pink-50/50 border border-gray-100 hover:border-pink-200 rounded-2xl transition duration-300 group">
                <i class="fa-solid fa-receipt block text-3xl text-pink-500 mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-bold text-gray-700 block">Lacak Order</span>
                <span class="text-[10px] text-gray-400 mt-1 block">Pantau Pesanan WA/IG</span>
            </a>
        </div>
    </div>
</div>
@endsection
