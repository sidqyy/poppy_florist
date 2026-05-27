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
        
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($materials as $material)
            <div class="bg-white rounded-3xl shadow-md border-2 border-gray-200 overflow-hidden flex flex-col">
                <div class="h-64 bg-gray-100 relative">
                    @if($material->image)
                    <img src="{{ Storage::url($material->image) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <i class="fa-solid fa-leaf text-6xl"></i>
                    </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-blue-500 shadow-sm">
                        Stok: {{ $material->stock }} {{ $material->unit }}
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-gray-800 mb-1 leading-tight">{{ $material->name }}</h3>
                    <p class="text-florist-500 font-extrabold text-2xl mb-4 mt-auto">Rp {{ number_format($material->price, 0, ',', '.') }}<span class="text-sm text-gray-400 font-medium">/{{ $material->unit }}</span></p>
                    
                    <form action="{{ route('pos.cart.add-material') }}" method="POST">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $material->id }}">
                        @if($material->stock > 0)
                        <button type="submit" class="touch-btn w-full py-4 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg">
                            <i class="fa-solid fa-cart-plus"></i> Tambah Eceran
                        </button>
                        @else
                        <button disabled type="button" class="w-full py-4 bg-gray-100 text-gray-400 font-bold rounded-2xl flex items-center justify-center gap-2 text-lg cursor-not-allowed">
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
