@extends('layouts.pos')

@section('content')
<div class="flex h-full w-full bg-gray-50">
    <div class="flex-1 p-6 overflow-y-auto" style="padding-bottom: 100px;">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('pos.index') }}" class="w-12 h-12 bg-white border border-gray-200 hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-600 transition-colors shadow-sm touch-btn" title="Kembali ke Menu Utama">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Rakitan Custom Produk</h2>
            </div>

            <form action="{{ route('pos.cart.add-custom') }}" method="POST" id="customForm" class="flex flex-col md:flex-row gap-6 items-start">
                @csrf

                <div class="flex-1 bg-white rounded-2xl p-6 shadow-md border-2 border-gray-200 w-full">

                    <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
                        <input type="hidden" name="custom_type_selector" id="custom_type_selector" value="bunga">

                        <button type="button" onclick="setCustomType('bunga')" id="tab-bunga" class="flex-1 py-3 px-4 rounded-lg text-sm font-bold bg-white text-purple-600 shadow-sm transition-all touch-btn">
                            Custom Buket Bunga
                        </button>

                        <button type="button" onclick="setCustomType('produk')" id="tab-produk" class="flex-1 py-3 px-4 rounded-lg text-sm font-bold text-gray-500 hover:text-gray-700 transition-all touch-btn">
                            Custom Buket Produk
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2 text-sm">
                            Beri Nama Rakitan Ini <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="custom_name" required autocomplete="off" placeholder="Contoh: Buket Custom Mawar Merah Kertas Hitam"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl focus:ring-2 focus:ring-purple-100 focus:border-purple-500 block p-3 outline-none font-medium">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2 text-sm">
                            Catatan Perangkaian (Opsional)
                        </label>

                        <textarea name="custom_notes" rows="2" placeholder="Contoh: Bunga mawar ditaruh di tengah, kertasnya dibentuk layer miring..." class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl focus:ring-2 focus:ring-purple-100 focus:border-purple-500 block p-3 outline-none"></textarea>
                    </div>

                    <div id="extra-section" class="hidden mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">
                            Daftar Tambahan Produk Custom
                        </h3>

                        <div id="extra-items-container" class="flex flex-col gap-3 mb-4"></div>

                        <button type="button" onclick="addExtraItem()" class="w-full py-3 bg-white hover:bg-gray-50 border-2 border-dashed border-gray-300 text-gray-600 font-bold rounded-xl shadow-sm flex items-center justify-center gap-2 touch-btn transition-colors">
                            <i class="fa-solid fa-plus"></i> Tambah Produk Custom Lainnya
                        </button>
                    </div>

                    @php
                        $typeLabels = [
                            'flower_fresh' => 'Bunga Asli',
                            'flower_artificial' => 'Bunga Artificial',
                            'wrapping' => 'Kertas Wrapping',
                            'ribbon' => 'Pita (Ribbon)',
                            'doll' => 'Boneka',
                            'greeting_card' => 'Kartu Ucapan',
                            'accessory' => 'Aksesoris Lainnya',
                            'packaging' => 'Packaging (Kardus/Paperbag)',
                            'service' => 'Jasa Perangkaian',
                        ];

                        $typeIcons = [
                            'flower_fresh' => 'fa-leaf',
                            'flower_artificial' => 'fa-seedling',
                            'wrapping' => 'fa-scroll',
                            'ribbon' => 'fa-ribbon',
                            'doll' => 'fa-snowman',
                            'greeting_card' => 'fa-envelope-open-text',
                            'accessory' => 'fa-gem',
                            'packaging' => 'fa-box',
                            'service' => 'fa-hands-holding',
                        ];

                        $step = 1;
                    @endphp

                    @foreach($groupedMaterials as $type => $items)
                        @if($type === 'service')
                            @continue
                        @endif

                        <h3 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">
                            {{ $step++ }}. Pilih {{ $typeLabels[$type] ?? ucwords(str_replace('_', ' ', $type)) }}
                        </h3>

                        <div class="flex flex-col gap-3 mb-6">
                            @foreach($items as $item)
                                @php
                                    $customPrice = ($item->price_arrangement ?? 0) > 0
                                        ? $item->price_arrangement
                                        : $item->price;
                                @endphp

                                <div class="border rounded-xl p-2.5 flex items-center gap-3 {{ $item->stock <= 0 ? 'opacity-50 bg-gray-50' : 'hover:border-purple-300 transition-colors' }}">
                                    @if($item->image)
                                        <img src="{{ Storage::url($item->image) }}" class="w-12 h-12 object-cover rounded-lg shadow-sm shrink-0">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 shadow-sm text-xl shrink-0">
                                            <i class="fa-solid {{ $typeIcons[$type] ?? 'fa-box-open' }}"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 text-left min-w-0">
                                        <h4 class="font-bold text-gray-800 text-sm leading-tight truncate">
                                            {{ $item->name }}
                                        </h4>

                                        <p class="text-purple-600 font-bold text-sm">
                                            Rp {{ number_format($customPrice, 0, ',', '.') }}
                                            <span class="text-xs text-gray-400">/{{ $item->unit }}</span>
                                        </p>
                                    </div>

                                    @if($item->stock > 0)
                                        <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 w-24 justify-between shrink-0">
                                            <button type="button" onclick="adjustQty('mat_{{ $item->id }}', -1)" class="w-7 h-7 rounded bg-white shadow-sm flex items-center justify-center text-gray-600 font-bold touch-btn">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>

                                            <input
                                                type="number"
                                                name="materials[{{ $item->id }}]"
                                                id="mat_{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-price="{{ $customPrice }}"
                                                data-type="{{ $item->type }}"
                                                value="0"
                                                min="0"
                                                max="{{ $item->stock }}"
                                                class="w-6 bg-transparent text-center font-bold outline-none text-sm p-0 border-none focus:ring-0 custom-mat-input"
                                                readonly>

                                            <button type="button" onclick="adjustQty('mat_{{ $item->id }}', 1, {{ $item->stock }})" class="w-7 h-7 rounded bg-white shadow-sm flex items-center justify-center text-purple-600 font-bold touch-btn">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-red-500 font-bold text-xs shrink-0 px-2">Habis</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>

                <div class="w-full md:w-80 shrink-0 bg-white rounded-2xl p-5 shadow-md border-2 border-gray-200 sticky top-6">
                    <h3 class="font-bold text-gray-800 mb-4 text-lg">
                        <i class="fa-solid fa-clipboard-list text-purple-500 mr-2"></i>
                        Ringkasan Rakitan
                    </h3>

                    <div id="custom-summary-box" class="mb-4 hidden">
                        <div id="summary-items" class="text-sm text-gray-600 mb-3 flex flex-col gap-2"></div>

                        <div class="border-t border-gray-200 pt-3 flex justify-between items-center font-bold text-gray-800">
                            <span>Estimasi Total:</span>
                            <span id="summary-total" class="text-purple-700 text-lg">Rp 0</span>
                        </div>
                    </div>

                    <div id="empty-summary" class="text-sm text-gray-400 mb-4 text-center py-4 border-2 border-dashed border-gray-200 rounded-xl">
                        Belum ada bahan yang dipilih.
                    </div>

                    <div class="mb-6 p-4 bg-purple-50 border border-purple-100 rounded-xl">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                name="is_premium_service"
                                id="isPremiumService"
                                value="1"
                                onchange="updateSummary()"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">

                            <span class="text-sm font-bold text-gray-700">
                                Gunakan Jasa Rangkai Premium
                            </span>
                        </label>

                        <p class="text-xs text-gray-400 mt-1">
                            Jika dicentang, sistem memakai harga jasa premium otomatis sesuai jumlah bunga asli.
                        </p>
                    </div>

                    <button type="submit" id="submit-custom-btn" class="w-full py-3 bg-gray-400 text-white font-bold rounded-xl shadow-sm flex flex-col items-center justify-center gap-1 touch-btn cursor-not-allowed transition-all" disabled>
                        <span>Masukkan ke Keranjang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('pos.partials.cart')
