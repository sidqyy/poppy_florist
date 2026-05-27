@extends('layouts.app')

@section('title', 'Katalog Produk Bucket')
@section('page_title', 'Katalog Produk Bucket')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Katalog Produk</h3>
        <p class="text-gray-500 text-sm mt-1">Kelola template produk bucket florist.</p>
    </div>
    <a href="{{ route('marketing.products.create', ['page' => request('page')]) }}" class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Produk
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Info Produk</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-100">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-image text-lg text-gray-300"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm md:text-base">{{ $product->name }}</h4>
                                <p class="text-xs text-gray-500 max-w-xs truncate mt-0.5" title="{{ $product->description }}">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex flex-wrap gap-1">
                            @forelse($product->categories as $cat)
                            <span class="px-2 py-0.5 bg-florist-50 text-florist-600 text-xs font-semibold rounded-md border border-florist-100">
                                {{ $cat->name }}
                            </span>
                            @empty
                            <span class="text-xs text-gray-400">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="py-4 px-6 font-bold text-gray-800 text-sm">
                        {{ $product->formatted_price }}
                    </td>
                    <td class="py-4 px-6">
                        @if($product->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('marketing.products.edit', ['product' => $product->id, 'page' => request('page')]) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors" title="Edit"><i class="fa-solid fa-pen text-xs"></i></a>
                            <form action="{{ route('marketing.products.destroy', $product->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <input type="hidden" name="page" value="{{ request('page') }}">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" onclick="return confirm('Hapus produk?')" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-400 bg-white">
                        <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>
                        <p class="text-sm">Belum ada data produk.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
