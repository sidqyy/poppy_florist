@extends('layouts.app')

@section('title', 'Edit Jasa Rangkai')
@section('page_title', 'Edit Jasa Rangkai')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            Edit Jasa Rangkai
        </h3>
        <p class="text-sm text-gray-500 mt-1">
            Perbarui aturan harga jasa rangkai.
        </p>
    </div>

    <form action="{{ route('admin.arrangement-services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Jasa</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $service->name) }}"
                           required
                           class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Minimal Item</label>
                        <input type="number"
                               name="min_item"
                               value="{{ old('min_item', $service->min_item) }}"
                               required
                               min="1"
                               class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Maksimal Item</label>
                        <input type="number"
                               name="max_item"
                               value="{{ old('max_item', $service->max_item) }}"
                               min="1"
                               placeholder="Kosongkan jika tanpa batas"
                               class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Jasa</label>
                    <input type="number"
                           name="price"
                           value="{{ old('price', (int) $service->price) }}"
                           required
                           min="0"
                           class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-pink-500 focus:ring-2 focus:ring-pink-100 outline-none">
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="is_premium"
                               value="1"
                               {{ old('is_premium', $service->is_premium) ? 'checked' : '' }}
                               class="w-5 h-5 text-pink-500 rounded">
                        <span class="font-medium text-gray-700">Premium</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-pink-500 rounded">
                        <span class="font-medium text-gray-700">Aktif</span>
                    </label>
                </div>

            </div>

            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3">
                <a href="{{ route('admin.arrangement-services.index') }}"
                   class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-pink-500 hover:bg-pink-600 text-white font-semibold shadow-sm transition">
                    <i class="fa-solid fa-save mr-2"></i>
                    Perbarui
                </button>
            </div>
        </div>
    </form>

</div>

@endsection