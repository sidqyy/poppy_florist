@extends('layouts.app')

@section('title', 'Master Data Bahan Baku')
@section('page_title', 'Master Data Bahan Baku')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Master Data Bahan Baku</h3>
        <p class="text-gray-500 text-sm mt-1">Kelola data bunga, wrapping, pita, dll.</p>
    </div>
    <a href="{{ route('admin.materials.create', ['type' => $type ?? 'flower_fresh']) }}" class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Bahan Baku
    </a>
</div>

<!-- Tabs for Types -->
<div class="flex overflow-x-auto gap-2 mb-6 pb-2 border-b border-gray-200">
    @php
        $types = [
            'flower_fresh' => 'Bunga Fresh',
            'flower_artificial' => 'Bunga Artificial',
            'wrapping' => 'Wrapping',
            'ribbon' => 'Pita',
            'doll' => 'Boneka',
            'greeting_card' => 'Kartu Ucapan',
            'accessory' => 'Aksesoris',
            'packaging' => 'Packaging',
            'service' => 'Jasa Rangkai'
        ];
    @endphp

    @foreach($types as $key => $label)
        <a href="{{ route('admin.materials.index', ['type' => $key]) }}" 
           class="px-4 py-2 rounded-t-lg font-medium whitespace-nowrap {{ $type == $key ? 'bg-florist-50 text-florist-600 border-b-2 border-florist-500' : 'text-gray-500 hover:text-florist-500 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="card-modern overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="py-3 px-4 font-medium">Nama Bahan</th>
                <th class="py-3 px-4 font-medium">Satuan</th>
                <th class="py-3 px-4 font-medium">Harga Dasar</th>
                <th class="py-3 px-4 font-medium">Harga Batangan</th>
                <th class="py-3 px-4 font-medium">Harga Rangkaian</th>
                <th class="py-3 px-4 font-medium">Stok</th>
                <th class="py-3 px-4 font-medium">Status</th>
                <th class="py-3 px-4 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 divide-y divide-gray-50">
            @forelse($materials as $material)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 font-medium text-gray-800">{{ $material->name }}</td>
                <td class="py-3 px-4 capitalize">{{ $material->unit }}</td>
                <td class="py-3 px-4 font-semibold text-florist-600">
                    {{ $material->formatted_price }}
                </td>
                <td class="py-3 px-4 font-semibold text-blue-600">
                    Rp {{ number_format($material->price_stem ?? 0, 0, ',', '.') }}
                </td>
                <td class="py-3 px-4 font-semibold text-purple-600">
                    Rp {{ number_format($material->price_arrangement ?? 0, 0, ',', '.') }}
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $material->stock > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $material->stock }}
                    </span>
                </td>
                <td class="py-3 px-4">
                    @if($material->is_active)
                        <span class="px-2 py-1 bg-green-50 text-green-600 rounded-full text-xs font-medium">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Nonaktif</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-right">
                    <a href="{{ route('admin.materials.edit', $material->id) }}" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fa-solid fa-pen-to-square"></i></a>
                    <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus bahan ini?')"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-8 text-center text-gray-400">Belum ada data bahan baku untuk kategori ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection