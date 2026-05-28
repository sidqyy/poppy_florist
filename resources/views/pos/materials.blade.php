@extends('layouts.pos')

@section('content')
<div class="flex h-full w-full bg-gray-50">
    <div class="flex-1 p-6 overflow-y-auto" style="padding-bottom: 100px;">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('pos.index') }}" class="w-12 h-12 bg-white border border-gray-200 hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-600 transition-colors shadow-sm touch-btn" title="Kembali ke Menu Utama">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-3xl font-bold text-gray-800">{{ $title }}</h2>
        </div>
        
        <div class="flex flex-col gap-3">
            @foreach($materials as $material)
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-200 p-3.5 flex items-center gap-4 hover:border-blue-300 transition-all">
                <!-- Image or Leaf Icon Column -->
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shrink-0 relative flex items-center justify-center">
                    @if($material->image)
                    <img src="{{ Storage::url($material->image) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-2xl">
                        <i class="fa-solid {{ $type === 'flower_fresh' ? 'fa-leaf' : 'fa-seedling' }}"></i>
                    </div>
                    @endif
                </div>

                <!-- Info Column -->
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                        <h3 class="text-base font-bold text-gray-800 leading-tight truncate">{{ $material->name }}</h3>
                        <span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full text-xs font-bold">
                            Stok: {{ $material->stock }} {{ $material->unit }}
                        </span>
                    </div>
                    <p class="text-florist-500 font-extrabold text-lg">
                        Rp {{ number_format($material->price, 0, ',', '.') }}<span class="text-xs text-gray-400 font-medium">/{{ $material->unit }}</span>
                    </p>
                </div>

                <!-- Action Button Column -->
                <div class="shrink-0">
                    <form action="{{ route('pos.cart.add-material') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $material->id }}">
                        @if($material->stock > 0)
                        <button type="submit" class="touch-btn py-3 px-5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-xl flex items-center gap-2 text-sm transition-colors">
                            <i class="fa-solid fa-cart-plus"></i> Tambah Eceran
                        </button>
                        @else
                        <button disabled type="button" class="py-3 px-5 bg-gray-100 text-gray-400 font-bold rounded-xl flex items-center gap-2 text-sm cursor-not-allowed">
                            Stok Habis
                        </button>
                        @endif
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @include('pos.partials.cart')
</div>
@endsection
