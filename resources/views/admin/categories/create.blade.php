@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page_title', 'Tambah Kategori')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Tambah Kategori Occasion</h3>
        <p class="text-gray-500 text-sm mt-1">Masukkan data kategori baru</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow-sm">
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

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori (Occasion) <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500" placeholder="Misal: Ulang Tahun">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kategori</label>
                <input type="text" name="description" value="{{ old('description') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-florist-500 focus:ring-florist-500" placeholder="Opsional">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-florist-600 shadow-sm focus:ring-florist-500">
                <span class="ml-2 text-sm text-gray-700">Aktif</span>
            </label>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-lg shadow-sm">
                <i class="fa-solid fa-save mr-2"></i> Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
