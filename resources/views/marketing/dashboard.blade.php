@extends('layouts.app')

@section('title', 'Dashboard Marketing')
@section('page_title', 'Performa Kampanye & Online Order')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-500 text-sm">Pantau jumlah traffic pemesanan online dan capaian target omzet Anda.</p>
    <a href="{{ route('orders.online.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">
        <i class="fa-solid fa-plus mr-2"></i> Input Order Online
    </a>
</div>

<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-globe"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Lead Hari Ini</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $todayOnlineOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Menunggu Diproses</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $pendingOnlineOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Total Order (Bulan Ini)</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $monthlyOnlineOrders }}</h3>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 flex items-center">
        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-500 text-2xl mr-4 shrink-0">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium mb-1">Omzet Hari Ini</p>
            <h3 class="text-lg font-bold text-gray-800">Rp {{ number_format($todayOnlineRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Fokus Hari Ini -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-bullseye text-red-500 mr-2"></i> Fokus Hari Ini</h4>
        
        @if($pendingOnlineOrders > 0)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl mb-4">
            <p class="text-yellow-700 font-bold mb-1">Terdapat {{ $pendingOnlineOrders }} order online belum dikerjakan florist!</p>
            <p class="text-sm text-yellow-600 mb-3">Segera follow up ke bagian produksi (Dapur) agar tidak terjadi keterlambatan pengiriman ke pelanggan.</p>
            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-bold shadow hover:bg-yellow-700 transition">Lihat Pesanan</a>
        </div>
        @else
        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <i class="fa-solid fa-check-double text-green-400 text-4xl mb-3"></i>
            <p class="font-bold text-gray-600">Semua Order Terkendali</p>
            <p class="text-sm text-gray-500">Tidak ada order online yang terbengkalai. Kerja bagus!</p>
        </div>
        @endif
    </div>

    <!-- Quick Links Marketing -->
    <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Jalan Pintas</h4>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('custom.index') }}" class="p-4 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                <i class="fa-solid fa-calculator block text-2xl text-blue-500 mb-2"></i>
                <span class="text-sm font-bold text-gray-700">Kalkulator Custom</span>
            </a>
            <a href="{{ route('orders.index') }}" class="p-4 text-center bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border-2 border-gray-200 shadow-md">
                <i class="fa-solid fa-receipt block text-2xl text-florist-500 mb-2"></i>
                <span class="text-sm font-bold text-gray-700">Lacak Order Pelanggan</span>
            </a>
        </div>
    </div>
</div>
@endsection
