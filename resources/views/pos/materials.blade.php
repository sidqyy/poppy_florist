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

        <!-- Form untuk memilih banyak batangan sekaligus -->
        <form action="{{ route('pos.cart.add-multiple-materials') }}" method="POST" id="materialsForm" class="flex flex-col lg:flex-row gap-6 items-start w-full">
            @csrf
            
            <!-- Kiri: Daftar Batangan / Eceran -->
            <div class="flex-1 flex flex-col gap-3 w-full">
                @foreach($materials as $material)
                @php
                    $cartKey = 'mat_' . $material->id;
                    $inCartQty = isset($cart[$cartKey]) ? $cart[$cartKey]['qty'] : 0;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border-2 {{ $inCartQty > 0 ? 'border-emerald-200 bg-emerald-50/5' : 'border-gray-200' }} p-3.5 flex items-center gap-4 hover:border-blue-300 transition-all" id="row_{{ $material->id }}">
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
                                <i class="fa-solid fa-basket-shopping text-emerald-600"></i> Di Keranjang: {{ $inCartQty }} {{ $material->unit }}
                            </span>
                            @endif
                        </div>
                        <p class="text-florist-500 font-extrabold text-lg">
                            Rp {{ number_format($material->price, 0, ',', '.') }}<span class="text-xs text-gray-400 font-medium">/{{ $material->unit }}</span>
                        </p>
                    </div>

                    <!-- Quantity Input Column -->
                    <div class="shrink-0">
                        @if($material->stock > 0)
                        <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 w-28 justify-between">
                            <button type="button" onclick="adjustQty('mat_{{ $material->id }}', -1)" class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-gray-600 font-bold touch-btn">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <input type="number" name="materials[{{ $material->id }}]" id="mat_{{ $material->id }}" 
                                   data-name="{{ $material->name }}" data-price="{{ $material->price }}" data-unit="{{ $material->unit }}"
                                   value="0" min="0" max="{{ $material->stock }}" 
                                   class="w-8 bg-transparent text-center font-black outline-none text-base p-0 border-none focus:ring-0 mat-qty-input" readonly>
                            <button type="button" onclick="adjustQty('mat_{{ $material->id }}', 1, {{ $material->stock }})" class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-blue-600 font-bold touch-btn">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        @else
                        <span class="bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-sm font-bold block text-center shrink-0">Stok Habis</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Kanan: Ringkasan Rincian Pilihan & Submit -->
            <div class="w-full lg:w-80 shrink-0 bg-white rounded-3xl p-5 shadow-md border-2 border-gray-200 sticky top-6">
                <h3 class="font-bold text-gray-800 mb-4 text-lg"><i class="fa-solid fa-clipboard-list text-blue-500 mr-2"></i> Rincian Pilihan</h3>
                
                <div id="summary-box" class="mb-4 hidden">
                    <div id="summary-items" class="text-sm text-gray-600 mb-3 flex flex-col gap-2.5">
                        <!-- Javascript akan memasukkan rincian di sini -->
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between items-center font-bold text-gray-800">
                        <span>Total Rincian:</span>
                        <span id="summary-total" class="text-blue-700 text-lg">Rp 0</span>
                    </div>
                </div>
                
                <div id="empty-summary" class="text-sm text-gray-400 mb-4 text-center py-6 border-2 border-dashed border-gray-200 rounded-2xl">
                    <i class="fa-solid fa-basket-shopping text-3xl mb-2 block text-gray-300"></i>
                    Belum ada bahan/item yang dipilih.
                </div>
    
                <!-- Submit -->
                <button type="submit" id="submit-btn" class="w-full py-4 bg-gray-300 text-white font-bold rounded-2xl shadow-sm flex flex-col items-center justify-center gap-1 touch-btn cursor-not-allowed transition-all" disabled>
                    <span>Masukkan ke Keranjang</span>
                </button>
            </div>
        </form>
    </div>

    @include('pos.partials.cart')
</div>

<script>
    function adjustQty(id, amount, max = null) {
        let input = document.getElementById(id);
        let current = parseInt(input.value) || 0;
        let next = current + amount;
        
        if (next < 0) next = 0;
        if (max !== null && next > max) next = max;
        
        input.value = next;
        
        // Highlight border baris jika dipilih secara lokal
        const matId = id.replace('mat_', '');
        const row = document.getElementById('row_' + matId);
        if (row) {
            if (next > 0) {
                row.classList.remove('border-gray-200');
                row.classList.add('border-blue-500', 'bg-blue-50/5');
            } else {
                row.classList.remove('border-blue-500', 'bg-blue-50/5');
                row.classList.add('border-gray-200');
            }
        }
        
        updateSummary();
    }

    function updateSummary() {
        let itemsHtml = '';
        let total = 0;
        let count = 0;
        
        document.querySelectorAll('.mat-qty-input').forEach(input => {
            let qty = parseInt(input.value) || 0;
            if (qty > 0) {
                let name = input.getAttribute('data-name');
                let price = parseFloat(input.getAttribute('data-price')) || 0;
                let unit = input.getAttribute('data-unit');
                let subtotal = qty * price;
                
                total += subtotal;
                count += qty;
                
                itemsHtml += `
                    <div class="flex justify-between items-start border-b border-gray-50 pb-2">
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-gray-800">${name}</span>
                            <span class="text-xs text-gray-400">${qty} ${unit} x Rp ${price.toLocaleString('id-ID')}</span>
                        </div>
                        <span class="font-black text-blue-600 whitespace-nowrap">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
            }
        });
        
        const summaryBox = document.getElementById('summary-box');
        const emptySummary = document.getElementById('empty-summary');
        const summaryItems = document.getElementById('summary-items');
        const summaryTotal = document.getElementById('summary-total');
        const submitBtn = document.getElementById('submit-btn');
        
        if (count > 0) {
            summaryBox.classList.remove('hidden');
            emptySummary.classList.add('hidden');
            
            summaryItems.innerHTML = itemsHtml;
            summaryTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
            
            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'shadow-sm');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'shadow-lg');
        } else {
            summaryBox.classList.add('hidden');
            emptySummary.classList.remove('hidden');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'shadow-sm');
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'shadow-lg');
        }
    }

    // Reset input quantities when the form is successfully submitted via AJAX
    const originalSendCartAjax = sendCartAjax;
    sendCartAjax = function(url, formData, isAddAction = false) {
        originalSendCartAjax(url, formData, isAddAction);
        
        // Reset all local quantity input values back to 0
        document.querySelectorAll('.mat-qty-input').forEach(input => {
            input.value = 0;
            // Restore row borders
            const matId = input.id.replace('mat_', '');
            const row = document.getElementById('row_' + matId);
            if (row) {
                row.classList.remove('border-blue-500', 'bg-blue-50/5');
                row.classList.add('border-gray-200');
            }
        });
        updateSummary();
    };
</script>
@endsection
