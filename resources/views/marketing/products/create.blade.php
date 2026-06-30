@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page_title', 'Tambah Produk')

@section('content')
<div class="mb-6">
    <a href="{{ route('marketing.products.index', ['page' => request('page')]) }}" class="text-gray-500 hover:text-florist-500">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-center mb-2">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i>
            <h3 class="font-bold text-red-800">Gagal Menyimpan Data</h3>
        </div>
        <ul class="list-disc list-inside text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('marketing.products.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf
    <input type="hidden" name="page" value="{{ request('page') }}">

    <div class="lg:col-span-1 space-y-6">
        <div class="card-modern p-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Informasi Dasar</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Acara (Opsional, Bisa Lebih Dari Satu)</label>
                <div class="grid grid-cols-2 gap-2 p-3 border border-gray-200 rounded-lg bg-gray-50 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }} class="w-4 h-4 text-florist-500 rounded focus:ring-florist-400">
                        <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketersediaan Produk</label>
                <select name="availability" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <option value="preorder" {{ old('availability') == 'preorder' ? 'selected' : '' }}>Pre-Order</option>
                    <option value="ready" {{ old('availability') == 'ready' ? 'selected' : '' }}>Ready Stock</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                <input type="file" name="image" id="productImageInput" accept="image/*" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">

                <div id="imagePreviewContainer" class="mt-3 hidden">
                    <p class="text-xs text-gray-400 mb-1">Pratinjau Foto:</p>
                    <div class="relative w-full h-48 rounded-lg overflow-hidden bg-gray-50 border border-gray-200 flex items-center justify-center">
                        <img id="imagePreview" src="" alt="Pratinjau Foto" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="mb-4 border-t border-gray-100 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Harga</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="price_type" value="fixed" {{ old('price_type', 'fixed') == 'fixed' ? 'checked' : '' }} class="w-4 h-4 text-florist-500 focus:ring-florist-400">
                        <span class="text-sm text-gray-700">Harga Tetap</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="price_type" value="range" {{ old('price_type') == 'range' ? 'checked' : '' }} class="w-4 h-4 text-florist-500 focus:ring-florist-400">
                        <span class="text-sm text-gray-700">Harga Range</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" id="labelTotalPrice">Harga Jual / Harga Minimal</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                    <input type="number" name="total_price" value="{{ old('total_price') }}" required class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>
            </div>

            <div class="mb-4 {{ old('price_type') == 'range' ? '' : 'hidden' }}" id="containerMaxPrice">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Maksimal</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                    <input type="number" name="max_price" id="inputMaxPrice" value="{{ old('max_price') }}" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="card-modern p-6 mb-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Pengaturan Opsi (Penyewaan & Kustomisasi)</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="font-bold text-gray-700 block mb-1">Bisa Disewa?</span>
                            <p class="text-xs text-gray-500 italic pr-4">Jika dicentang, maka saat mode sewa harga = harga sewa per hari dikali jumlah hari.</p>
                        </div>

                        <input type="checkbox" name="is_rentable" value="1" {{ old('is_rentable') ? 'checked' : '' }} class="w-5 h-5 text-florist-500 rounded focus:ring-florist-400 cursor-pointer mt-0.5" id="isRentableCheckbox">
                    </div>

                    <div id="rentalPriceContainer" class="mt-auto pt-3 border-t border-gray-200 {{ old('is_rentable') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa per Hari:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="rental_price_per_day" value="{{ old('rental_price_per_day') }}" placeholder="cth: 100000" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="font-bold text-gray-700 block mb-1">Komponen Fleksibel?</span>
                            <p class="text-xs text-gray-500 italic pr-4">Customer memilih komponen sendiri saat order.</p>
                        </div>

                        <input type="checkbox" name="has_flexible_components" value="1" {{ old('has_flexible_components') ? 'checked' : '' }} class="w-5 h-5 text-florist-500 rounded focus:ring-florist-400 cursor-pointer mt-0.5" id="isFlexibleCheckbox">
                    </div>

                    <div id="flexibleComponentsContainer" class="mt-auto pt-3 border-t border-gray-200 {{ old('has_flexible_components') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Komponen Items:</label>
                        <input type="number" name="max_flexible_components" value="{{ old('max_flexible_components') }}" placeholder="cth: 10" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer w-max">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-florist-500 rounded focus:ring-florist-400">
                    <span class="font-bold text-gray-700">Produk Aktif (Tampilkan di Katalog)</span>
                </label>
            </div>
        </div>

        <div class="card-modern p-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-bold text-gray-800">Komponen Bahan Baku (Opsional)</h4>
                <button type="button" id="addComponentBtn" class="px-3 py-1.5 bg-florist-100 text-florist-600 hover:bg-florist-200 rounded text-sm font-medium">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Bahan
                </button>
            </div>

            <div class="bg-gray-50 rounded-lg p-1 border-2 border-gray-200 shadow-md">
                <table class="w-full text-left text-sm" id="componentsTable">
                    <thead>
                        <tr class="text-gray-500">
                            <th class="p-3 w-1/2">Bahan Baku</th>
                            <th class="p-3 w-20">Qty</th>
                            <th class="p-3">Catatan</th>
                            <th class="p-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="componentsBody">
                        <tr class="component-row border-t border-gray-200 bg-white">
                            <td class="p-2">
                                <select name="components[0][material_id]" class="material-select w-full px-3 py-2 border border-gray-200 rounded focus:ring-1 focus:ring-florist-400 outline-none">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($materials as $mat)
                                        <option value="{{ $mat->id }}" data-price="{{ $mat->price }}">
                                            {{ $mat->name }} (Rp {{ number_format($mat->price,0,',','.') }}/{{ $mat->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="p-2">
                                <input type="number" name="components[0][qty]" min="1" class="qty-input w-full px-3 py-2 border border-gray-200 rounded text-center focus:ring-1 focus:ring-florist-400 outline-none">
                            </td>

                            <td class="p-2">
                                <input type="text" name="components[0][notes]" placeholder="Opsional" class="w-full px-3 py-2 border border-gray-200 rounded focus:ring-1 focus:ring-florist-400 outline-none">
                            </td>

                            <td class="p-2 text-center">
                                <button type="button" class="remove-btn text-red-400 hover:text-red-600">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between items-center p-4 bg-florist-50 rounded-lg border border-florist-100">
                <span class="text-gray-600 font-medium">Estimasi Harga Pokok:</span>
                <span class="text-2xl font-bold text-florist-600" id="totalPriceDisplay">Rp 0</span>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="font-bold text-gray-800">Varian Produk</h4>
                        <p class="text-xs text-gray-500">Ukuran, gambar ukuran, harga varian, dan foto khusus tiap varian.</p>
                    </div>

                    <button type="button" onclick="addSizeRow()" class="px-3 py-2 bg-blue-500 text-white rounded-lg text-sm font-bold">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Ukuran
                    </button>
                </div>

                <div id="sizesContainer" class="space-y-4"></div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg shadow-sm">
                    Simpan Produk
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('componentsBody');
    const addBtn = document.getElementById('addComponentBtn');
    const totalDisplay = document.getElementById('totalPriceDisplay');

    let rowCount = 1;
    let sizeIndex = 0;

    const materialsOptions = `
        <option value="">-- Pilih Bahan --</option>
        @foreach($materials as $mat)
            <option value="{{ $mat->id }}" data-price="{{ $mat->price }}">
                {{ $mat->name }} (Rp {{ number_format($mat->price,0,',','.') }}/{{ $mat->unit }})
            </option>
        @endforeach
    `;

    function showElement(element) {
        element.classList.remove('hidden');
    }

    function hideElement(element) {
        element.classList.add('hidden');
    }

    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('.component-row').forEach(row => {
            const select = row.querySelector('.material-select');
            const qtyInput = row.querySelector('.qty-input');

            if (select && select.value && select.options[select.selectedIndex]) {
                const price = parseFloat(select.options[select.selectedIndex].dataset.price || 0);
                const qty = parseInt(qtyInput.value || 0);
                total += price * qty;
            }
        });

        totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    addBtn.addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.className = 'component-row border-t border-gray-200 bg-white';

        tr.innerHTML = `
            <td class="p-2">
                <select name="components[${rowCount}][material_id]" class="material-select w-full px-3 py-2 border border-gray-200 rounded focus:ring-1 focus:ring-florist-400 outline-none">
                    ${materialsOptions}
                </select>
            </td>

            <td class="p-2">
                <input type="number" name="components[${rowCount}][qty]" min="1" value="1" class="qty-input w-full px-3 py-2 border border-gray-200 rounded text-center focus:ring-1 focus:ring-florist-400 outline-none">
            </td>

            <td class="p-2">
                <input type="text" name="components[${rowCount}][notes]" placeholder="Opsional" class="w-full px-3 py-2 border border-gray-200 rounded focus:ring-1 focus:ring-florist-400 outline-none">
            </td>

            <td class="p-2 text-center">
                <button type="button" class="remove-btn text-red-400 hover:text-red-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowCount++;
        calculateTotal();
    });

    tbody.addEventListener('click', (e) => {
        if (e.target.closest('.remove-btn')) {
            if (document.querySelectorAll('.component-row').length > 1) {
                e.target.closest('tr').remove();
            } else {
                const row = e.target.closest('tr');
                row.querySelector('.material-select').value = '';
                row.querySelector('.qty-input').value = '';
                row.querySelector('input[type="text"]').value = '';
            }

            calculateTotal();
        }
    });

    tbody.addEventListener('change', calculateTotal);
    tbody.addEventListener('input', calculateTotal);

    window.addSizeRow = function() {
        const container = document.getElementById('sizesContainer');
        const currentSizeIndex = sizeIndex;

        const html = `
            <div class="size-row bg-white border border-blue-100 rounded-xl p-4">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="font-bold text-gray-700">Data Ukuran</h5>
                    <button type="button" onclick="this.closest('.size-row').remove()" class="text-red-500 hover:text-red-700 text-sm font-bold">
                        Hapus Ukuran
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Ukuran</label>
                        <input type="text" name="sizes[${currentSizeIndex}][size_name]" placeholder="Contoh: 80 x 60" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Gambar Ukuran</label>
                        <input type="file" name="sizes[${currentSizeIndex}][image]" accept="image/*" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-400 mt-1">Dipakai jika varian harga tidak punya foto khusus.</p>
                    </div>
                </div>

                <div class="variants-container space-y-3"></div>

                <button type="button" onclick="addVariantRow(this, ${currentSizeIndex})" class="mt-3 px-3 py-2 bg-florist-100 text-florist-700 rounded-lg text-sm font-bold">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Varian Harga
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        sizeIndex++;
    }

    window.addVariantRow = function(button, currentSizeIndex) {
        const variantsContainer = button.closest('.size-row').querySelector('.variants-container');
        const variantIndex = variantsContainer.children.length;

        const html = `
            <div class="variant-row grid grid-cols-12 gap-2 bg-gray-50 border border-gray-100 rounded-lg p-3">
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Nama Varian</label>
                    <input type="text" name="sizes[${currentSizeIndex}][variants][${variantIndex}][variant_name]" placeholder="Contoh: 2 Titik Bunga" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Harga</label>
                    <input type="number" name="sizes[${currentSizeIndex}][variants][${variantIndex}][price]" placeholder="Harga" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>

                <div class="col-span-12 md:col-span-5">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Foto Varian Harga</label>
                    <input type="file" name="sizes[${currentSizeIndex}][variants][${variantIndex}][image]" accept="image/*" class="variant-image-input w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">

                    <div class="variant-preview-container mt-2 hidden">
                        <div class="w-full h-28 bg-white border border-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                            <img src="" class="variant-preview-image w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="col-span-12 md:col-span-1 flex items-end">
                    <button type="button" onclick="this.closest('.variant-row').remove()" class="w-full px-2 py-2 bg-red-100 text-red-600 rounded-lg">
                        ×
                    </button>
                </div>
            </div>
        `;

        variantsContainer.insertAdjacentHTML('beforeend', html);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('variant-image-input')) {
            const file = e.target.files[0];
            const row = e.target.closest('.variant-row');
            const previewContainer = row.querySelector('.variant-preview-container');
            const previewImage = row.querySelector('.variant-preview-image');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                }

                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
                previewContainer.classList.add('hidden');
            }
        }
    });

    const priceRadios = document.querySelectorAll('input[name="price_type"]');
    const containerMaxPrice = document.getElementById('containerMaxPrice');
    const inputMaxPrice = document.getElementById('inputMaxPrice');
    const labelTotalPrice = document.getElementById('labelTotalPrice');

    priceRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'range') {
                showElement(containerMaxPrice);
                inputMaxPrice.required = true;
                labelTotalPrice.textContent = 'Harga Minimal';
            } else {
                hideElement(containerMaxPrice);
                inputMaxPrice.required = false;
                labelTotalPrice.textContent = 'Harga Jual';
            }
        });
    });

    const isRentableCheckbox = document.getElementById('isRentableCheckbox');
    const rentalPriceContainer = document.getElementById('rentalPriceContainer');

    isRentableCheckbox.addEventListener('change', function() {
        if (this.checked) {
            showElement(rentalPriceContainer);
        } else {
            hideElement(rentalPriceContainer);
        }
    });

    const isFlexibleCheckbox = document.getElementById('isFlexibleCheckbox');
    const flexibleComponentsContainer = document.getElementById('flexibleComponentsContainer');

    isFlexibleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            showElement(flexibleComponentsContainer);
        } else {
            hideElement(flexibleComponentsContainer);
        }
    });

    const imageInput = document.getElementById('productImageInput');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImg = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }

            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
            previewImg.src = '';
        }
    });
});
</script>
@endsection