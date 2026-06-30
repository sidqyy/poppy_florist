@extends('layouts.pos')

@section('content')
<div class="flex h-full w-full bg-gray-50">
    <div class="flex-1 p-6 overflow-y-auto" style="padding-bottom: 100px;">
        
        @if(!request('category'))
            <div class="max-w-7xl mx-auto w-full pt-10 relative">
                <a href="{{ route('pos.index') }}" class="absolute top-0 left-0 w-12 h-12 bg-white border border-gray-200 hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-600 transition-colors shadow-sm touch-btn" title="Kembali ke Menu Utama">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                
                <div class="text-center mb-12">
                    <h2 class="text-5xl font-black text-gray-800 tracking-tight">Pilih Occasion</h2>
                    <p class="text-xl text-gray-400 mt-3 font-medium">Pilih kategori acara untuk melihat daftar buket yang tersedia</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($categories as $category)
                        @if(in_array($category->slug, ['bunga-artificial', 'custom-order'])) @continue @endif

                        @php
                            $catName = strtolower($category->name);

                            if (str_contains($catName, 'birthday') || str_contains($catName, 'ulang tahun')) {
                                $icon = 'fa-cake-candles';
                                $iconColor = 'text-amber-600';
                                $bgColor = 'bg-gradient-to-tr from-amber-50 to-orange-100/50';
                                $borderColor = 'group-hover:border-amber-400 group-hover:shadow-amber-100';
                            } elseif (str_contains($catName, 'graduation') || str_contains($catName, 'wisuda') || str_contains($catName, 'kelulusan')) {
                                $icon = 'fa-graduation-cap';
                                $iconColor = 'text-indigo-600';
                                $bgColor = 'bg-gradient-to-tr from-indigo-50 to-blue-100/50';
                                $borderColor = 'group-hover:border-indigo-400 group-hover:shadow-indigo-100';
                            } elseif (str_contains($catName, 'anniversary')) {
                                $icon = 'fa-gift';
                                $iconColor = 'text-rose-600';
                                $bgColor = 'bg-gradient-to-tr from-rose-50 to-pink-100/50';
                                $borderColor = 'group-hover:border-rose-400 group-hover:shadow-rose-100';
                            } elseif (str_contains($catName, 'rest in peace') || str_contains($catName, 'duka cita') || str_contains($catName, 'condolence')) {
                                $icon = 'fa-dove';
                                $iconColor = 'text-slate-600';
                                $bgColor = 'bg-gradient-to-tr from-slate-100 to-slate-200/50';
                                $borderColor = 'group-hover:border-slate-400 group-hover:shadow-slate-100';
                            } elseif (str_contains($catName, 'wedding') || str_contains($catName, 'pernikahan')) {
                                $icon = 'fa-heart';
                                $iconColor = 'text-pink-600';
                                $bgColor = 'bg-gradient-to-tr from-pink-50 to-rose-100/50';
                                $borderColor = 'group-hover:border-pink-400 group-hover:shadow-pink-100';
                            } elseif (str_contains($catName, 'opening') || str_contains($catName, 'pembukaan') || str_contains($catName, 'grand')) {
                                $icon = 'fa-store';
                                $iconColor = 'text-emerald-600';
                                $bgColor = 'bg-gradient-to-tr from-emerald-50 to-teal-100/50';
                                $borderColor = 'group-hover:border-emerald-400 group-hover:shadow-emerald-100';
                            } elseif (str_contains($catName, 'formal') || str_contains($catName, 'ceremony') || str_contains($catName, 'resmi')) {
                                $icon = 'fa-award';
                                $iconColor = 'text-sky-600';
                                $bgColor = 'bg-gradient-to-tr from-sky-50 to-blue-100/50';
                                $borderColor = 'group-hover:border-sky-400 group-hover:shadow-sky-100';
                            } elseif (str_contains($catName, 'baby') || str_contains($catName, 'melahirkan')) {
                                $icon = 'fa-baby';
                                $iconColor = 'text-cyan-600';
                                $bgColor = 'bg-gradient-to-tr from-cyan-50 to-sky-100/50';
                                $borderColor = 'group-hover:border-cyan-400 group-hover:shadow-cyan-100';
                            } elseif (str_contains($catName, 'get well') || str_contains($catName, 'sembuh') || str_contains($catName, 'gws')) {
                                $icon = 'fa-heart-pulse';
                                $iconColor = 'text-teal-600';
                                $bgColor = 'bg-gradient-to-tr from-teal-50 to-emerald-100/50';
                                $borderColor = 'group-hover:border-teal-400 group-hover:shadow-teal-100';
                            } else {
                                $icon = 'fa-spa';
                                $iconColor = 'text-pink-600';
                                $bgColor = 'bg-gradient-to-tr from-pink-50 to-rose-100/50';
                                $borderColor = 'group-hover:border-pink-400 group-hover:shadow-pink-100';
                            }
                        @endphp

                        <a href="{{ route('pos.catalog', ['category' => $category->id]) }}" class="group bg-white p-8 rounded-[32px] shadow-lg hover:shadow-xl border-2 border-transparent {{ $borderColor }} transition-all duration-300 touch-btn flex items-center gap-6">
                            <div class="w-28 h-28 {{ $bgColor }} rounded-2xl flex items-center justify-center {{ $iconColor }} group-hover:scale-110 transition-transform duration-300 shadow-sm shrink-0">
                                <i class="fa-solid {{ $icon }} text-5xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-2xl font-black text-gray-800 mb-1.5 group-hover:text-pink-500 transition-colors">{{ $category->name }}</h3>
                                <p class="text-gray-400 text-sm leading-snug">Koleksi buket spesial {{ $category->name }}</p>
                            </div>
                            <div class="w-14 h-14 rounded-full bg-gray-50 group-hover:bg-pink-50 flex items-center justify-center text-gray-400 group-hover:text-pink-500 transition-colors shrink-0">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
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
                        @php
                            $hasVariants = $product->sizes && $product->sizes->count() > 0;
                        @endphp

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

                                @if($hasVariants)
                                    <div class="absolute top-4 left-4 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Varian
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-800 mb-1 leading-tight">{{ $product->name }}</h3>

                                <p class="text-florist-500 font-extrabold text-2xl mb-4 mt-auto">
                                    {{ $product->formatted_price }}
                                </p>
                                
                                <form action="{{ route('pos.cart.add') }}" method="POST" id="form-add-{{ $product->id }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="is_rented" id="is-rented-{{ $product->id }}" value="0">
                                    <input type="hidden" name="rental_duration" id="rental-duration-{{ $product->id }}" value="">

                                    @if($hasVariants)
                                        <button type="button"
                                            data-action="variant"
                                            data-product-id="{{ $product->id }}"
                                            class="touch-btn w-full py-4 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                                            <i class="fa-solid fa-layer-group"></i> Pilih Varian
                                        </button>

                                    @elseif($product->is_rentable)
                                        @if($product->price_type == 'range')
                                            <input type="hidden" name="custom_price" id="custom-price-{{ $product->id }}" value="">
                                        @endif

                                        <button type="button" data-action="rentable" data-product-id="{{ $product->id }}" data-price-type="{{ $product->price_type }}" data-min-price="{{ $product->total_price }}" data-max-price="{{ $product->max_price ?? 0 }}" data-rental-price-per-day="{{ $product->rental_price_per_day ?? 0 }}" class="touch-btn w-full py-4 bg-florist-50 hover:bg-florist-100 text-florist-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                                            <i class="fa-solid fa-cart-plus"></i> Tambah
                                        </button>

                                    @elseif($product->price_type == 'range')
                                        <input type="hidden" name="custom_price" id="custom-price-{{ $product->id }}" value="">

                                        <button type="button" data-action="range" data-product-id="{{ $product->id }}" data-min-price="{{ $product->total_price }}" data-max-price="{{ $product->max_price ?? $product->total_price }}" class="touch-btn w-full py-4 bg-florist-50 hover:bg-florist-100 text-florist-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
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
            @endif
        @endif
    </div>

    @include('pos.partials.cart')
