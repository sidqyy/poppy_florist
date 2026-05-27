@extends('layouts.app')

@section('title', 'Owner Dashboard - Poppy Florist')
@section('page_title', 'Owner Dashboard')

@section('content')
<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}! 👑</h3>
    <p class="text-gray-500 text-sm mt-1">Ringkasan performa toko bunga Anda hari ini.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card-modern p-6">
        <p class="text-sm font-medium text-gray-500 mb-1">Pendapatan Hari Ini</p>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">Rp 2.450.000</h4>
        <p class="text-xs font-medium text-green-500"><i class="fa-solid fa-arrow-trend-up"></i> +12% dari kemarin</p>
    </div>
    
    <div class="card-modern p-6">
        <p class="text-sm font-medium text-gray-500 mb-1">Total Pesanan</p>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">18</h4>
        <p class="text-xs font-medium text-green-500"><i class="fa-solid fa-arrow-trend-up"></i> +3% dari kemarin</p>
    </div>
    
    <div class="card-modern p-6">
        <p class="text-sm font-medium text-gray-500 mb-1">Bucket Terjual</p>
        <h4 class="text-2xl font-bold text-gray-800 mb-2">24</h4>
        <p class="text-xs font-medium text-red-500"><i class="fa-solid fa-arrow-trend-down"></i> -2% dari kemarin</p>
    </div>

    <div class="card-modern p-6 bg-florist-500 text-white border-0">
        <p class="text-sm font-medium text-florist-100 mb-1">Total Pendapatan Bulan Ini</p>
        <h4 class="text-2xl font-bold text-white mb-2">Rp 45.800.000</h4>
        <p class="text-xs font-medium text-florist-100">November 2026</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card-modern p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Grafik Penjualan Mingguan</h4>
        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center border-2 border-gray-200 shadow-md">
            <p class="text-gray-400"><i class="fa-solid fa-chart-column mr-2"></i> Area Grafik Penjualan (Placeholder)</p>
        </div>
    </div>

    <div class="card-modern p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Produk Terlaris</h4>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                <div class="w-12 h-12 rounded-md bg-pink-100 flex items-center justify-center text-pink-500 text-xl font-bold">1</div>
                <div class="flex-1">
                    <h5 class="font-bold text-gray-800">Bucket Mawar Merah (Isi 10)</h5>
                    <p class="text-sm text-gray-500">Terjual: 45 unit</p>
                </div>
                <div class="font-bold text-florist-600">Rp 150k</div>
            </div>
            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                <div class="w-12 h-12 rounded-md bg-pink-100 flex items-center justify-center text-pink-500 text-xl font-bold">2</div>
                <div class="flex-1">
                    <h5 class="font-bold text-gray-800">Standing Flower Wedding</h5>
                    <p class="text-sm text-gray-500">Terjual: 12 unit</p>
                </div>
                <div class="font-bold text-florist-600">Rp 550k</div>
            </div>
            <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                <div class="w-12 h-12 rounded-md bg-pink-100 flex items-center justify-center text-pink-500 text-xl font-bold">3</div>
                <div class="flex-1">
                    <h5 class="font-bold text-gray-800">Bucket Bunga Matahari</h5>
                    <p class="text-sm text-gray-500">Terjual: 30 unit</p>
                </div>
                <div class="font-bold text-florist-600">Rp 120k</div>
            </div>
        </div>
    </div>
</div>
@endsection
