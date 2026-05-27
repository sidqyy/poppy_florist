@extends('layouts.app')

@section('title', 'Edit Promo')
@section('page_title', 'Edit Promo: ' . $promo->code)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.promos.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar Promo
    </a>
</div>

<div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6">
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

    <form action="{{ route('admin.promos.update', $promo->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Informasi Dasar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $promo->code) }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none uppercase" placeholder="Contoh: VALENTINE50">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $promo->name) }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" placeholder="Contoh: Promo Valentine">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Syarat & Ketentuan</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('description', $promo->description) }}</textarea>
            </div>

            <!-- Nilai Diskon -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <option value="fixed" {{ old('type', $promo->type) == 'fixed' ? 'selected' : '' }}>Nominal Pasti (Rp)</option>
                    <option value="percentage" {{ old('type', $promo->type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Potongan <span class="text-red-500">*</span></label>
                <input type="number" name="value" value="{{ old('value', $promo->value) }}" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" placeholder="Contoh: 15000">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Belanja (Rp)</label>
                <input type="number" name="min_purchase" value="{{ old('min_purchase', $promo->min_purchase) }}" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batas Maksimal Penggunaan (Kuota)</label>
                <input type="number" name="max_uses" value="{{ old('max_uses', $promo->max_uses) }}" min="1" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" placeholder="Kosongkan jika tanpa batas">
            </div>

            <!-- Masa Berlaku -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Berlakunya Promo</label>
                <input type="datetime-local" name="start_date" value="{{ old('start_date', $promo->start_date ? $promo->start_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir Promo</label>
                <input type="datetime-local" name="end_date" value="{{ old('end_date', $promo->end_date ? $promo->end_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>
            
            <div class="md:col-span-2 pt-4 border-t border-gray-100">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promo->is_active) ? 'checked' : '' }} class="w-5 h-5 text-florist-500 rounded focus:ring-florist-400">
                    <span class="text-gray-700 font-medium">Aktifkan Promo Ini Sekarang</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg shadow-sm transition-transform active:scale-95">
                <i class="fa-solid fa-save mr-2"></i> Update Promo
            </button>
        </div>
    </form>
</div>
@endsection
