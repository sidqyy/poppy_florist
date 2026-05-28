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
        
        <!-- Tabs Navigation -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 border-b border-gray-200 scrollbar-none" style="-ms-overflow-style: none; scrollbar-width: none;">
            @php
                $tabs = [
                    'flower_fresh' => ['label' => '🌸 Bunga Segar', 'color' => 'orange'],
                    'flower_artificial' => ['label' => '🌿 Bunga Artificial', 'color' => 'teal'],
                    'doll' => ['label' => '🧸 Boneka', 'color' => 'purple'],
                    'accessory' => ['label' => '🎀 Aksesoris', 'color' => 'pink'],
                    'packaging' => ['label' => '📦 Packaging', 'color' => 'blue'],
                    'wrapping' => ['label' => '📜 Wrapping', 'color' => 'amber'],
                    'ribbon' => ['label' => '🎗️ Pita', 'color' => 'rose'],
                    'greeting_card' => ['label' => '✉️ Kartu Ucapan', 'color' => 'indigo'],
                ];
            @endphp
            @foreach($tabs as $tabKey => $tabData)
                <a href="{{ route('pos.materials', ['type' => $tabKey]) }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all shadow-sm shrink-0 touch-btn {{ $type === $tabKey ? 'bg-blue-600 text-white shadow-blue-200 shadow-md' : 'bg-white hover:bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $tabData['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex flex-col gap-3">
            @foreach($materials as $material)
            @php
                $cartKey = 'mat_' . $material->id;
                $inCartQty = isset($cart[$cartKey]) ? $cart[$cartKey]['qty'] : 0;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border-2 {{ $inCartQty > 0 ? 'border-emerald-500 bg-emerald-50/10' : 'border-gray-200' }} p-3.5 flex items-center gap-4 hover:border-blue-300 transition-all">
                <!-- Image or Leaf Icon Column -->
                <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0 relative flex items-center justify-center shadow-sm border border-gray-100">
                    @if($material->image)
                    <img src="{{ Storage::url($material->image) }}" class="w-full h-full object-cover">
                    @else
                    @php
                        $styleMap = [
                            'flower_fresh' => ['icon' => 'fa-spa', 'bg' => 'bg-rose-50', 'text' => 'text-rose-500'],
                            'flower_artificial' => ['icon' => 'fa-seedling', 'bg' => 'bg-teal-50', 'text' => 'text-teal-500'],
                            'doll' => ['icon' => 'fa-face-smile', 'bg' => 'bg-purple-50', 'text' => 'text-purple-500'],
                            'accessory' => ['icon' => 'fa-gem', 'bg' => 'bg-pink-50', 'text' => 'text-pink-500'],
                            'packaging' => ['icon' => 'fa-box-open', 'bg' => 'bg-blue-50', 'text' => 'text-blue-500'],
                            'wrapping' => ['icon' => 'fa-scroll', 'bg' => 'bg-amber-50', 'text' => 'text-amber-500'],
                            'ribbon' => ['icon' => 'fa-ribbon', 'bg' => 'bg-red-50', 'text' => 'text-red-500'],
                            'greeting_card' => ['icon' => 'fa-envelope-open-text', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-500'],
                        ];
                        $style = $styleMap[$type] ?? ['icon' => 'fa-box-open', 'bg' => 'bg-gray-50', 'text' => 'text-gray-400'];
                    @endphp
                    <div class="w-full h-full flex items-center justify-center {{ $style['bg'] }} {{ $style['text'] }} text-3xl">
                        <i class="fa-solid {{ $style['icon'] }}"></i>
                    </div>
                    @endif
                </div>

                <!-- Info Column -->
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                        <h3 class="text-base font-bold text-gray-800 leading-tight truncate">{{ $material->name }}</h3>
                        <span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full text-xs font-bold shrink-0">
                            Stok: {{ $material->stock }} {{ $material->unit }}
                        </span>
                        @if($inCartQty > 0)
                        <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full text-xs font-black shadow-sm flex items-center gap-1 shrink-0">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Terpilih: {{ $inCartQty }} {{ $material->unit }}
                        </span>
                        @endif
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
                            @if($inCartQty > 0)
                            <button type="submit" class="touch-btn py-3 px-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl flex items-center gap-2 text-sm transition-colors shadow-md shadow-emerald-100">
                                <i class="fa-solid fa-cart-plus"></i> Tambah Lagi
                            </button>
                            @else
                            <button type="submit" class="touch-btn py-3 px-5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-xl flex items-center gap-2 text-sm transition-colors">
                                <i class="fa-solid fa-cart-plus"></i> Tambah Eceran
                            </button>
                            @endif
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
