@extends('layouts.app')

@section('title', 'Edit Pesanan Online')
@section('page_title', 'Edit Pesanan Online #' . $order->order_number)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Form Edit Pesanan Online</h3>
        <p class="text-gray-500 text-sm mt-1">Mengubah data pesanan yang sudah ada.</p>
    </div>
</div>

<form action="{{ route('orders.online.update', $order->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 space-y-6">

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fa-solid fa-user text-florist-600 mr-2"></i> Informasi Pelanggan
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nomor Pesanan</label>
                        <input type="text" value="{{ $order->order_number }}" disabled class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-500 outline-none font-bold">
                        <p class="text-xs text-gray-500 mt-1">Nomor pesanan tidak dapat diubah.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Pemesan <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name', $order->customer_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">No. WhatsApp</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fa-solid fa-basket-shopping text-florist-600 mr-2"></i> Detail Pesanan
                </h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Produk yang Dipesan <span class="text-red-500">*</span></label>
                        <input type="text" name="product_name" required value="{{ old('product_name', $order->product_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Harga Kesepakatan (Rp) <span class="text-red-500">*</span></label>
                            @php
                                $basePrice = $order->total_amount - $order->delivery_fee + $order->discount;
                            @endphp
                            <input type="number" name="total_price" id="total_price" required min="0" value="{{ old('total_price', $basePrice) }}" readonly class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 outline-none font-bold">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Foto Referensi Desain</label>
                            @if($order->reference_image)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/'.$order->reference_image) }}" target="_blank" class="text-blue-500 text-xs font-bold hover:underline"><i class="fa-solid fa-image"></i> Lihat Foto Saat Ini</a>
                                </div>
                            @endif
                            <input type="file" name="reference_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-florist-50 file:text-florist-700 hover:file:bg-florist-100 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto referensi.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Rincian Tambahan <span class="text-red-500">*</span></label>
                        <textarea name="notes" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <label class="block text-sm font-bold text-gray-800">Komponen / Material Pesanan</label>
                                <p class="text-xs text-gray-500">Ubah komponen bunga jika diperlukan.</p>
                            </div>

                            <button type="button" onclick="addComponentRow()" class="px-3 py-2 bg-florist-100 text-florist-700 rounded-lg text-sm font-bold hover:bg-florist-200">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah
                            </button>
                        </div>

                        <div id="components_container" class="space-y-3">
                            @php
                                $orderItem = $order->items->first();
                                $existingComponents = $orderItem ? $orderItem->components : [];
                                $compIndex = 0;
                            @endphp

                            @if(count($existingComponents) > 0)
                                @foreach($existingComponents as $comp)
                                    <div class="component-row grid grid-cols-12 gap-2">
                                        <div class="col-span-4">
                                            <select name="components[{{ $compIndex }}][material_id]" onchange="toggleColorInput(this); calculateTotalPrice()" class="material-select component-material w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                                <option value="">Pilih Material</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}" 
                                                            data-type="{{ $material->type }}"
                                                            data-price="{{ $material->price ?? 0 }}"
                                                            data-price-arrangement="{{ $material->price_arrangement ?? 0 }}"
                                                            data-price-stem="{{ $material->price_stem ?? 0 }}"
                                                            {{ $comp->material_id == $material->id ? 'selected' : '' }}>
                                                        {{ $material->name }} - Stok: {{ $material->stock }} {{ $material->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-span-2">
                                            <select name="components[{{ $compIndex }}][price_type]" onchange="calculateTotalPrice()" class="component-price-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                                <option value="arrangement" {{ (isset($comp->material) && $comp->unit_price == $comp->material->price_arrangement) || !isset($comp->material) ? 'selected' : '' }}>Rangkaian</option>
                                                <option value="stem" {{ (isset($comp->material) && $comp->unit_price == $comp->material->price_stem && $comp->unit_price != 0) ? 'selected' : '' }}>Batangan</option>
                                            </select>
                                        </div>

                                        <div class="col-span-3">
                                            <input type="text" name="components[{{ $compIndex }}][color]" value="{{ $comp->color }}" placeholder="Warna bunga" class="component-color {{ ($comp->material && $comp->material->type === 'flower_fresh') ? '' : 'hidden' }} w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                        </div>

                                        <div class="col-span-2">
                                            <input type="number" name="components[{{ $compIndex }}][qty]" min="1" value="{{ $comp->qty }}" placeholder="Qty" oninput="calculateTotalPrice()" class="component-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                        </div>

                                        <div class="col-span-1">
                                            <button type="button" onclick="removeComponentRow(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @php $compIndex++; @endphp
                                @endforeach
                            @else
                                <div class="component-row grid grid-cols-12 gap-2">
                                    <div class="col-span-4">
                                        <select name="components[0][material_id]" onchange="toggleColorInput(this); calculateTotalPrice()" class="material-select component-material w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                            <option value="">Pilih Material</option>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}" 
                                                        data-type="{{ $material->type }}"
                                                        data-price="{{ $material->price ?? 0 }}"
                                                        data-price-arrangement="{{ $material->price_arrangement ?? 0 }}"
                                                        data-price-stem="{{ $material->price_stem ?? 0 }}">
                                                    {{ $material->name }} - Stok: {{ $material->stock }} {{ $material->unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-2">
                                        <select name="components[0][price_type]" onchange="calculateTotalPrice()" class="component-price-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                            <option value="arrangement">Rangkaian</option>
                                            <option value="stem">Batangan</option>
                                        </select>
                                    </div>

                                    <div class="col-span-3">
                                        <input type="text" name="components[0][color]" placeholder="Warna bunga" class="component-color hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                    </div>

                                    <div class="col-span-2">
                                        <input type="number" name="components[0][qty]" min="1" placeholder="Qty" oninput="calculateTotalPrice()" class="component-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                                    </div>

                                    <div class="col-span-1">
                                        <button type="button" onclick="removeComponentRow(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @php $compIndex = 1; @endphp
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Kartu Ucapan</label>
                        <textarea name="greeting_card" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('greeting_card', $order->greeting_card) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fa-solid fa-truck-fast text-florist-600 mr-2"></i> Jadwal & Pengiriman
                </h4>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Jadwal Kirim / Ambil <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_at" required value="{{ old('scheduled_at', $order->scheduled_at ? \Carbon\Carbon::parse($order->scheduled_at)->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Metode <span class="text-red-500">*</span></label>
                            <select name="delivery_method" id="delivery_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" onchange="toggleDeliveryFields()">
                                <option value="pickup" {{ old('delivery_method', $order->delivery_method) == 'pickup' ? 'selected' : '' }}>Ambil di Toko</option>
                                <option value="delivery" {{ old('delivery_method', $order->delivery_method) == 'delivery' ? 'selected' : '' }}>Diantar Kurir</option>
                            </select>
                        </div>
                    </div>

                    <div id="delivery_fields" class="hidden space-y-4 border-t border-gray-100 pt-4 mt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Nama Penerima</label>
                                <input type="text" name="recipient_name" value="{{ old('recipient_name', $order->recipient_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Telp Penerima</label>
                                <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $order->recipient_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Alamat Lengkap Pengantaran</label>
                            <textarea name="delivery_address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Jarak (KM)</label>
                                <div class="flex gap-2">
                                    <input type="number" name="delivery_distance" id="delivery_distance" step="0.1" min="0" value="{{ old('delivery_distance', $order->delivery_distance) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                                    <button type="button" onclick="calculateOngkir()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg font-bold hover:bg-blue-200 transition-colors shrink-0 text-sm">
                                        Hitung
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Ongkir (Rp)</label>
                                <input type="number" name="delivery_fee" id="delivery_fee" value="{{ old('delivery_fee', $order->delivery_fee) }}" readonly class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 outline-none font-bold">
                            </div>
                        </div>

                        <div id="ongkir_message" class="text-xs font-bold text-red-500 mt-1 hidden"></div>

                        <div class="grid grid-cols-2 gap-4 hidden">
                            <input type="text" name="delivery_lat" id="delivery_lat" value="{{ old('delivery_lat', $order->delivery_lat) }}">
                            <input type="text" name="delivery_lng" id="delivery_lng" value="{{ old('delivery_lng', $order->delivery_lng) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 text-lg">
                    <i class="fa-solid fa-save mr-2"></i> Update Pesanan
                </button>
                <a href="{{ route('orders.show', $order->id) }}" class="block text-center w-full py-3 mt-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-colors text-sm">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

<script>
    let componentIndex = {{ max(1, $compIndex) }};

    function addComponentRow() {
        const container = document.getElementById('components_container');

        const row = document.createElement('div');
        row.className = 'component-row grid grid-cols-12 gap-2';

        row.innerHTML = `
            <div class="col-span-4">
                <select name="components[${componentIndex}][material_id]" onchange="toggleColorInput(this); calculateTotalPrice()" class="material-select component-material w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                    <option value="">Pilih Material</option>
                    @foreach ($materials as $material)
                        <option value="{{ $material->id }}" 
                                data-type="{{ $material->type }}"
                                data-price="{{ $material->price ?? 0 }}"
                                data-price-arrangement="{{ $material->price_arrangement ?? 0 }}"
                                data-price-stem="{{ $material->price_stem ?? 0 }}">
                            {{ $material->name }} - Stok: {{ $material->stock }} {{ $material->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2">
                <select name="components[${componentIndex}][price_type]" onchange="calculateTotalPrice()" class="component-price-type w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
                    <option value="arrangement">Rangkaian</option>
                    <option value="stem">Batangan</option>
                </select>
            </div>

            <div class="col-span-3">
                <input type="text" name="components[${componentIndex}][color]" placeholder="Warna bunga" class="component-color hidden w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
            </div>

            <div class="col-span-2">
                <input type="number" name="components[${componentIndex}][qty]" min="1" placeholder="Qty" oninput="calculateTotalPrice()" class="component-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm">
            </div>

            <div class="col-span-1">
                <button type="button" onclick="removeComponentRow(this)" class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(row);
        componentIndex++;
    }

    function removeComponentRow(button) {
        const rows = document.querySelectorAll('.component-row');

        if (rows.length > 1) {
            button.closest('.component-row').remove();
            calculateTotalPrice();
        }
    }

    function calculateTotalPrice() {
        let total = 0;
        const rows = document.querySelectorAll('.component-row');
        
        rows.forEach(row => {
            const materialSelect = row.querySelector('.component-material');
            const priceTypeSelect = row.querySelector('.component-price-type');
            const qtyInput = row.querySelector('.component-qty');
            
            if (materialSelect && priceTypeSelect && qtyInput) {
                const selectedOption = materialSelect.options[materialSelect.selectedIndex];
                const priceType = priceTypeSelect.value;
                const qty = parseFloat(qtyInput.value) || 0;
                
                if (selectedOption && selectedOption.value !== "") {
                    let price = 0;
                    if (priceType === 'arrangement') {
                        price = parseFloat(selectedOption.getAttribute('data-price-arrangement')) || 0;
                    } else {
                        price = parseFloat(selectedOption.getAttribute('data-price-stem')) || 0;
                    }

                    if (price === 0) {
                        price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    }
                    
                    total += (price * qty);
                }
            }
        });
        
        document.getElementById('total_price').value = total;
    }

    function toggleColorInput(select) {
        const row = select.closest('.component-row');
        const colorInput = row.querySelector('.component-color');
        const selectedOption = select.options[select.selectedIndex];
        if(!selectedOption) return;
        
        const materialType = selectedOption.getAttribute('data-type');

        if (materialType === 'flower_fresh') {
            colorInput.classList.remove('hidden');
        } else {
            colorInput.classList.add('hidden');
            colorInput.value = '';
        }
    }

    function toggleDeliveryFields() {
        const method = document.getElementById('delivery_method').value;
        const fields = document.getElementById('delivery_fields');

        if (method === 'delivery') {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
            document.getElementById('delivery_fee').value = '';
            document.getElementById('delivery_distance').value = '';
            document.getElementById('ongkir_message').classList.add('hidden');
        }
    }

    function calculateOngkir() {
        const distance = document.getElementById('delivery_distance').value;
        if (!distance) return;

        const msg = document.getElementById('ongkir_message');
        msg.classList.add('hidden');

        fetch(`/api/calculate-ongkir?distance=${distance}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    msg.textContent = data.message;
                    msg.classList.remove('hidden');
                    document.getElementById('delivery_fee').value = 0;
                } else {
                    document.getElementById('delivery_fee').value = data.fee;
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleDeliveryFields();
    });
</script>
@endsection
