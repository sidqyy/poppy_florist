<style>
    .cart-float-btn {
        bottom: 32px;
        right: 32px;
    }

    html.is-kiosk-mode .cart-float-btn {
        bottom: 32px;
        right: 32px;
    }
</style>
<!-- Floating Cart Button -->
<button onclick="toggleCart()" class="cart-float-btn fixed z-30 touch-btn bg-gradient-to-r from-florist-500 to-pink-500 hover:from-florist-600 hover:to-pink-600 text-white rounded-full py-4 px-8 shadow-xl shadow-florist-500/30 flex items-center justify-center gap-4 transition-all hover:scale-105 border-2 border-white/20 backdrop-blur-sm">
    <div class="relative flex items-center justify-center">
        <i class="fa-solid fa-basket-shopping text-2xl"></i>
        @if($totalItems > 0)
        <span class="absolute -top-3 -right-4 bg-red-500 text-white text-[11px] font-black rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-sm">{{ $totalItems }}</span>
        @endif
    </div>
    <div class="flex flex-col items-start leading-none border-l border-white/30 pl-4">
        <span class="text-[11px] font-medium text-white/90 uppercase tracking-wider mb-1">Total Keranjang</span>
        <span class="font-extrabold text-xl tracking-tight">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
    </div>
</button>

<!-- Cart Overlay -->
<div id="cartOverlay" onclick="toggleCart()" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 hidden transition-opacity opacity-0 duration-300"></div>

