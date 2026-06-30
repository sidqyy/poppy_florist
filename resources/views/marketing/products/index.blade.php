@extends('layouts.app')

@section('title', 'Katalog Produk Bucket')
@section('page_title', 'Katalog Produk Bucket')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Katalog Produk</h3>
        <p class="text-gray-500 text-sm mt-1">Kelola template produk bucket florist.</p>
    </div>

    <a href="{{ route('marketing.products.create', ['page' => request('page')]) }}"
       class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm w-max">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Produk
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form action="{{ route('marketing.products.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama produk, deskripsi, kategori, ukuran, atau varian..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg shadow-sm">
            Cari
        </button>

        @if(request('search'))
            <a href="{{ route('marketing.products.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-lg text-center">
                Reset
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Info Produk</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Varian</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    @php
                        $sizeCount = $product->sizes ? $product->sizes->count() : 0;
                        $variantCount = 0;
                        $variantImages = collect();
                        $variantPrices = collect();

                        $previewImage = $product->image;

                        if ($product->sizes) {
                            foreach ($product->sizes as $size) {
                                if ($size->variants) {
                                    foreach ($size->variants as $variant) {
                                        $variantCount++;

                                        if (!empty($variant->price)) {
                                            $variantPrices->push($variant->price);
                                        }

                                        if (!empty($variant->image)) {
                                            $variantImages->push($variant->image);

                                            if (!$previewImage) {
                                                $previewImage = $variant->image;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($previewImage === $product->image && $variantImages->count() > 0) {
                            $previewImage = $variantImages->first();
                        }

                        $minVariantPrice = $variantPrices->count() > 0 ? $variantPrices->min() : null;
                        $maxVariantPrice = $variantPrices->count() > 0 ? $variantPrices->max() : null;
                    @endphp

                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-100">
                                    @if($previewImage)
                                        <img src="{{ asset('storage/'.$previewImage) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-lg text-gray-300"></i>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm md:text-base">
                                        {{ $product->name }}
                                    </h4>

                                    <p class="text-xs text-gray-500 max-w-xs truncate mt-0.5"
                                       title="{{ $product->description }}">
                                        {{ $product->description ?? 'Tidak ada deskripsi' }}
                                    </p>

                                    @if($sizeCount > 0)
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                            <i class="fa-solid fa-layer-group mr-1"></i>
                                            Produk Varian
                                        </span>
                                    @endif
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
                            @if($variantCount > 0 && $variantPrices->count() > 0)
                                @if($minVariantPrice == $maxVariantPrice)
                                    Rp {{ number_format($minVariantPrice, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($minVariantPrice, 0, ',', '.') }}
                                    <span class="text-gray-400">-</span>
                                    Rp {{ number_format($maxVariantPrice, 0, ',', '.') }}
                                @endif

                                <div class="text-[11px] text-blue-500 font-medium mt-1">
                                    Harga dari varian
                                </div>
                            @else
                                {{ $product->formatted_price }}
                            @endif
                        </td>

                        <td class="py-4 px-6">
                            @if($sizeCount > 0)
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $sizeCount }} Ukuran
                                        </span>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                            {{ $variantCount }} Varian
                                        </span>
                                    </div>

                                    @if($variantImages->count() > 0)
                                        <div class="flex -space-x-2">
                                            @foreach($variantImages->take(4) as $image)
                                                <img src="{{ asset('storage/'.$image) }}"
                                                     class="w-8 h-8 rounded-full border-2 border-white object-cover shadow-sm"
                                                     title="Foto varian">
                                            @endforeach

                                            @if($variantImages->count() > 4)
                                                <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[10px] font-bold text-gray-600 shadow-sm">
                                                    +{{ $variantImages->count() - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-[11px] text-gray-400">
                                            Belum ada foto varian
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Tidak ada varian</span>
                            @endif
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
                                <a href="{{ route('marketing.products.edit', ['product' => $product->id, 'page' => request('page'), 'search' => request('search')]) }}"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors"
                                   title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>

                                <form action="{{ route('marketing.products.destroy', $product->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                    <input type="hidden" name="search" value="{{ request('search') }}">

                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors"
                                            onclick="return confirm('Hapus produk?')"
                                            title="Hapus">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400 bg-white">
                            <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>

                            @if(request('search'))
                                <p class="text-sm">Produk dengan kata kunci "{{ request('search') }}" tidak ditemukan.</p>
                                <a href="{{ route('marketing.products.index') }}" class="inline-block mt-2 text-sm text-florist-500 hover:underline">
                                    Reset pencarian
                                </a>
                            @else
                                <p class="text-sm">Belum ada data produk.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->appends(request()->query())->links() }}
</div>
@endsection