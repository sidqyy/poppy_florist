@extends('layouts.app')

@section('title', 'Input Mutasi Stok Manual')
@section('page_title', 'Mutasi Stok Manual')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.stocks.index') }}" class="text-gray-500 hover:text-florist-500"><i class="fa-solid fa-arrow-left mr-2"></i> Kembali</a>
</div>

<div class="card-modern p-6 max-w-2xl mx-auto">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h4 class="text-blue-800 font-bold mb-1"><i class="fa-solid fa-circle-info mr-2"></i> Informasi</h4>
        <p class="text-sm text-blue-700">Fitur ini digunakan untuk menambah (Restock) atau mengurangi (Rusak/Layu/Hilang) stok bahan baku secara manual di luar transaksi penjualan.</p>
    </div>

    <h3 class="text-2xl font-bold text-gray-800 mb-6">Input Mutasi Stok</h3>

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
    
    <form action="{{ route('admin.stocks.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
            <select name="material_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                <option value="">-- Pilih Bahan Baku --</option>
                @foreach($materials as $mat)
                    <option value="{{ $mat->id }}">{{ $mat->name }} (Sisa: {{ $mat->stock }} {{ $mat->unit }})</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Mutasi <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <option value="in">MASUK (Penambahan Stok)</option>
                    <option value="out">KELUAR (Pengurangan Stok)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="qty" value="{{ old('qty') }}" min="1" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan</label>
            <textarea name="notes" rows="3" placeholder="Contoh: Barang datang dari supplier X, atau Bunga mawar layu dibuang..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('notes') }}</textarea>
        </div>
        
        <div class="flex justify-end border-t border-gray-100 pt-6">
            <button type="submit" class="px-6 py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg shadow-sm font-bold">
                Simpan Mutasi
            </button>
        </div>
    </form>
</div>
@endsection
