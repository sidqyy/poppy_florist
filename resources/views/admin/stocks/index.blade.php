@extends('layouts.app')

@section('title', 'Mutasi & Laporan Stok')
@section('page_title', 'Mutasi & Laporan Stok')

@section('content')

@if(count($lowStocks) > 0 || count($expiringFlowers) > 0)
<div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    @if(count($lowStocks) > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm">
        <h4 class="text-red-700 font-bold text-lg mb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Peringatan Stok Menipis!</h4>
        <ul class="space-y-1 text-sm text-red-600">
            @foreach($lowStocks as $mat)
                <li>• {{ $mat->name }} (Sisa: <b>{{ $mat->stock }}</b> {{ $mat->unit }}) - Min: {{ $mat->min_stock }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($expiringFlowers) > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 shadow-sm">
        <h4 class="text-yellow-700 font-bold text-lg mb-2"><i class="fa-solid fa-leaf mr-2 text-yellow-500"></i> Bunga Hampir Layu!</h4>
        <ul class="space-y-1 text-sm text-yellow-600">
            @foreach($expiringFlowers as $mut)
                <li>• {{ $mut->material->name }} (In: {{ $mut->qty }}) - <b>Estimasi Layu: {{ $mut->expires_at->diffForHumans() }}</b></li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Riwayat Mutasi Stok</h3>
        <p class="text-gray-500 text-sm mt-1">Lacak semua barang masuk dan keluar.</p>
    </div>
    <a href="{{ route('admin.stocks.create') }}" class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm">
        <i class="fa-solid fa-plus mr-2"></i> Input Mutasi Manual
    </a>
</div>

<div class="card-modern overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
            <tr>
                <th class="py-3 px-4 font-medium">Waktu</th>
                <th class="py-3 px-4 font-medium">Bahan Baku</th>
                <th class="py-3 px-4 font-medium">Tipe</th>
                <th class="py-3 px-4 font-medium">Jumlah</th>
                <th class="py-3 px-4 font-medium">Keterangan</th>
                <th class="py-3 px-4 font-medium">Oleh</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 divide-y divide-gray-50">
            @forelse($mutations as $mut)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-3 px-4 font-medium text-gray-800">{{ $mut->material->name ?? 'Dihapus' }}</td>
                <td class="py-3 px-4">
                    @if($mut->type == 'in')
                        <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold">MASUK</span>
                    @else
                        <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-bold">KELUAR</span>
                    @endif
                </td>
                <td class="py-3 px-4 font-bold {{ $mut->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $mut->type == 'in' ? '+' : '-' }}{{ $mut->qty }}
                </td>
                <td class="py-3 px-4 text-xs italic">{{ $mut->notes ?? '-' }}</td>
                <td class="py-3 px-4">{{ $mut->user->name ?? 'System' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada riwayat mutasi stok.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100 bg-gray-50">
        {{ $mutations->links() }}
    </div>
</div>
@endsection
