@extends('layouts.app')

@section('title', 'Checkout Pesanan')
@section('page_title', 'Checkout Pesanan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Checkout Pesanan Offline</h3>
        <p class="text-gray-500 text-sm mt-1">Selesaikan pesanan dari pelanggan di toko.</p>
    </div>
</div>

<form action="{{ route('orders.store') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Form -->
        <div class="lg:col-span-2 space-y-6">
            
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
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-user mr-2 text-florist-400"></i> Informasi Pelanggan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name') ?? str_replace('Custom Bucket - ', '', $product->name) }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-truck mr-2 text-florist-400"></i> Pengiriman & Jadwal</h4>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="delivery_method" value="pickup" checked class="peer hidden">
                            <div class="border border-gray-200 rounded-xl p-4 text-center peer-checked:border-florist-500 peer-checked:bg-florist-50 transition-all">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-truck mr-2 text-florist-400"></i> Pengiriman & Ongkir</h4>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pengiriman</label>
                    <select name="delivery_method" id="delivery_method" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none" required onchange="toggleDeliveryFields()">
                        <option value="pickup">Ambil Sendiri (Pickup)</option>
                        <option value="delivery">Kirim ke Alamat (Delivery)</option>
                    </select>
                </div>

                <div id="delivery_fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Kirim</label>
                        <input type="datetime-local" name="scheduled_at" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="delivery_address" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jarak Pengiriman (KM)</label>
                            <div class="flex gap-2">
                                <input type="number" name="delivery_distance" id="delivery_distance" step="0.1" min="0" placeholder="Misal: 5.5" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                                <button type="button" onclick="calculateOngkir()" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-bold hover:bg-blue-200 transition-colors shrink-0">Hitung</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ongkos Kirim (Rp)</label>
                            <input type="number" name="delivery_fee" id="delivery_fee" readonly class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 outline-none font-bold">
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
            
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-stopwatch mr-2 text-florist-400"></i> Prioritas & Estimasi</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-2 p-3 border border-yellow-200 bg-yellow-50 rounded-xl cursor-pointer hover:bg-yellow-100 transition-colors h-full">
                            <input type="checkbox" name="is_urgent" value="1" class="w-5 h-5 text-yellow-600 rounded focus:ring-yellow-500">
                            <div>
                                <span class="block font-bold text-yellow-800 text-sm">Pesanan Urgent 🔥</span>
                                <span class="text-xs text-yellow-700">Akan diprioritaskan di dapur</span>
                            </div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Pengerjaan (Menit)</label>
                        <input type="number" name="estimated_time" min="0" placeholder="Opsional" value="{{ old('estimated_time') }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-comment-dots mr-2 text-florist-400"></i> Catatan Pesanan</h4>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden sticky top-6">
                <div class="p-5 bg-gray-800 text-white">
                    <h4 class="font-bold text-lg">Detail Produk</h4>
                </div>
                
                <div class="p-5 border-b border-gray-100">
                    <h5 class="font-bold text-gray-800 text-lg mb-1">{{ $product->name }}</h5>
                    <p class="text-sm text-gray-500">
                        @if($product->availability == 'custom')
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-600 rounded text-xs font-bold mr-1">CUSTOM</span>
                        @endif
                        {{ $product->categories->first()->name ?? 'Umum' }}
                    </p>
                </div>
                
                <div class="p-5 bg-gray-50 border-b border-gray-100">
                    <h6 class="text-xs font-bold text-gray-400 uppercase mb-3">Rincian Komponen</h6>
                    <ul class="space-y-3">
                        @foreach($product->components as $comp)
                        <li class="flex justify-between items-start text-sm">
                            <div class="flex-1 pr-4">
                                <span class="font-medium text-gray-800">{{ $comp->material->name }}</span>
                                <div class="text-gray-400 text-xs">{{ $comp->qty }} {{ $comp->material->unit }} x Rp {{ number_format($comp->material->price, 0, ',', '.') }}</div>
                            </div>
                            <span class="font-bold text-gray-600">Rp {{ number_format($comp->material->price * $comp->qty, 0, ',', '.') }}</span>
                        </li>
            
            <!-- Section Diskon & Promo -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-tag mr-2 text-florist-400"></i> Potongan Harga</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher</label>
                        <div class="flex gap-2">
                            <input type="text" name="promo_code" id="promo_code" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none uppercase" placeholder="KODE PROMO">
                            <input type="hidden" name="promo_id" id="promo_id">
                            <button type="button" onclick="checkPromo()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-bold hover:bg-purple-200 transition-colors shrink-0">Terapkan</button>
                        </div>
                        <div id="promo_message" class="text-xs font-bold mt-1 hidden"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diskon Manual (Rp)</label>
                        <input type="number" name="discount" id="discount" value="0" min="0" oninput="updateGrandTotal()" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                        <p class="text-xs text-gray-400 mt-1" id="discount_hint">Otomatis terisi jika pakai voucher.</p>
                    </div>
                </div>
            </div>

            <!-- Section Total & Submit -->
            <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
                <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600 font-medium">Subtotal Produk</span>
                        <span class="font-bold text-gray-800" id="display_subtotal" data-value="{{ $product->total_price }}">Rp {{ number_format($product->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600 font-medium">Ongkos Kirim</span>
                        <span class="font-bold text-gray-800" id="display_ongkir">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600 font-medium">Diskon</span>
                        <span class="font-bold text-red-600" id="display_discount">- Rp 0</span>
                    </div>
                    <hr class="border-gray-300 my-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800 font-bold uppercase tracking-wider">Total Akhir</span>
                        <span class="text-2xl font-bold text-florist-600" id="display_grand_total">Rp {{ number_format($product->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Pembayaran Awal</label>
                    <select name="payment_status" class="w-full px-4 py-3 border border-gray-200 rounded-xl font-medium text-gray-800 outline-none focus:border-florist-400 bg-gray-50">
                        <option value="paid">Lunas (Paid)</option>
                        <option value="dp">DP (Down Payment)</option>
                        <option value="unpaid">Belum Bayar (Unpaid)</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-4 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 text-lg" onclick="return confirm('Proses Pesanan? Stok material akan otomatis dikurangi.')">
                    <i class="fa-solid fa-check-circle mr-2"></i> Proses Pesanan
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
        updateGrandTotal();
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
            updateGrandTotal();
        });
}

function checkPromo() {
    const code = document.getElementById('promo_code').value.trim();
    const subtotal = parseFloat(document.getElementById('display_subtotal').getAttribute('data-value')) || 0;
    const msg = document.getElementById('promo_message');
    const discountInput = document.getElementById('discount');
    const promoIdInput = document.getElementById('promo_id');

    msg.classList.remove('hidden', 'text-green-500', 'text-red-500');
    
    if (!code) {
        msg.textContent = 'Masukkan kode voucher terlebih dahulu!';
        msg.classList.add('text-red-500');
        return;
    }

    fetch(`/api/check-promo?code=${code}&subtotal=${subtotal}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                msg.textContent = data.message;
                msg.classList.add('text-red-500');
                discountInput.readOnly = false;
                promoIdInput.value = '';
            } else {
                msg.textContent = data.message;
                msg.classList.add('text-green-500');
                discountInput.value = data.discount_amount;
                discountInput.readOnly = true;
                promoIdInput.value = data.promo_id;
                document.getElementById('discount_hint').textContent = 'Diskon dikunci oleh Voucher.';
            }
            updateGrandTotal();
        })
        .catch(err => {
            msg.textContent = 'Terjadi kesalahan sistem.';
            msg.classList.add('text-red-500');
        });
}

function updateGrandTotal() {
    let subtotal = parseFloat(document.getElementById('display_subtotal').getAttribute('data-value')) || 0;
    let ongkir = parseFloat(document.getElementById('delivery_fee').value) || 0;
    let discount = parseFloat(document.getElementById('discount').value) || 0;

    let grandTotal = subtotal + ongkir - discount;
    if (grandTotal < 0) grandTotal = 0;

    document.getElementById('display_ongkir').textContent = 'Rp ' + ongkir.toLocaleString('id-ID');
    document.getElementById('display_discount').textContent = '- Rp ' + discount.toLocaleString('id-ID');
    document.getElementById('display_grand_total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}
</script>
@endsection