</div>

<style>
    .pagination-container nav {
        width: 100%;
        display: flex;
        justify-content: center !important;
    }

    .pagination-container nav > div:first-child {
        display: none !important;
    }

    .pagination-container nav > div:last-child {
        display: flex;
        justify-content: center !important;
        flex: 1;
    }

    #imagePreviewModal {
        z-index: 9999999 !important;
    }

    #imagePreviewModal img,
    #imagePreviewModal button {
        z-index: 10000000 !important;
    }
</style>

<?php
    $variantProducts = [];

    $products = $products ?? [];

    if (!is_iterable($products)) {
        $products = [];
    }

    foreach ($products as $product) {
        if ($product === null) {
            continue;
        }

        $productImage = $product->image
            ? \Illuminate\Support\Facades\Storage::url($product->image)
            : null;

        $variantProducts[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'image' => $productImage,
            'requires_component_selection' => (bool) $product->has_flexible_components,
            'is_rentable' => (bool) $product->is_rentable,
            'rental_price_per_day' => (int) ($product->rental_price_per_day ?? 0),
            'sizes' => [],
        ];

        foreach ($product->sizes ?? [] as $size) {
            if ($size === null) {
                continue;
            }

            $sizeImage = $size->image
                ? \Illuminate\Support\Facades\Storage::url($size->image)
                : $productImage;

            $sizeData = [
                'id' => $size->id,
                'size_name' => $size->size_name,
                'image' => $sizeImage,
                'variants' => [],
            ];

            foreach ($size->variants ?? [] as $variant) {
                if ($variant === null) {
                    continue;
                }

                $variantImage = $variant->image
                    ? \Illuminate\Support\Facades\Storage::url($variant->image)
                    : $sizeImage;

                $sizeData['variants'][] = [
                    'id' => $variant->id,
                    'variant_name' => $variant->variant_name,
                    'price' => (int) $variant->price,
                    'image' => $variantImage,
                ];
            }

            $variantProducts[$product->id]['sizes'][] = $sizeData;
        }
    }

    $materialData = [];

    $materials = $materials ?? [];

    if (!is_iterable($materials)) {
        $materials = [];
    }

    foreach ($materials as $material) {
        if ($material === null) {
            continue;
        }

        $materialData[] = [
            'id' => $material->id,
            'name' => $material->name,
            'type' => $material->type,
            'stock' => $material->stock,
            'unit' => $material->unit,
        ];
    }
