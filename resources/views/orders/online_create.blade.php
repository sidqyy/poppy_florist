@extends('layouts.app')

@section('title', 'Input Pesanan Online')
@section('page_title', 'Input Pesanan Online (Dari WA/IG)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Form Pesanan Online</h3>
        <p class="text-gray-500 text-sm mt-1">Teruskan permintaan pelanggan ke Dapur Florist.</p>
    </div>
</div>

<form action="{{ route('orders.online.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                        <label class="block text-sm font-bold text-gray-800 mb-1">Prefix / Sumber Pesanan</label>
                        <select name="order_prefix" id="order_prefix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold" onchange="toggleManualOrderField()">
                            <option value="PESW" {{ old('order_prefix') == 'PESW' ? 'selected' : '' }}>PESW - Pesanan Web</option>
                            <option value="PESM" {{ old('order_prefix') == 'PESM' ? 'selected' : '' }}>PESM - Pesanan Marketing (Lebih dari 3 jam)</option>
                            <option value="PJLM" {{ old('order_prefix') == 'PJLM' ? 'selected' : '' }}>PJLM - Pesanan Marketing Kilat (Kurang dari 3 jam)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="prefix_hint">
                            Nomor urut (misal: 001, 002) akan otomatis dibuatkan oleh sistem di belakang Prefix ini.
                        </p>
                    </div>

                    <div class="md:col-span-2 hidden" id="manual_order_container">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nomor Order Manual (dari Web) <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 font-bold">
                                PESW-
                            </span>
                            <input type="text" name="manual_order_number" id="manual_order_number" value="{{ old('manual_order_number') }}" placeholder="Contoh: 12345" class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Masukkan nomor order unik dari website Anda tanpa mengetik PESW-.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Pemesan <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">No. WhatsApp</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fa-solid fa-basket-shopping text-florist-600 mr-2"></i> Detail Pesanan
                </h4>

                <div class="space-y-4">
                    <div class="relative" id="product_autocomplete_wrapper">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Produk yang Dipesan <span class="text-red-500">*</span></label>
                        <input type="text" name="product_name" id="product_name" autocomplete="off" required value="{{ old('product_name') }}" placeholder="Ketik nama produk atau varian..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        
                        <!-- Custom Dropdown Container -->
                        <div id="product_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                            <!-- Items will be injected here by JS -->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Harga Kesepakatan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_price" id="total_price" required min="0" value="{{ old('total_price') ?? 0 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Foto Referensi Desain</label>
                            <input type="file" name="reference_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-florist-50 file:text-florist-700 hover:file:bg-florist-100 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Rincian Tambahan <span class="text-red-500">*</span></label>
                        <textarea name="notes" rows="3" required placeholder="Contoh: Kertas wrap warna hitam, pita emas..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('notes') }}</textarea>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <label class="block text-sm font-bold text-gray-800">Komponen / Material Pesanan</label>
                                <p class="text-xs text-gray-500">Field warna hanya muncul jika material bertipe fresh flowers.</p>
                            </div>

                            <button type="button" onclick="addComponentRow()" class="px-3 py-2 bg-florist-100 text-florist-700 rounded-lg text-sm font-bold hover:bg-florist-200">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah
                            </button>
                        </div>

                        <div id="components_container" class="space-y-3">
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
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Kartu Ucapan</label>
                        <textarea name="greeting_card" rows="2" placeholder="Tulis ucapan yang akan diprint/ditulis..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('greeting_card') }}</textarea>
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
                            <input type="datetime-local" name="scheduled_at" required value="{{ old('scheduled_at') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Metode <span class="text-red-500">*</span></label>
                            <select name="delivery_method" id="delivery_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" onchange="toggleDeliveryFields()">
                                <option value="pickup" {{ old('delivery_method') == 'pickup' ? 'selected' : '' }}>Ambil di Toko</option>
                                <option value="delivery" {{ old('delivery_method') == 'delivery' ? 'selected' : '' }}>Diantar Kurir</option>
                            </select>
                        </div>
                    </div>

                    <div id="delivery_fields" class="hidden space-y-4 border-t border-gray-100 pt-4 mt-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Nama Penerima</label>
                                <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Telp Penerima</label>
                                <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Alamat Lengkap Pengantaran</label>
                            <textarea name="delivery_address" rows="2" placeholder="Tuliskan alamat pengiriman..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('delivery_address') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Jarak (KM)</label>
                                <div class="flex gap-2">
                                    <input type="number" name="delivery_distance" id="delivery_distance" step="0.1" min="0" value="{{ old('delivery_distance') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                                    <button type="button" onclick="calculateOngkir()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg font-bold hover:bg-blue-200 transition-colors shrink-0 text-sm">
                                        Hitung
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Ongkir (Rp)</label>
                                <input type="number" name="delivery_fee" id="delivery_fee" value="{{ old('delivery_fee') }}" readonly class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 outline-none font-bold">
                            </div>
                        </div>

                        <div id="ongkir_message" class="text-xs font-bold text-red-500 mt-1 hidden"></div>

                        <div class="grid grid-cols-2 gap-4 hidden">
                            <input type="text" name="delivery_lat" id="delivery_lat">
                            <input type="text" name="delivery_lng" id="delivery_lng">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fa-solid fa-wallet text-florist-600 mr-2"></i> Pembayaran
                </h4>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Pembayaran</label>
                        <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-800 outline-none focus:border-florist-400">
                            <option value="paid_qris" {{ old('payment_status') == 'paid_qris' ? 'selected' : '' }}>Lunas QRIS</option>
                            <option value="paid_tf" {{ old('payment_status') == 'paid_tf' ? 'selected' : '' }}>Lunas TF</option>
                            <option value="dp" {{ old('payment_status') == 'dp' ? 'selected' : '' }}>DP</option>
                            <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nominal Dibayar (Rp)</label>
                        <input type="number" name="initial_payment" min="0" value="{{ old('initial_payment') ?? 0 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        <p class="text-xs text-gray-500 mt-1">Isi 0 jika Belum Bayar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Upload Bukti Transfer</label>
                        <input type="file" name="payment_proof" accept="image/*,.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 text-sm">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 text-lg">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Simpan Pesanan
                </button>

                <button type="button" onclick="playBellSound(); playVoiceNotification();" class="w-full py-2 mt-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-medium transition">
                    <i class="fa-solid fa-volume-high mr-2"></i> Test Notifikasi
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    let componentIndex = 1;

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
        const productNameInput = document.getElementById('product_name');
        if (productNameInput && productNameInput.value.toLowerCase().includes('bunga papan')) {
            return; // Jangan hitung/ubah harga dari komponen jika produk adalah Bunga Papan
        }

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

    function toggleManualOrderField() {
        const prefix = document.getElementById('order_prefix').value;
        const container = document.getElementById('manual_order_container');
        const input = document.getElementById('manual_order_number');
        const hint = document.getElementById('prefix_hint');

        if (prefix === 'PESW') {
            container.classList.remove('hidden');
            input.setAttribute('required', 'required');
            hint.textContent = 'Nomor order akan disinkronkan menggunakan nomor manual yang diinput di bawah.';
        } else {
            container.classList.add('hidden');
            input.removeAttribute('required');
            hint.textContent = 'Nomor urut (misal: 001, 002) akan otomatis dibuatkan oleh sistem di belakang Prefix ini.';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleManualOrderField();
        toggleDeliveryFields();

        document.querySelectorAll('.material-select').forEach(select => {
            toggleColorInput(select);
        });

        // Catalog Products Data
        const catalogProducts = [
            @foreach($products as $product)
                @if($product->sizes && $product->sizes->count() > 0)
                    @foreach($product->sizes as $size)
                        @if($size->variants && $size->variants->count() > 0)
                            @foreach($size->variants as $variant)
                                { name: "{{ $product->name }} - {{ $size->size_name }} - {{ $variant->variant_name }}", price: {{ $variant->price }} },
                            @endforeach
                        @else
                            { name: "{{ $product->name }} - {{ $size->size_name }}", price: {{ $product->total_price }} },
                        @endif
                    @endforeach
                @else
                    { name: "{{ $product->name }}", price: {{ $product->total_price }} },
                @endif
            @endforeach
        ];

        const productNameInput = document.getElementById('product_name');
        const productDropdown = document.getElementById('product_dropdown');
        const wrapper = document.getElementById('product_autocomplete_wrapper');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function renderDropdown(items) {
            productDropdown.innerHTML = '';
            if (items.length === 0) {
                productDropdown.classList.add('hidden');
                return;
            }

            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'px-4 py-3 hover:bg-florist-50 cursor-pointer border-b last:border-b-0 flex justify-between items-center transition-colors';
                div.innerHTML = `
                    <div class="font-medium text-gray-800 text-sm">${item.name}</div>
                    <div class="text-florist-600 font-bold text-sm bg-florist-50 px-2 py-1 rounded">${formatRupiah(item.price)}</div>
                `;
                
                div.addEventListener('mousedown', function(e) {
                    e.preventDefault(); // Prevent blur
                    productNameInput.value = item.name;
                    document.getElementById('total_price').value = item.price;
                    productDropdown.classList.add('hidden');
                });
                
                productDropdown.appendChild(div);
            });
            
            productDropdown.classList.remove('hidden');
        }

        if(productNameInput) {
            productNameInput.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                if (!val) {
                    productDropdown.classList.add('hidden');
                    return;
                }
                const filtered = catalogProducts.filter(p => p.name.toLowerCase().includes(val));
                renderDropdown(filtered);
            });

            productNameInput.addEventListener('focus', function() {
                const val = this.value.toLowerCase();
                const filtered = val ? catalogProducts.filter(p => p.name.toLowerCase().includes(val)) : catalogProducts;
                if(filtered.length > 0) renderDropdown(filtered);
            });

            productNameInput.addEventListener('blur', function() {
                productDropdown.classList.add('hidden');
            });
        }
    });

    function playBellSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();

            const playNote = (frequency, startTime, duration, type = 'sine') => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.value = frequency;
                gain.gain.setValueAtTime(0, startTime);
                gain.gain.linearRampToValueAtTime(0.2, startTime + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(startTime);
                osc.stop(startTime + duration);
            };

            playNote(1046.50, ctx.currentTime, 1.0, 'sine');
            playNote(1318.51, ctx.currentTime + 0.1, 1.2, 'sine');
            playNote(1567.98, ctx.currentTime + 0.2, 1.8, 'sine');
        } catch (e) {}
    }

    function playVoiceNotification() {
        try {
            const url = "/pesanan_masuk.mp3";
            const audio = new Audio(url);
            audio.play().catch(() => {});
        } catch (e) {}
    }
</script>
@endsection