</div>

<script>
const arrangementServices = {!! json_encode(
    collect($arrangementServices ?? [])->map(function ($service) {
        return [
            'name' => $service->name,
            'min_item' => (int) $service->min_item,
            'max_item' => $service->max_item ? (int) $service->max_item : null,
            'price' => (int) $service->price,
            'is_premium' => (bool) $service->is_premium,
        ];
    })->values()
) !!};

function getArrangementService(itemCount, isPremium) {
    return arrangementServices.find(function(service) {
        const minOk = itemCount >= service.min_item;
        const maxOk = service.max_item === null || itemCount <= service.max_item;
        const premiumOk = service.is_premium === isPremium;

        return minOk && maxOk && premiumOk;
    });
}

function adjustQty(id, amount, max = null) {
    const input = document.getElementById(id);

    if (!input) return;

    let current = parseInt(input.value) || 0;
    let next = current + amount;

    if (next < 0) next = 0;
    if (max !== null && next > max) next = max;

    input.value = next;
    updateSummary();
}

function updateSummary() {
    let itemsHtml = '';
    let total = 0;
    let count = 0;
    let freshFlowerCount = 0;

    document.querySelectorAll('.custom-mat-input').forEach(function(input) {
        const qty = parseInt(input.value) || 0;

        if (qty > 0) {
            const name = input.getAttribute('data-name');
            const price = parseFloat(input.getAttribute('data-price')) || 0;
            const type = input.getAttribute('data-type');
            const subtotal = qty * price;

            total += subtotal;
            count += qty;

            if (type === 'flower_fresh') {
                freshFlowerCount += qty;
            }

            itemsHtml += `
                <div class="flex justify-between items-start">
                    <span>${qty}x ${name}</span>
                    <span class="font-semibold text-gray-800 whitespace-nowrap">Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
            `;
        }
    });

    if (document.getElementById('custom_type_selector').value === 'produk') {
        document.querySelectorAll('#extra-items-container > div').forEach(function(row) {
            const nameInput = row.querySelector('.extra-name-input');
            const priceInput = row.querySelector('.extra-price-input');
            const qtyInput = row.querySelector('.extra-qty-input');

            const name = nameInput.value.trim() || 'Produk Tambahan';
            const price = parseFloat(priceInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;

            if (qty > 0 && nameInput.value.trim().length > 0 && !isNaN(price) && priceInput.value.length > 0) {
                const subtotal = qty * price;

                total += subtotal;
                count += qty;

                itemsHtml += `
                    <div class="flex justify-between items-start">
                        <span class="text-purple-600">${qty}x ${name}</span>
                        <span class="font-semibold text-gray-800 whitespace-nowrap">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
            }
        });
    }

    const premiumCheckbox = document.getElementById('isPremiumService');
    const isPremium = premiumCheckbox ? premiumCheckbox.checked : false;

    if (freshFlowerCount > 0) {
        const selectedService = getArrangementService(freshFlowerCount, isPremium);

        if (selectedService) {
            total += selectedService.price;

            itemsHtml += `
                <div class="flex justify-between items-start border-t border-gray-200 pt-2 mt-2">
                    <span class="font-bold text-purple-600">
                        1x Jasa Rangkai ${selectedService.name}
                        <span class="block text-xs text-gray-400 font-medium">
                            Berdasarkan ${freshFlowerCount} bunga asli
                        </span>
                    </span>
                    <span class="font-bold text-purple-700 whitespace-nowrap">
                        Rp ${selectedService.price.toLocaleString('id-ID')}
                    </span>
                </div>
            `;
        }
    }

    const summaryBox = document.getElementById('custom-summary-box');
    const emptySummary = document.getElementById('empty-summary');
    const summaryItems = document.getElementById('summary-items');
    const summaryTotal = document.getElementById('summary-total');
    const submitBtn = document.getElementById('submit-custom-btn');

    if (count > 0) {
        summaryBox.classList.remove('hidden');
        emptySummary.classList.add('hidden');

        summaryItems.innerHTML = itemsHtml;
        summaryTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');

        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'shadow-sm');
        submitBtn.classList.add('bg-purple-600', 'hover:bg-purple-700', 'shadow-lg');
    } else {
        summaryBox.classList.add('hidden');
        emptySummary.classList.remove('hidden');

        submitBtn.disabled = true;
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'shadow-sm');
        submitBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'shadow-lg');
    }
}

function setCustomType(type) {
    document.getElementById('custom_type_selector').value = type;

    const tabBunga = document.getElementById('tab-bunga');
    const tabProduk = document.getElementById('tab-produk');
    const extraSection = document.getElementById('extra-section');

    if (type === 'bunga') {
        tabBunga.classList.replace('text-gray-500', 'text-purple-600');
        tabBunga.classList.replace('hover:text-gray-700', 'bg-white');
        tabBunga.classList.add('bg-white', 'shadow-sm');

        tabProduk.classList.replace('text-purple-600', 'text-gray-500');
        tabProduk.classList.replace('bg-white', 'hover:text-gray-700');
        tabProduk.classList.remove('shadow-sm');

        extraSection.classList.add('hidden');
        document.getElementById('extra-items-container').innerHTML = '';
        extraIndex = 0;
    } else {
        tabProduk.classList.replace('text-gray-500', 'text-purple-600');
        tabProduk.classList.replace('hover:text-gray-700', 'bg-white');
        tabProduk.classList.add('bg-white', 'shadow-sm');

        tabBunga.classList.replace('text-purple-600', 'text-gray-500');
        tabBunga.classList.replace('bg-white', 'hover:text-gray-700');
        tabBunga.classList.remove('shadow-sm');

        extraSection.classList.remove('hidden');

        if (document.getElementById('extra-items-container').children.length === 0) {
            addExtraItem();
        }
    }

    updateSummary();
}

let extraIndex = 0;

function addExtraItem() {
    const container = document.getElementById('extra-items-container');
    const rowId = `extra_${extraIndex}`;

    const html = `
        <div id="${rowId}" class="border border-purple-100 bg-purple-50 rounded-xl p-3 flex flex-col md:flex-row items-center gap-3 relative">
            <button type="button" onclick="document.getElementById('${rowId}').remove(); updateSummary();" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-sm text-xs touch-btn">
                <i class="fa-solid fa-times"></i>
            </button>

            <div class="flex-1 w-full">
                <input type="text" name="extra_items[${extraIndex}][name]" placeholder="Nama Tambahan (Cth: Coklat)" required class="w-full bg-white border border-gray-200 text-gray-800 rounded-lg p-2 text-sm outline-none focus:border-purple-500 extra-name-input" oninput="updateSummary()">
            </div>

            <div class="w-full md:w-32">
                <input type="number" name="extra_items[${extraIndex}][price]" placeholder="Harga" required min="0" class="w-full bg-white border border-gray-200 text-gray-800 rounded-lg p-2 text-sm outline-none focus:border-purple-500 extra-price-input" oninput="updateSummary()">
            </div>

            <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-1 w-24 justify-between shrink-0">
                <button type="button" onclick="adjustExtraQty('${rowId}_qty', -1)" class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-gray-600 font-bold touch-btn">
                    <i class="fa-solid fa-minus text-xs"></i>
                </button>

                <input type="number" name="extra_items[${extraIndex}][qty]" id="${rowId}_qty" value="1" min="1" class="w-6 bg-transparent text-center font-bold outline-none text-sm p-0 border-none focus:ring-0 extra-qty-input" readonly>

                <button type="button" onclick="adjustExtraQty('${rowId}_qty', 1)" class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center text-gray-600 font-bold touch-btn">
                    <i class="fa-solid fa-plus text-xs"></i>
                </button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    extraIndex++;
    updateSummary();
}

function adjustExtraQty(id, amount) {
    const input = document.getElementById(id);

    if (!input) return;

    let current = parseInt(input.value) || 0;
    let next = current + amount;

    if (next < 1) next = 1;

    input.value = next;
    updateSummary();
}
</script>
@endsection