<!-- Right: Cart Sidebar -->
<div id="cartPanel" class="fixed top-0 right-0 h-full w-[450px] bg-white shadow-2xl z-50 flex flex-col border-l border-gray-100 transform translate-x-full transition-transform duration-300">
    <div class="p-6 bg-florist-500 text-white">
        <h2 class="text-2xl font-bold flex items-center justify-between">
            <span>Pesanan Anda ({{ $totalItems }})</span>
            <button onclick="toggleCart()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </h2>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50">
        @forelse($cart as $id => $item)
        <div class="bg-white p-4 rounded-2xl shadow-md border-2 border-gray-200 flex gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    @if($item['type'] === 'custom')
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-600 text-[10px] font-bold rounded">CUSTOM</span>
                    @elseif($item['type'] === 'material')
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded">ECERAN</span>
                    @elseif(isset($item['is_rented']) && $item['is_rented'])
                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-600 text-[10px] font-bold rounded">SEWA {{ $item['rental_duration'] }} HARI</span>
                    @else
                    <span class="px-2 py-0.5 bg-green-100 text-green-600 text-[10px] font-bold rounded">KATALOG</span>
                    @endif
                    <h4 class="font-bold text-gray-800 text-lg leading-tight">{{ $item['name'] }}</h4>
                </div>
                <p class="text-florist-500 font-bold">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>

                @if($item['type'] === 'custom' && !empty($item['components']))
                <ul class="mt-2 text-xs text-gray-500 list-disc list-inside">
                    @foreach($item['components'] as $comp)
                    <li>{{ $comp['name'] }} ({{ $comp['qty'] }}x)</li>
                    @endforeach
                </ul>
                @endif
            </div>

            <div class="flex flex-col items-end justify-between">
                <form action="{{ route('pos.cart.remove') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <button type="submit" class="touch-btn text-red-300 hover:text-red-500 p-2">
                        <i class="fa-solid fa-trash-can text-xl"></i>
                    </button>
                </form>

                <form action="{{ route('pos.cart.update') }}" method="POST" class="flex items-center gap-3 bg-gray-100 rounded-full p-1 mt-2">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">

                    <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}" class="touch-btn w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-600 font-bold" {{ $item['qty'] <= 1 ? 'disabled' : '' }}>
                        <i class="fa-solid fa-minus"></i>
                    </button>

                    <span class="font-bold text-xl w-6 text-center">{{ $item['qty'] }}</span>

                    <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}" class="touch-btn w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-florist-500 font-bold">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50 space-y-4">
            <i class="fa-solid fa-basket-shopping text-6xl"></i>
            <p class="text-xl font-medium">Keranjang masih kosong</p>
        </div>
        @endforelse
    </div>

    <!-- Cart Footer -->
    <div class="p-6 bg-white border-t border-gray-100 pb-8">
        <div class="flex justify-between items-center mb-6">
            <span class="text-xl text-gray-500 font-bold">Total Tagihan</span>
            <span class="text-3xl font-extrabold text-gray-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
        </div>

        <div class="flex gap-2">
            <form action="{{ route('pos.cart.clear') }}" method="POST" class="w-1/4">
                @csrf
                <button type="submit" class="touch-btn w-full py-5 bg-red-50 hover:bg-red-100 text-red-500 text-xl font-bold rounded-2xl flex justify-center items-center shadow-sm">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </form>

            @if($totalItems > 0)
            <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="touch-btn w-3/4 py-5 bg-florist-500 hover:bg-florist-600 text-white text-xl font-bold rounded-2xl flex justify-center items-center gap-3 shadow-lg shadow-florist-200">
                BAYAR
            </button>
            @else
            <button disabled class="w-3/4 py-5 bg-gray-200 text-gray-400 text-xl font-bold rounded-2xl flex justify-center items-center gap-3 cursor-not-allowed">
                KOSONG
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Form & Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl my-auto flex flex-col max-h-full">
        <div class="p-5 bg-florist-500 text-white flex justify-between items-center shrink-0 rounded-t-3xl">
            <h2 class="text-2xl font-bold"><i class="fa-solid fa-cash-register mr-2"></i> Proses Pembayaran & Detail Pesanan</h2>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-white/80 hover:text-white touch-btn">
                <i class="fa-solid fa-xmark text-3xl"></i>
            </button>
        </div>

        <form action="{{ route('pos.store') }}" method="POST" class="flex flex-col md:flex-row overflow-hidden flex-1">
            @csrf

            <!-- Kiri: Form Data -->
            <div class="flex-1 p-6 md:p-8 overflow-y-auto border-r border-gray-100 space-y-6">

                <!-- Data Pelanggan -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2"><i class="fa-solid fa-user text-florist-500 mr-2"></i> Data Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pembeli <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required autocomplete="off" placeholder="Nama..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. Telp Pembeli</label>
                            <input type="text" name="customer_phone" autocomplete="off" placeholder="08..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima <span class="text-xs font-normal text-gray-400">(Opsional)</span></label>
                            <input type="text" name="recipient_name" autocomplete="off" placeholder="Penerima..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. Telp Penerima</label>
                            <input type="text" name="recipient_phone" autocomplete="off" placeholder="08..."
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                        </div>
                    </div>
                </div>

                <!-- Pengiriman -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2"><i class="fa-solid fa-truck text-florist-500 mr-2"></i> Pengiriman & Jadwal</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Pengantaran <span class="text-red-500">*</span></label>
                            <select name="delivery_method" id="pos_delivery_method" required onchange="togglePosDelivery()"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                                <option value="pickup">Diambil di Toko (Pickup)</option>
                                <option value="delivery">Diantar Kurir (Delivery)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal & Jam Pengantaran <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="scheduled_at" required value="{{ date('Y-m-d\TH:i') }}"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                        </div>
                    </div>

                    <!-- Delivery Fields -->
                    <div id="pos_delivery_fields" class="hidden space-y-4 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Cari Lokasi di Peta</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" id="map_search_input" placeholder="Ketik nama jalan atau daerah..." class="w-full bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                                <button type="button" onclick="searchLocation()" class="px-4 py-3 bg-gray-600 text-white rounded-lg font-bold hover:bg-gray-700 transition-colors shrink-0 touch-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                            <div id="posMap" class="w-full h-48 bg-gray-200 rounded-xl overflow-hidden border border-gray-300 z-0 relative"></div>
                            <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-circle-info"></i> Geser atau klik peta untuk menentukan titik pengiriman.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi Peta</label>
                            <textarea name="map_address" id="map_address_input" rows="2" readonly class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg outline-none p-3 mb-2" placeholder="Otomatis terisi dari pencarian atau klik peta..."></textarea>

                            <label class="block text-sm font-bold text-gray-700 mb-1">Detail Alamat</label>
                            <textarea name="detail_address" id="detail_address_input" rows="2" placeholder="Patokan, No. Rumah, RT/RW, Blok..." class="w-full bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jarak (KM)</label>
                                <div class="flex gap-2">
                                    <input type="number" name="delivery_distance" id="pos_delivery_distance" step="0.1" min="0" placeholder="0" class="w-full bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3">
                                    <button type="button" onclick="calculatePosOngkir()" class="px-4 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors shrink-0 touch-btn">Hitung</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Ongkos Kirim Otomatis</label>
                                <input type="number" name="delivery_fee" id="pos_delivery_fee" value="0" readonly class="w-full bg-gray-200 border-none text-gray-600 rounded-lg outline-none p-3 font-bold">
                            </div>
                        </div>
                        <div id="pos_ongkir_message" class="text-xs font-bold text-red-500 mt-1 hidden"></div>
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4"><i class="fa-solid fa-note-sticky text-florist-500 mr-2"></i> Catatan Pesanan</h3>
                    <textarea name="notes" rows="2" placeholder="Warna pita, pesan kartu ucapan..." class="w-full bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-300 outline-none p-3"></textarea>
                </div>
            </div>

            <!-- Kanan: Pembayaran & Ringkasan -->
            <div class="w-full md:w-[400px] bg-gray-50 p-6 md:p-8 flex flex-col justify-between shrink-0 overflow-y-auto">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Metode Pembayaran</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" class="peer sr-only" checked>
                                <div class="text-center py-3 border-2 border-gray-200 bg-white rounded-xl peer-checked:border-florist-500 peer-checked:bg-florist-50 peer-checked:text-florist-600 font-bold transition-all touch-btn">
                                    <i class="fa-solid fa-money-bill-wave mb-1 block"></i> Cash
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="transfer" class="peer sr-only">
                                <div class="text-center py-3 border-2 border-gray-200 bg-white rounded-xl peer-checked:border-florist-500 peer-checked:bg-florist-50 peer-checked:text-florist-600 font-bold transition-all touch-btn">
                                    <i class="fa-solid fa-building-columns mb-1 block"></i> Transfer
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="qris" class="peer sr-only">
                                <div class="text-center py-3 border-2 border-gray-200 bg-white rounded-xl peer-checked:border-florist-500 peer-checked:bg-florist-50 peer-checked:text-florist-600 font-bold transition-all touch-btn">
                                    <i class="fa-solid fa-qrcode mb-1 block"></i> QRIS
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 border-b pb-2">Ringkasan Biaya</h3>

                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Subtotal Barang ({{ $totalItems }})</span>
                            <span class="font-bold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4 text-blue-600" id="ongkir_summary_row">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold" id="ongkir_summary_text">Rp 0</span>
                        </div>

                        <div class="border-t pt-3 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Total Akhir</span>
                            <span class="text-2xl font-extrabold text-florist-600" id="grand_total_display">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <input type="hidden" id="base_total" value="{{ $totalPrice }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Diterima (Rp)</label>
                        <input type="number" name="amount_tendered" id="amount_tendered" required value="{{ $totalPrice }}"
                            class="w-full bg-white border border-gray-300 text-gray-800 text-xl font-bold rounded-xl focus:ring-2 focus:ring-florist-300 block p-3 outline-none"
                            onkeyup="calculateChange()">

                        <!-- Quick Cash -->
                        <div class="grid grid-cols-4 gap-1 mt-2">
                            <button type="button" onclick="setCash('pas')" class="py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded text-xs touch-btn">Uang Pas</button>
                            <button type="button" onclick="setCash(50000)" class="py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded text-xs touch-btn">50 Rb</button>
                            <button type="button" onclick="setCash(100000)" class="py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded text-xs touch-btn">100 Rb</button>
                            <button type="button" onclick="setCash(200000)" class="py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded text-xs touch-btn">200 Rb</button>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-4 flex justify-between items-center text-white">
                        <span class="text-sm font-bold text-gray-300">Kembalian:</span>
                        <span id="change_display" class="text-2xl font-extrabold text-green-400">Rp 0</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button type="submit" class="touch-btn w-full py-4 bg-florist-500 hover:bg-florist-600 text-white text-xl font-bold rounded-2xl flex justify-center items-center shadow-lg">
                        KONFIRMASI & BAYAR
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let posGrandTotal = parseInt("{{ $totalPrice }}") || 0;

    function setCash(amount) {
        if (amount === 'pas') amount = posGrandTotal;
        document.getElementById('amount_tendered').value = amount;
        calculateChange();
    }

    function calculateChange() {
        const tendered = parseInt(document.getElementById('amount_tendered').value) || 0;
        let change = tendered - posGrandTotal;
        if (change < 0) change = 0;

        document.getElementById('change_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
    }

    function toggleCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');

        if (panel.classList.contains('translate-x-full')) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            }, 10);
        } else {
            panel.classList.add('translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }

    function togglePosDelivery() {
        const method = document.getElementById('pos_delivery_method').value;
        const fields = document.getElementById('pos_delivery_fields');
        if (method === 'delivery') {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
            document.getElementById('pos_delivery_distance').value = '';
            document.getElementById('pos_delivery_fee').value = 0;
            updatePosGrandTotal();
        }
    }

    function calculatePosOngkir() {
        const distance = document.getElementById('pos_delivery_distance').value;
        const msg = document.getElementById('pos_ongkir_message');

        if (!distance) {
            msg.innerText = 'Masukkan jarak terlebih dahulu';
            msg.classList.remove('hidden');
            return;
        }

        msg.classList.add('hidden');

        fetch(`{{ route('api.ongkir') }}?distance=${distance}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('pos_delivery_fee').value = data.fee;
                    updatePosGrandTotal();
                } else {
                    msg.innerText = data.message;
                    msg.classList.remove('hidden');
                    document.getElementById('pos_delivery_fee').value = 0;
                    updatePosGrandTotal();
                }
            })
            .catch(err => {
                msg.innerText = 'Gagal menghitung ongkir';
                msg.classList.remove('hidden');
            });
    }

    function updatePosGrandTotal() {
        const base = parseInt(document.getElementById('base_total').value) || 0;
        const fee = parseInt(document.getElementById('pos_delivery_fee').value) || 0;

        posGrandTotal = base + fee;

        document.getElementById('ongkir_summary_text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(fee);
        document.getElementById('grand_total_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(posGrandTotal);

        // Update amount tendered default if it was equal to old grand total
        const currentTendered = parseInt(document.getElementById('amount_tendered').value) || 0;
        if (currentTendered < posGrandTotal) {
            document.getElementById('amount_tendered').value = posGrandTotal;
        }

        calculateChange();
    }

    // Leaflet Map Logic
    let posMap, marker;
    const storeLat = parseFloat("{{ \App\Models\Setting::get('store_lat', '-3.320556') }}");
    const storeLng = parseFloat("{{ \App\Models\Setting::get('store_lng', '114.587222') }}");

    function initPosMap() {
        if (posMap) return; // Already initialized

        // Fix leaflet container not rendering properly when hidden
        setTimeout(() => {
            posMap = L.map('posMap').setView([storeLat, storeLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(posMap);

            // Add Store Marker
            L.marker([storeLat, storeLng]).addTo(posMap)
                .bindPopup('<b>Toko Kami</b><br>Lokasi Florist').openPopup();

            // Handle Map Click
            posMap.on('click', function(e) {
                setDestinationMarker(e.latlng.lat, e.latlng.lng);

                // Reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            document.getElementById('map_address_input').value = data.display_name;
                        }
                    });
            });
        }, 300);
    }

    function setDestinationMarker(lat, lng) {
        if (marker) {
            posMap.removeLayer(marker);
        }
        marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(posMap).bindPopup('Lokasi Pengiriman').openPopup();

        // Calculate Driving Distance via OSRM
        calculateOSRM(storeLat, storeLng, lat, lng);
    }

    function calculateOSRM(lat1, lng1, lat2, lng2) {
        // OSRM API expects format: lng,lat
        const url = `https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=false`;

        const msg = document.getElementById('pos_ongkir_message');
        msg.innerText = 'Menghitung rute...';
        msg.classList.remove('hidden', 'text-red-500');
        msg.classList.add('text-blue-500');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                msg.classList.add('hidden', 'text-red-500');
                msg.classList.remove('text-blue-500');

                if (data.code === 'Ok' && data.routes.length > 0) {
                    const distanceMeters = data.routes[0].distance;
                    let distanceKm = (distanceMeters / 1000).toFixed(1);

                    document.getElementById('pos_delivery_distance').value = distanceKm;
                    calculatePosOngkir(); // Trigger ongkir calculation
                } else {
                    alert('Rute berkendara tidak ditemukan!');
                }
            })
            .catch(err => {
                msg.classList.add('hidden');
                console.error('OSRM Error', err);
            });
    }

    function searchLocation() {
        const query = document.getElementById('map_search_input').value;
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    posMap.setView([lat, lng], 15);
                    setDestinationMarker(lat, lng);
                    document.getElementById('map_address_input').value = data[0].display_name;
                } else {
                    alert('Lokasi tidak ditemukan');
                }
            });
    }

    // Call initPosMap when delivery is selected
    const originalTogglePosDelivery = togglePosDelivery;
    togglePosDelivery = function() {
        originalTogglePosDelivery();
        const method = document.getElementById('pos_delivery_method').value;
        if (method === 'delivery') {
            initPosMap();
        }
    };

    // AJAX Cart Interceptor (No Refresh)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form) return;
        
        const action = form.getAttribute('action') || '';
        if (action.includes('/pos/cart/add') || 
            action.includes('/pos/cart/update') || 
            action.includes('/pos/cart/remove') || 
            action.includes('/pos/cart/clear')) {
            
            e.preventDefault();
            
            // Siapkan FormData dan masukkan nilai tombol submitter (seperti tombol + / - qty)
            const formData = new FormData(form);
            const submitter = e.submitter;
            if (submitter && submitter.name) {
                formData.append(submitter.name, submitter.value);
            }
            
            sendCartAjax(form.action, formData, action.includes('/pos/cart/add'));
        }
    });

    function sendCartAjax(url, formData, isAddAction = false) {
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(htmlText => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            
            // Swap isi panel keranjang (Sidebar)
            const newCartPanel = doc.getElementById('cartPanel');
            const currentCartPanel = document.getElementById('cartPanel');
            if (newCartPanel && currentCartPanel) {
                currentCartPanel.innerHTML = newCartPanel.innerHTML;
            }
            
            // Swap isi tombol keranjang melayang (Floating Button)
            const newFloatBtn = doc.querySelector('.cart-float-btn');
            const currentFloatBtn = document.querySelector('.cart-float-btn');
            if (newFloatBtn && currentFloatBtn) {
                currentFloatBtn.innerHTML = newFloatBtn.innerHTML;
            }

            // Selaraskan base total untuk kalkulasi tagihan di modal pembayaran
            const newBaseTotalInput = doc.getElementById('base_total');
            if (newBaseTotalInput) {
                document.getElementById('base_total').value = newBaseTotalInput.value;
                if (typeof updatePosGrandTotal === 'function') {
                    updatePosGrandTotal();
                }
            }

            // Tampilkan notifikasi sukses jika menambah barang baru
            if (isAddAction && typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil dimasukkan ke keranjang',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        })
        .catch(error => {
            console.error('Gagal memperbarui keranjang:', error);
        });
    }
</script>