?>

<script id="productVariantsData" type="application/json">
    {!! json_encode($variantProducts, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>

<script id="materialData" type="application/json">
    {!! json_encode($materialData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>

<script>
    const productVariantsData = JSON.parse(document.getElementById('productVariantsData').textContent || 'null') || {};
    const materialData = JSON.parse(document.getElementById('materialData').textContent || '[]');

    document.addEventListener('click', function(event) {
        const button = event.target.closest('button[data-action]');

        if (!button) return;

        const action = button.dataset.action;
        const productId = parseInt(button.dataset.productId, 10);

        if (!productId) return;

        if (action === 'variant') {
            event.preventDefault();
            openVariantModal(productId);
        } else if (action === 'rentable') {
            event.preventDefault();

            const priceType = button.dataset.priceType || '';
            const minPrice = parseInt(button.dataset.minPrice, 10) || 0;
            const maxPrice = parseInt(button.dataset.maxPrice, 10) || 0;
            const rentalPricePerDay = parseInt(button.dataset.rentalPricePerDay, 10) || 0;

            promptRentable(productId, priceType, minPrice, maxPrice, rentalPricePerDay);
        } else if (action === 'range') {
            event.preventDefault();

            const minPrice = parseInt(button.dataset.minPrice, 10) || 0;
            const maxPrice = parseInt(button.dataset.maxPrice, 10) || 0;

            promptRangePrice(productId, minPrice, maxPrice);
        }
    });

function openVariantModal(productId) {
    const product = productVariantsData[productId];

    if (!product || !product.sizes || product.sizes.length === 0) {
        Swal.fire('Gagal', 'Produk ini belum memiliki varian.', 'error');
        return;
    }

    let sizeOptions = '';

    product.sizes.forEach((size, index) => {
        sizeOptions += `
            <option value="${size.id}" ${index === 0 ? 'selected' : ''}>
                ${size.size_name}
            </option>
        `;
    });

    const firstSize = product.sizes[0];
    const firstVariants = firstSize.variants || [];
    const firstPrice = firstVariants.length > 0 ? firstVariants[0].price : 0;

    const firstImage = firstVariants.length > 0 && firstVariants[0].image
        ? firstVariants[0].image
        : (firstSize.image || product.image || '');

    let variantOptions = '';

    firstVariants.forEach((variant, index) => {
        variantOptions += `
            <option
                value="${variant.id}"
                data-price="${variant.price}"
                data-image="${variant.image || ''}"
                ${index === 0 ? 'selected' : ''}>
                ${variant.variant_name}
            </option>
        `;
    });

    Swal.fire({
        title: product.name,
        width: 850,
        showCancelButton: true,
        confirmButtonText: product.requires_component_selection ? 'Lanjut Pilih Komponen' : 'Tambah ke Keranjang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#EC4899',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-left">
                <div class="bg-gray-100 rounded-2xl overflow-hidden h-80 flex items-center justify-center">
                    ${
                        firstImage
                            ? `<img id="variantPreviewImage"
                                   src="${firstImage}"
                                   onclick="openFullImage(this.src)"
                                   class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                                   title="Klik untuk melihat gambar penuh">`
                            : `<i class="fa-solid fa-image text-6xl text-gray-300"></i>`
                    }
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ukuran</label>
                    <select id="variantSizeSelect" class="w-full px-4 py-3 border border-gray-200 rounded-xl mb-4">
                        ${sizeOptions}
                    </select>

                    <label class="block text-sm font-bold text-gray-700 mb-2">Titik Bunga</label>
                    <select id="variantPriceSelect" class="w-full px-4 py-3 border border-gray-200 rounded-xl mb-4">
                        ${variantOptions}
                    </select>

                    <div class="bg-pink-50 border border-pink-100 rounded-2xl p-4">
                        <p class="text-sm text-gray-500">Harga Terpilih</p>
                        <p id="variantSelectedPrice" class="text-3xl font-black text-pink-600">
                            Rp ${firstPrice.toLocaleString('id-ID')}
                        </p>
                    </div>

                    ${product.is_rentable ? `
                        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                            <label class="flex items-center gap-2 font-bold text-blue-700">
                                <input type="checkbox" id="variantIsRented" class="w-4 h-4">
                                Sewa Produk Ini
                            </label>

                            <div id="variantRentalBox" class="hidden mt-3">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Hari</label>
                                <input type="number" id="variantRentalDuration" min="1" value="1"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl">

                                <p class="text-sm text-blue-600 font-bold mt-2">
                                    Harga Sewa: Rp ${product.rental_price_per_day.toLocaleString('id-ID')} / hari
                                </p>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `,
        didOpen: () => {
            const sizeSelect = document.getElementById('variantSizeSelect');
            const variantSelect = document.getElementById('variantPriceSelect');
            const priceText = document.getElementById('variantSelectedPrice');
            const rentedCheckbox = document.getElementById('variantIsRented');
            const rentalBox = document.getElementById('variantRentalBox');

            if (rentedCheckbox && rentalBox) {
                rentedCheckbox.addEventListener('change', function() {
                    rentalBox.classList.toggle('hidden', !this.checked);
                });
            }

            function getPreviewImageElement() {
                return document.getElementById('variantPreviewImage');
            }

            function refreshPriceAndImage() {
                const selectedOption = variantSelect.options[variantSelect.selectedIndex];

                if (!selectedOption) {
                    priceText.textContent = 'Rp 0';
                    return;
                }

                const price = parseInt(selectedOption.dataset.price || 0);
                priceText.textContent = 'Rp ' + price.toLocaleString('id-ID');

                const selectedSizeId = parseInt(sizeSelect.value, 10);
                const selectedSize = product.sizes.find(size => size.id === selectedSizeId);

                if (!selectedSize) return;

                const selectedVariant = selectedSize.variants.find(variant => String(variant.id) === String(selectedOption.value));
                const imageUrl = selectedVariant && selectedVariant.image
                    ? selectedVariant.image
                    : (selectedSize.image || product.image || '');

                const previewImage = getPreviewImageElement();

                if (previewImage && imageUrl) {
                    previewImage.src = imageUrl;
                }
            }

            function refreshVariantOptions() {
                const selectedSizeId = parseInt(sizeSelect.value, 10);
                const selectedSize = product.sizes.find(size => size.id === selectedSizeId);

                if (!selectedSize) return;

                variantSelect.innerHTML = '';

                selectedSize.variants.forEach((variant, index) => {
                    const option = document.createElement('option');

                    option.value = variant.id;
                    option.dataset.price = variant.price;
                    option.dataset.image = variant.image || '';
                    option.textContent = variant.variant_name;

                    if (index === 0) {
                        option.selected = true;
                    }

                    variantSelect.appendChild(option);
                });

                refreshPriceAndImage();
            }

            sizeSelect.addEventListener('change', refreshVariantOptions);
            variantSelect.addEventListener('change', refreshPriceAndImage);

            refreshPriceAndImage();
        },
        preConfirm: () => {
            const sizeId = document.getElementById('variantSizeSelect').value;
            const variantId = document.getElementById('variantPriceSelect').value;
            const isRented = document.getElementById('variantIsRented')?.checked ? 1 : 0;
            const rentalDuration = document.getElementById('variantRentalDuration')?.value || '';

            if (!sizeId || !variantId) {
                Swal.showValidationMessage('Pilih ukuran dan titik bunga terlebih dahulu.');
                return false;
            }

            if (isRented && (!rentalDuration || rentalDuration < 1)) {
                Swal.showValidationMessage('Masukkan jumlah hari sewa yang valid.');
                return false;
            }

            return {
                size_id: sizeId,
                variant_id: variantId,
                is_rented: isRented,
                rental_duration: rentalDuration
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (product.requires_component_selection) {
                openComponentModal(
                    productId,
                    result.value.size_id,
                    result.value.variant_id,
                    result.value.is_rented,
                    result.value.rental_duration
                );
            } else {
                addVariantDirectToCart(
                    productId,
                    result.value.size_id,
                    result.value.variant_id,
                    result.value.is_rented,
                    result.value.rental_duration
                );
            }
        }
    });
}
function openComponentModal(productId, sizeId, variantId, isRented = 0, rentalDuration = '') {
    let materialHtml = '';

    materialData.forEach(material => {
        materialHtml += `
            <div class="flex items-center justify-between gap-3 border border-gray-200 rounded-xl p-3 mb-2">
                <div class="text-left">
                    <p class="font-bold text-gray-800">${material.name}</p>

                    <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full bg-pink-100 text-pink-600">
                        ${material.type}
                    </span>

                    <p class="text-xs text-gray-400">Stok: ${material.stock} ${material.unit}</p>
                </div>

                <input
                    type="number"
                    min="0"
                    max="${material.stock}"
                    value="0"
                    data-material-id="${material.id}"
                    class="component-qty w-20 px-3 py-2 border border-gray-200 rounded-lg text-center"
                >
            </div>
        `;
    });

    Swal.fire({
        title: 'Pilih Komponen Bunga',
        width: 700,
        html: `
            <div class="text-left max-h-[420px] overflow-y-auto">
                ${materialHtml}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Tambah ke Keranjang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#EC4899',
        preConfirm: () => {
            const selectedMaterials = [];

            document.querySelectorAll('.component-qty').forEach(input => {
                const qty = parseInt(input.value) || 0;

                if (qty > 0) {
                    selectedMaterials.push({
                        material_id: input.dataset.materialId,
                        qty: qty
                    });
                }
            });

            if (selectedMaterials.length === 0) {
                Swal.showValidationMessage('Pilih minimal satu material.');
                return false;
            }

            return selectedMaterials;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');

            form.method = 'POST';
            form.action = "{{ route('pos.cart.add-variant-product') }}";

            let materialInputs = '';

            result.value.forEach((item, index) => {
                materialInputs += `
                    <input type="hidden" name="materials[${index}][material_id]" value="${item.material_id}">
                    <input type="hidden" name="materials[${index}][qty]" value="${item.qty}">
                `;
            });

            form.innerHTML = `
                @csrf
                <input type="hidden" name="product_id" value="${productId}">
                <input type="hidden" name="size_id" value="${sizeId}">
                <input type="hidden" name="variant_id" value="${variantId}">
                <input type="hidden" name="is_rented" value="${isRented}">
                <input type="hidden" name="rental_duration" value="${rentalDuration}">
                ${materialInputs}
            `;

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function addVariantDirectToCart(productId, sizeId, variantId, isRented = 0, rentalDuration = '') {
    const form = document.createElement('form');

    form.method = 'POST';
    form.action = "{{ route('pos.cart.add-variant-product') }}";

    form.innerHTML = `
        @csrf
        <input type="hidden" name="product_id" value="${productId}">
        <input type="hidden" name="size_id" value="${sizeId}">
        <input type="hidden" name="variant_id" value="${variantId}">
        <input type="hidden" name="is_rented" value="${isRented}">
        <input type="hidden" name="rental_duration" value="${rentalDuration}">
    `;

    document.body.appendChild(form);
    form.submit();
}

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
                    return 'Anda harus memasukkan harga!';
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
                            return 'Masukkan jumlah hari yang valid!';
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

    function openFullImage(imageUrl) {
        let oldModal = document.getElementById('imagePreviewModal');

        if (oldModal) {
            oldModal.remove();
        }

        const modal = document.createElement('div');

        modal.id = 'imagePreviewModal';
        modal.style.position = 'fixed';
        modal.style.inset = '0';
        modal.style.background = 'rgba(0,0,0,0.92)';
        modal.style.zIndex = '2147483647';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.padding = '20px';

        modal.innerHTML = `
            <button type="button"
                onclick="closeImagePreview()"
                style="
                    position:absolute;
                    top:20px;
                    right:30px;
                    color:white;
                    font-size:48px;
                    font-weight:bold;
                    z-index:2147483647;
                ">
                ×
            </button>

            <img src="${imageUrl}"
                style="
                    max-width:95vw;
                    max-height:95vh;
                    object-fit:contain;
                    border-radius:16px;
                    box-shadow:0 20px 50px rgba(0,0,0,0.5);
                    z-index:2147483647;
                ">
        `;

        modal.addEventListener('click', function(e) {
            if (e.target.id === 'imagePreviewModal') {
                closeImagePreview();
            }
        });

        document.body.appendChild(modal);
    }

    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');

        if (modal) {
            modal.remove();
        }
    }
</script>
@endsection