@extends('layouts.app')

@section('title', 'Edit Bahan Baku')
@section('page_title', 'Edit Bahan Baku')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Edit Bahan Baku</h3>
        <p class="text-gray-500 text-sm mt-1">Ubah data bahan baku</p>
    </div>
    <a href="{{ route('admin.materials.index', ['type' => $material->type]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow-sm">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div class="card-modern p-6">
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

    <form action="{{ route('admin.materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bahan Baku <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $material->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis (Tipe) <span class="text-red-500">*</span></label>
                <select name="type" id="typeSelect" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
                    <option value="flower_fresh" {{ $material->type == 'flower_fresh' ? 'selected' : '' }}>Bunga Fresh</option>
                    <option value="flower_artificial" {{ $material->type == 'flower_artificial' ? 'selected' : '' }}>Bunga Artificial</option>
                    <option value="wrapping" {{ $material->type == 'wrapping' ? 'selected' : '' }}>Wrapping</option>
                    <option value="ribbon" {{ $material->type == 'ribbon' ? 'selected' : '' }}>Pita</option>
                    <option value="doll" {{ $material->type == 'doll' ? 'selected' : '' }}>Boneka</option>
                    <option value="greeting_card" {{ $material->type == 'greeting_card' ? 'selected' : '' }}>Kartu Ucapan</option>
                    <option value="accessory" {{ $material->type == 'accessory' ? 'selected' : '' }}>Aksesoris</option>
                    <option value="packaging" {{ $material->type == 'packaging' ? 'selected' : '' }}>Packaging</option>
                    <option value="service" {{ $material->type == 'service' ? 'selected' : '' }}>Jasa Rangkai</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="unit" value="{{ old('unit', $material->unit) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Dasar / Modal (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="price" value="{{ old('price', $material->price) }}" required min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
                <p class="text-xs text-gray-500 mt-1">Harga modal atau harga dasar bahan.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Batangan (Rp)</label>
                <input type="number" name="price_stem" value="{{ old('price_stem', $material->price_stem ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500" placeholder="Contoh: 8000">
                <p class="text-xs text-gray-500 mt-1">Dipakai jika bahan dijual satuan/batangan.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Rangkaian (Rp)</label>
                <input type="number" name="price_arrangement" value="{{ old('price_arrangement', $material->price_arrangement ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500" placeholder="Contoh: 15000">
                <p class="text-xs text-gray-500 mt-1">Dipakai jika bahan masuk ke rangkaian/custom bucket.</p>
            </div>
            
            <div id="minStockGroup">
                <label class="block text-sm font-medium text-gray-700 mb-2">Batas Min. Stok <span class="text-red-500">*</span></label>
                <input type="number" name="min_stock" id="minStockInput" value="{{ old('min_stock', $material->min_stock ?? 5) }}" required min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
            </div>
            
            <div id="stockGroup">
                <label class="block text-sm font-medium text-gray-700 mb-2">Stok <span class="text-red-500">*</span></label>
                <input type="number" name="stock" id="stockInput" value="{{ old('stock', $material->stock) }}" required min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto / Gambar Material</label>
                @if($material->image)
                    <div class="mb-3">
                        <img src="{{ Storage::url($material->image) }}" class="h-32 w-auto object-cover rounded-lg border border-gray-200">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-florist-500 focus:ring-florist-500">
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto. Sangat disarankan untuk produk jadi seperti Bunga Artificial.</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $material->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-florist-600 shadow-sm focus:ring-florist-500">
                <span class="ml-2 text-sm text-gray-700">Aktif (Tersedia untuk dijual)</span>
            </label>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg shadow-sm">
                <i class="fa-solid fa-save mr-2"></i> Update Data
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const typeSelect = document.getElementById('typeSelect');
        const stockGroup = document.getElementById('stockGroup');
        const stockInput = document.getElementById('stockInput');
        const minStockGroup = document.getElementById('minStockGroup');
        const minStockInput = document.getElementById('minStockInput');

        function toggleStock() {
            if (typeSelect.value === 'service') {
                stockGroup.style.display = 'none';
                stockInput.removeAttribute('required');
                if(minStockGroup) minStockGroup.style.display = 'none';
                if(minStockInput) minStockInput.removeAttribute('required');
            } else {
                stockGroup.style.display = 'block';
                stockInput.setAttribute('required', 'required');
                if(minStockGroup) minStockGroup.style.display = 'block';
                if(minStockInput) minStockInput.setAttribute('required', 'required');
            }
        }

        typeSelect.addEventListener('change', toggleStock);
        toggleStock();
    });
</script>
@endsection