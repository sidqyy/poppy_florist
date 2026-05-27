@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Statistik & Laporan')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Laporan Komprehensif</h3>
        <p class="text-gray-500 text-sm mt-1">Ringkasan performa penjualan dan operasional toko bunga Anda.</p>
    </div>
    
    <!-- Filter Panel -->
    <div class="bg-white p-2 rounded-xl shadow-md border-2 border-gray-200">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-florist-200 focus:outline-none">
            </div>
            <span class="text-gray-400 text-sm">-</span>
            <div>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-florist-200 focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Saring
            </button>
            <button type="submit" name="action" value="print" formtarget="_blank" class="px-4 py-2 bg-gray-800 hover:bg-black text-white font-bold rounded-lg text-sm transition-colors shadow-sm ml-2">
                <i class="fa-solid fa-print mr-1"></i> Print / PDF
            </button>
            <button type="submit" name="action" value="export_csv" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-file-csv mr-1"></i> Excel (CSV)
            </button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card-modern p-6 border-l-4 border-l-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
                <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    
    <div class="card-modern p-6 border-l-4 border-l-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Pesanan</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ number_format($totalOrders) }} <span class="text-sm font-normal text-gray-500">transaksi</span></h4>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>
    
    <div class="card-modern p-6 border-l-4 border-l-florist-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan Ongkir</p>
                <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalDeliveryFee, 0, ',', '.') }}</h4>
            </div>
            <div class="w-10 h-10 rounded-full bg-florist-50 flex items-center justify-center text-florist-500">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
        </div>
    </div>
    
    <div class="card-modern p-6 border-l-4 border-l-red-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Diskon Diberikan</p>
                <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h4>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 card-modern p-6">
        <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tren Penjualan Harian</h4>
        <div class="relative h-72">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    
    <div class="card-modern p-6">
        <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Online vs Offline</h4>
        <div class="relative h-64 flex justify-center">
            <canvas id="sourceChart"></canvas>
        </div>
        <div class="mt-4 flex justify-center gap-6">
            <div class="text-center">
                <p class="text-xs text-gray-500 font-bold uppercase">Online</p>
                <p class="font-bold text-lg text-blue-500">{{ $onlineOrders }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 font-bold uppercase">Offline</p>
                <p class="font-bold text-lg text-florist-500">{{ $offlineOrders }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Products -->
    <div class="card-modern overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h4 class="font-bold text-gray-800"><i class="fa-solid fa-trophy text-yellow-500 mr-2"></i> 5 Produk Terlaris</h4>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-400 border-b border-gray-100 text-xs uppercase">
                <tr>
                    <th class="py-3 px-5">Produk</th>
                    <th class="py-3 px-5 text-center">Terjual</th>
                    <th class="py-3 px-5 text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-gray-600">
                @forelse($topProducts as $prod)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-5 font-medium">{{ $prod->product_name }}</td>
                    <td class="py-3 px-5 text-center"><span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-bold">{{ $prod->total_qty }}</span></td>
                    <td class="py-3 px-5 text-right text-gray-800 font-medium">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="py-6 text-center text-gray-400">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Materials -->
    <div class="card-modern overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h4 class="font-bold text-gray-800"><i class="fa-brands fa-pagelines text-green-500 mr-2"></i> Penggunaan Bunga Terbanyak</h4>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-400 border-b border-gray-100 text-xs uppercase">
                <tr>
                    <th class="py-3 px-5">Nama Material</th>
                    <th class="py-3 px-5 text-right">Total Terpakai (Tangkai)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-gray-600">
                @forelse($topMaterials as $mat)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-5 font-medium">{{ $mat->material_name }}</td>
                    <td class="py-3 px-5 text-right"><span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold">{{ $mat->total_used }}</span></td>
                </tr>
                @empty
                <tr><td colspan="2" class="py-6 text-center text-gray-400">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart (Tren Harian)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const labels = {!! json_encode($dailySales->pluck('date')) !!};
    const dataRevenue = {!! json_encode($dailySales->pluck('revenue')) !!};
    
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: dataRevenue,
                borderColor: '#ec4899', // florists-500
                backgroundColor: 'rgba(236, 72, 153, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#ec4899',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Doughnut Chart (Online vs Offline)
    const sourceCtx = document.getElementById('sourceChart').getContext('2d');
    new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                data: [{{ $onlineOrders }}, {{ $offlineOrders }}],
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '75%'
        }
    });
});
</script>
@endsection
