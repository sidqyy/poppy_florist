@extends('layouts.pos')

@section('content')
<div class="flex h-full w-full bg-gray-50">
    <div class="flex-1 p-6 overflow-y-auto" style="padding-bottom: 100px;">
        
        @if(!request('category'))
            <!-- Occasion Selection Screen -->
            <div class="max-w-6xl mx-auto w-full pt-10 relative">
                <a href="{{ route('pos.index') }}" class="absolute top-0 left-0 w-12 h-12 bg-white border border-gray-200 hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-600 transition-colors shadow-sm touch-btn" title="Kembali ke Menu Utama">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-extrabold text-gray-800 tracking-tight">Pilih Occasion</h2>
                    <p class="text-xl text-gray-500 mt-2">Pilih kategori acara untuk melihat daftar buket yang tersedia</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($categories as $category)
                    @if(in_array($category->slug, ['bunga-artificial', 'custom-order'])) @continue @endif
                    @php
                        $catName = strtolower($category->name);
                        $icon = 'fa-spa';
                    @endphp
                    <a href="{{ route('pos.catalog', ['category' => $category->id]) }}" class="group bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-florist-200 border-2 border-transparent hover:border-florist-500 transition-all touch-btn flex items-center gap-6">
                        <div class="w-24 h-24 bg-florist-100 rounded-2xl flex items-center justify-center text-florist-600 group-hover:scale-110 transition-transform shadow-sm">
                            <i class="fa-solid {{ $icon }} text-5xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-extrabold text-gray-800 mb-2">{{ $category->name }}</h3>
                            <p class="text-gray-500 text-lg leading-snug">Lihat koleksi buket untuk acara {{ $category->name }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 text-2xl group-hover:text-florist-500 transition-colors"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Product Grid Screen for Selected Occasion -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('pos.catalog') }}" class="w-12 h-12 bg-white border border-gray-200 hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-600 transition-colors shadow-sm touch-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            @php
                                $selectedCategory = $categories->where('id', request('category'))->first();
                            @endphp
                            Katalog: {{ $selectedCategory ? $selectedCategory->name : 'Semua' }}
                        </h2>
                        <p class="text-gray-500 font-medium mt-1">Pilih produk untuk dimasukkan ke keranjang</p>
                    </div>
                </div>
            </div>
            
            @if($products->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center shadow-md border-2 border-gray-200 mt-10">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mx-auto mb-4">
                        <i class="fa-solid fa-box-open text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum Ada Produk</h3>
                    <p class="text-gray-500 text-lg">Belum ada buket yang tersedia untuk kategori ini.</p>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($products as $product)
                    <div class="bg-white rounded-3xl shadow-md border-2 border-gray-200 overflow-hidden flex flex-col">
                        <div class="h-64 bg-gray-100 relative">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class="fa-solid fa-image text-6xl"></i>
                            </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-florist-500 shadow-sm">
                                {{ $product->formatted_price }}
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="text-xl font-bold text-gray-800 mb-1 leading-tight">{{ $product->name }}</h3>
                            <p class="text-florist-500 font-extrabold text-2xl mb-4 mt-auto">{{ $product->formatted_price }}</p>
                            
                            <form action="{{ route('pos.cart.add') }}" method="POST" id="form-add-{{ $product->id }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="is_rented" id="is-rented-{{ $product->id }}" value="0">
                                <input type="hidden" name="rental_duration" id="rental-duration-{{ $product->id }}" value="">
                                
                                @if($product->is_rentable)
                                    @if($product->price_type == 'range')
                                    <input type="hidden" name="custom_price" id="custom-price-{{ $product->id }}" value="">
                                    @endif
                                    <button type="button" onclick="promptRentable({{ $product->id }}, '{{ $product->price_type }}', {{ $product->total_price }}, {{ $product->max_price ?? 0 }}, {{ $product->rental_price_per_day }})" class="touch-btn w-full py-4 bg-florist-50 hover:bg-florist-100 text-florist-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                                        <i class="fa-solid fa-cart-plus"></i> Tambah
                                    </button>
                                @elseif($product->price_type == 'range')
                                <input type="hidden" name="custom_price" id="custom-price-{{ $product->id }}" value="">
                                <button type="button" onclick="promptRangePrice({{ $product->id }}, {{ $product->total_price }}, {{ $product->max_price }})" class="touch-btn w-full py-4 bg-florist-50 hover:bg-florist-100 text-florist-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                                    <i class="fa-solid fa-cart-plus"></i> Tambah
                                </button>
                                @else
                                <button type="submit" class="touch-btn w-full py-4 bg-florist-50 hover:bg-florist-100 text-florist-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                                    <i class="fa-solid fa-cart-plus"></i> Tambah
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-8 mb-12 flex justify-center pagination-container">
                    {{ $products->links() }}
                </div>
                
                <style>
                    /* Center Laravel Tailwind Pagination */
                    .pagination-container nav {
                        width: 100%;
                        display: flex;
                        justify-content: center !important;
                    }
                    /* Hide the "Showing 1 to 10..." text on desktop */
                    .pagination-container nav > div:first-child {
                        display: none !important;
                    }
                    /* Center the page numbers */
                    .pagination-container nav > div:last-child {
                        display: flex;
                        justify-content: center !important;
                        flex: 1;
                    }
                </style>
            @endif
        @endif
    </div>

    @include('pos.partials.cart')
</div>

<script>
    function promptRangePrice(productId, minPrice, maxPrice) {
        Swal.fire({
            title: 'Tentukan Harga',
            text: `Produk ini memiliki range harga Rp ${minPrice.toLocaleString('id-ID')} - Rp ${maxPrice.toLocaleString('id-ID')}`,
            input: 'number',
            inputAttributes: {
                min: minPrice,
                max: maxPrice,
                step: 1000
            },
            inputValue: minPrice,
            showCancelButton: true,
            confirmButtonText: 'Tambahkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#EC4899',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus memasukkan harga!'
                }
                if (value < minPrice) {
                    return `Harga minimal adalah Rp ${minPrice.toLocaleString('id-ID')}`;
                }
                if (value > maxPrice) {
                    return `Harga maksimal adalah Rp ${maxPrice.toLocaleString('id-ID')}`;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('custom-price-' + productId).value = result.value;
                document.getElementById('form-add-' + productId).submit();
            }
        });
    }

    function promptRentable(productId, priceType, minPrice, maxPrice, rentalPricePerDay) {
        Swal.fire({
            title: 'Pilih Mode Transaksi',
            text: 'Apakah produk ini akan dibeli permanen atau disewa?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Beli',
            denyButtonText: 'Sewa',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#EC4899',
            denyButtonColor: '#3B82F6'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('is-rented-' + productId).value = '0';
                if (priceType === 'range') {
                    promptRangePrice(productId, minPrice, maxPrice);
                } else {
                    document.getElementById('form-add-' + productId).submit();
                }
            } else if (result.isDenied) {
                Swal.fire({
                    title: 'Berapa Hari?',
                    text: `Harga sewa: Rp ${rentalPricePerDay.toLocaleString('id-ID')} / hari`,
                    input: 'number',
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    inputValue: 1,
                    showCancelButton: true,
                    confirmButtonText: 'Tambahkan',
                    confirmButtonColor: '#3B82F6',
                    inputValidator: (value) => {
                        if (!value || value < 1) {
                            return 'Masukkan jumlah hari yang valid!'
                        }
                    }
                }).then((rentResult) => {
                    if (rentResult.isConfirmed) {
                        document.getElementById('is-rented-' + productId).value = '1';
                        document.getElementById('rental-duration-' + productId).value = rentResult.value;
                        document.getElementById('form-add-' + productId).submit();
                    }
                });
            }
        });
    }
</script>
@endsection
