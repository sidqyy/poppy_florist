@extends('layouts.app')

@section('title', 'Kategori Occasion')
@section('page_title', 'Kategori Occasion')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Master Kategori (Occasion)</h3>
        <p class="text-gray-500 text-sm mt-1">Kelola kategori acara untuk produk buket.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
    </a>
</div>

<div class="card-modern overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="py-3 px-4 font-medium">Nama Kategori</th>
                <th class="py-3 px-4 font-medium">Deskripsi</th>
                <th class="py-3 px-4 font-medium">Jumlah Produk</th>
                <th class="py-3 px-4 font-medium text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 divide-y divide-gray-50">
            @forelse($categories as $category)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 font-bold text-gray-800">{{ $category->name }}</td>
                <td class="py-3 px-4">{{ $category->description ?? '-' }}</td>
                <td class="py-3 px-4"><span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">{{ $category->products_count }} Produk</span></td>
                <td class="py-3 px-4 text-right">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fa-solid fa-pen-to-square"></i></a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus kategori ini?')"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data kategori.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
