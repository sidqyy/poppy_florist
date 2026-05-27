@extends('layouts.app')

@section('title', 'Manajemen Promo')
@section('page_title', 'Manajemen Promo & Voucher')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-500 text-sm">Kelola kode voucher dan diskon untuk pelanggan.</p>
    <a href="{{ route('admin.promos.create') }}" class="bg-florist-500 hover:bg-florist-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Promo
    </a>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm">
                <th class="p-4 font-medium uppercase tracking-wider">Kode</th>
                <th class="p-4 font-medium uppercase tracking-wider">Nama Promo</th>
                <th class="p-4 font-medium uppercase tracking-wider">Nilai Diskon</th>
                <th class="p-4 font-medium uppercase tracking-wider">Masa Berlaku</th>
                <th class="p-4 font-medium uppercase tracking-wider">Kuota Terpakai</th>
                <th class="p-4 font-medium uppercase tracking-wider">Status</th>
                <th class="p-4 font-medium uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($promos as $promo)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="p-4 font-bold text-florist-600">{{ $promo->code }}</td>
                <td class="p-4">
                    <div class="font-bold text-gray-800">{{ $promo->name }}</div>
                    <div class="text-xs text-gray-500">Min. Trx: Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}</div>
                </td>
                <td class="p-4 font-medium text-gray-700">
                    @if($promo->type == 'percentage')
                        {{ $promo->value }}%
                    @else
                        Rp {{ number_format($promo->value, 0, ',', '.') }}
                    @endif
                </td>
                <td class="p-4 text-sm text-gray-600">
                    @if($promo->start_date && $promo->end_date)
                        {{ $promo->start_date->format('d/m/y') }} - {{ $promo->end_date->format('d/m/y') }}
                    @else
                        Selamanya
                    @endif
                </td>
                <td class="p-4 text-sm font-medium text-gray-700">
                    {{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}
                </td>
                <td class="p-4">
                    @if($promo->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md">Nonaktif</span>
                    @endif
                </td>
                <td class="p-4 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.promos.edit', $promo->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-500">
                    Belum ada data promo.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
