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

        <!-- Kolom Kiri: Informasi Pesanan & Pelanggan -->
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

            <!-- Informasi Pelanggan -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-user text-florist-600 mr-2"></i> Informasi Pelanggan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Prefix / Sumber Pesanan</label>
                        <select name="order_prefix" id="order_prefix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold" onchange="toggleManualOrderField()">
                            <option value="PESW" {{ old('order_prefix') == 'PESW' ? 'selected' : '' }}>PESW - Pesanan Web</option>
                            <option value="PESM" {{ old('order_prefix') == 'PESM' ? 'selected' : '' }}>PESM - Pesanan Marketing (Lebih dari 3 jam)</option>
                            <option value="PJLM" {{ old('order_prefix') == 'PJLM' ? 'selected' : '' }}>PJLM - Pesanan Marketing Kilat (Kurang dari 3 jam)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="prefix_hint">Nomor urut (misal: 001, 002) akan otomatis dibuatkan oleh sistem di belakang Prefix ini.</p>
                    </div>
                    <div class="md:col-span-2 hidden" id="manual_order_container">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nomor Order Manual (dari Web) <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 font-bold">
                                PESW-
                            </span>
                            <input type="text" name="manual_order_number" id="manual_order_number" value="{{ old('manual_order_number') }}" placeholder="Contoh: 12345" class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Masukkan nomor order unik dari website Anda (tanpa mengetik "PESW-").</p>
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

            <!-- Detail Pesanan -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-basket-shopping text-florist-600 mr-2"></i> Detail Pesanan</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Produk yang Dipesan <span class="text-red-500">*</span></label>
                        <input type="text" name="product_name" required value="{{ old('product_name') }}" placeholder="Contoh: Buket Mawar Merah 10 Tangkai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Harga Kesepakatan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_price" required min="0" value="{{ old('total_price') ?? 0 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none font-bold text-florist-600">
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

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Kartu Ucapan</label>
                        <textarea name="greeting_card" rows="2" placeholder="Tulis ucapan yang akan diprint/ditulis..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('greeting_card') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengiriman & Pembayaran -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Jadwal & Pengiriman -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-truck-fast text-florist-600 mr-2"></i> Jadwal & Pengiriman</h4>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Jadwal Kirim / Ambil <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Metode <span class="text-red-500">*</span></label>
                            <select name="delivery_method" id="delivery_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" onchange="toggleDeliveryFields()">
                                <option value="pickup">Ambil di Toko</option>
                                <option value="delivery">Diantar Kurir</option>
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
                                    <input type="number" name="delivery_distance" id="delivery_distance" step="0.1" min="0" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                                    <button type="button" onclick="calculateOngkir()" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg font-bold hover:bg-blue-200 transition-colors shrink-0 text-sm">Hitung</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Ongkir (Rp)</label>
                                <input type="number" name="delivery_fee" id="delivery_fee" readonly class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-lg text-gray-600 outline-none font-bold">
                            </div>
                        </div>
                        <div id="ongkir_message" class="text-xs font-bold text-red-500 mt-1 hidden"></div>

                        <div class="grid grid-cols-2 gap-4 hidden">
                            <!-- Hidden fields for future Maps integration -->
                            <input type="text" name="delivery_lat" id="delivery_lat">
                            <input type="text" name="delivery_lng" id="delivery_lng">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembayaran & Submit -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-wallet text-florist-600 mr-2"></i> Pembayaran</h4>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Pembayaran</label>
                        <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-800 outline-none focus:border-florist-400">
                            <option value="paid">Lunas (Paid)</option>
                            <option value="dp">DP (Down Payment)</option>
                            <option value="unpaid">Belum Bayar (Unpaid)</option>
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
            </div>
        </div>

    </div>
</form>

<script>
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

    function checkPromo() {
        const code = document.getElementById('promo_code').value.trim();
        // In online order we might not have a live subtotal before submitting, but let's assume it passes 0 for now
        // Since in online order they just pick a product, the actual price is calculated backend. 
        // We can just check voucher validity without subtotal.
        const msg = document.getElementById('promo_message');
        const discountInput = document.getElementById('discount');
        const promoIdInput = document.getElementById('promo_id');

        msg.classList.remove('hidden', 'text-green-500', 'text-red-500');

        if (!code) {
            msg.textContent = 'Masukkan kode voucher terlebih dahulu!';
            msg.classList.add('text-red-500');
            return;
        }

        fetch(`/api/check-promo?code=${code}&subtotal=99999999`) // Bypass subtotal check for online create view (since product is selected, price varies)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    if (data.message.includes('Minimal belanja')) {
                        // Let it pass for now, validate in backend
                        msg.textContent = 'Voucher ditemukan. Akan divalidasi saat simpan.';
                        msg.classList.add('text-green-500');
                        promoIdInput.value = data.promo_id || '';
                    } else {
                        msg.textContent = data.message;
                        msg.classList.add('text-red-500');
                        discountInput.readOnly = false;
                        promoIdInput.value = '';
                    }
                } else {
                    msg.textContent = 'Voucher aktif! Potongan akan dihitung otomatis saat disimpan.';
                    msg.classList.add('text-green-500');
                    discountInput.value = ''; // Let backend calculate
                    discountInput.readOnly = true;
                    promoIdInput.value = data.promo_id;
                    document.getElementById('discount_hint').textContent = 'Diskon dikunci oleh Voucher.';
                }
            })
            .catch(err => {
                msg.textContent = 'Terjadi kesalahan sistem.';
                msg.classList.add('text-red-500');
            });
    }

    function updateGrandTotal() {
        // For online order form, we don't display a live grand total right now because the product is just a dropdown.
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

    // Jalankan saat pertama kali dibuka untuk mempertahankan state old value
    document.addEventListener('DOMContentLoaded', function() {
        toggleManualOrderField();
    });
</script>
@endsection