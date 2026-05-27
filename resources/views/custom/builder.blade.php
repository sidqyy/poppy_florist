@extends('layouts.app')

@section('title', 'Buat Custom Bucket')
@section('page_title', 'Custom Bucket Builder')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Builder Mode</h3>
        <p class="text-gray-500 text-sm mt-1">Rakik bucket custom secara interaktif (Point of Sales)</p>
    </div>
    <a href="{{ route('custom.drafts') }}" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">
        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Draft
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:h-[calc(100vh-180px)] min-h-[600px]">
    
    <!-- Panel Kiri: Katalog Bahan Baku -->
    <div class="lg:col-span-7 xl:col-span-8 flex flex-col bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden h-[500px] lg:h-auto">
        
        <!-- Filter Tabs -->
        <div class="flex overflow-x-auto gap-2 p-4 border-b border-gray-100 bg-gray-50">
            <button class="filter-tab active px-4 py-2 rounded-lg text-sm font-bold bg-florist-500 text-white shadow-md transition-colors" data-filter="all">Semua</button>
            <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="flower_fresh">Bunga Fresh</button>
            <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="flower_artificial">Bunga Artificial</button>
            <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="wrapping">Wrapping</button>
            <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="ribbon">Pita & Aksesoris</button>
        </div>

        <!-- Material Grid -->
        <div class="p-4 overflow-y-auto flex-1 bg-gray-50/50">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($materials as $mat)
                <div class="material-card bg-white p-3 rounded-xl border border-gray-200 hover:border-florist-400 cursor-pointer shadow-sm hover:shadow-md transition-all group select-none" 
                     data-id="{{ $mat->id }}" 
                     data-name="{{ $mat->name }}" 
                     data-price="{{ $mat->price }}" 
                     data-stock="{{ $mat->stock }}"
                     data-type="{{ $mat->type }}"
                     onclick="addComponent(this)">
                    
                    <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center relative overflow-hidden">
                        @if($mat->image)
                            <img src="{{ asset('storage/'.$mat->image) }}" class="object-cover w-full h-full">
                        @else
                            <i class="fa-solid fa-leaf text-3xl text-gray-300"></i>
                        @endif
                        <div class="absolute inset-0 bg-florist-500/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i class="fa-solid fa-plus text-white text-2xl drop-shadow-md"></i>
                        </div>
                    </div>
                    
                    <h5 class="text-sm font-bold text-gray-800 leading-tight mb-1 truncate group-hover:text-florist-600">{{ $mat->name }}</h5>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-florist-500 font-bold text-sm">{{ $mat->formatted_price }}</span>
                        <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-600">Stok: {{ $mat->stock }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Panel Kanan: Struk / Estimasi -->
    <div class="lg:col-span-5 xl:col-span-4 flex flex-col bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden relative h-[500px] lg:h-auto">
        <!-- Header -->
        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
            <h4 class="font-bold"><i class="fa-solid fa-receipt mr-2 text-gray-400"></i> Draft Pesanan</h4>
            <span class="text-xs bg-gray-700 px-2 py-1 rounded">Custom Bucket</span>
        </div>

        <div class="p-4 border-b border-gray-100 flex flex-col gap-3">
            <input type="text" id="customerName" placeholder="Nama Pelanggan / Referensi..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none text-sm font-medium">
            
            <div class="grid grid-cols-2 gap-2">
                <select id="deliveryMethod" class="px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-florist-400">
                    <option value="pickup">Ambil di Toko</option>
                    <option value="delivery">Kirim (Delivery)</option>
                </select>
                <div id="distanceContainer" class="hidden relative">
                    <input type="number" id="deliveryDistance" placeholder="Jarak (km)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-florist-400 pr-8">
                    <span class="absolute right-3 top-2 text-sm text-gray-400">km</span>
                </div>
            </div>
            <div id="ongkirLabel" class="text-xs text-florist-500 font-bold hidden text-right">Ongkir: Rp 0</div>
        </div>

        <!-- Component List -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="cartContainer">
            <!-- Empty State -->
            <div id="emptyCart" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                <i class="fa-solid fa-basket-shopping text-5xl mb-3"></i>
                <p class="text-sm">Belum ada bahan yang dipilih.</p>
                <p class="text-xs mt-1">Klik item di sebelah kiri untuk meracik.</p>
            </div>
            
            <!-- Items Container -->
            <div id="cartItems" class="space-y-3 hidden">
                <!-- Injected via JS -->
            </div>
        </div>

        <!-- Footer / Checkout -->
        <div class="p-4 bg-white border-t border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500 font-medium">Estimasi Subtotal</span>
                <span class="text-2xl font-bold text-florist-600" id="totalPrice">Rp 0</span>
            </div>
            <button id="saveDraftBtn" onclick="submitCustomBucket()" class="w-full py-3 bg-florist-500 hover:bg-florist-600 text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan Draft Custom
            </button>
        </div>
        
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm hidden items-center justify-center z-10">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-florist-500 border-t-transparent"></div>
        </div>
    </div>
</div>

<script>
    // State
    const cart = {};
    let total = 0;
    let ongkir = 0;

    // Delivery Logic
    document.getElementById('deliveryMethod').addEventListener('change', function() {
        if(this.value === 'delivery') {
            document.getElementById('distanceContainer').classList.remove('hidden');
            calculateOngkir();
        } else {
            document.getElementById('distanceContainer').classList.add('hidden');
            document.getElementById('ongkirLabel').classList.add('hidden');
            ongkir = 0;
            renderCart();
        }
    });

    document.getElementById('deliveryDistance').addEventListener('input', debounce(function() {
        if(document.getElementById('deliveryMethod').value === 'delivery') {
            calculateOngkir();
        }
    }, 500));

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => { clearTimeout(timeout); func(...args); };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function calculateOngkir() {
        const distance = document.getElementById('deliveryDistance').value;
        if(distance > 0) {
            fetch(`/api/calculate-ongkir?distance=${distance}`)
                .then(res => res.json())
                .then(data => {
                    ongkir = data.fee;
                    document.getElementById('ongkirLabel').innerText = `Ongkir: Rp ${ongkir.toLocaleString('id-ID')}`;
                    document.getElementById('ongkirLabel').classList.remove('hidden');
                    renderCart();
                });
        } else {
            ongkir = 0;
            document.getElementById('ongkirLabel').classList.add('hidden');
            renderCart();
        }
    }

    // Filter Logic
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', (e) => {
            // Update Active Class
            document.querySelectorAll('.filter-tab').forEach(t => {
                t.className = 'filter-tab px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 hover:bg-gray-100 border border-gray-200';
            });
            e.target.className = 'filter-tab active px-4 py-2 rounded-lg text-sm font-bold bg-florist-500 text-white shadow-md transition-colors';
            
            // Filter Cards
            const filter = e.target.dataset.filter;
            document.querySelectorAll('.material-card').forEach(card => {
                if (filter === 'all' || 
                   (filter === 'ribbon' && (card.dataset.type === 'ribbon' || card.dataset.type === 'accessory' || card.dataset.type === 'doll' || card.dataset.type === 'greeting_card' || card.dataset.type === 'packaging')) ||
                   card.dataset.type === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Add Component
    function addComponent(element) {
        const id = element.dataset.id;
        const name = element.dataset.name;
        const price = parseFloat(element.dataset.price);
        const maxStock = parseInt(element.dataset.stock);

        if (!cart[id]) {
            cart[id] = { id, name, price, qty: 1, maxStock };
        } else {
            if (cart[id].qty < maxStock) {
                cart[id].qty++;
            } else {
                alert(`Stok ${name} tidak mencukupi! Sisa: ${maxStock}`);
                return;
            }
        }
        
        renderCart();
    }

    // Update Qty
    function updateQty(id, delta) {
        if (cart[id]) {
            const newQty = cart[id].qty + delta;
            if (newQty > 0) {
                if (newQty <= cart[id].maxStock) {
                    cart[id].qty = newQty;
                } else {
                    alert(`Stok tidak mencukupi! Sisa: ${cart[id].maxStock}`);
                }
            } else {
                delete cart[id];
            }
            renderCart();
        }
    }

    // Render Cart
    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyState = document.getElementById('emptyCart');
        const btn = document.getElementById('saveDraftBtn');
        
        container.innerHTML = '';
        total = 0;

        const keys = Object.keys(cart);
        
        if (keys.length === 0) {
            container.classList.add('hidden');
            emptyState.classList.remove('hidden');
            btn.disabled = true;
            document.getElementById('totalPrice').innerText = 'Rp 0';
            return;
        }

        container.classList.remove('hidden');
        emptyState.classList.add('hidden');
        btn.disabled = false;

        keys.forEach(k => {
            const item = cart[k];
            const subtotal = item.price * item.qty;
            total += subtotal;

            container.innerHTML += `
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex justify-between items-center">
                    <div class="flex-1 pr-3">
                        <h6 class="text-sm font-bold text-gray-800 leading-tight">${item.name}</h6>
                        <div class="text-florist-500 text-xs font-semibold mt-1">Rp ${item.price.toLocaleString('id-ID')}</div>
                    </div>
                    
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-1 border border-gray-200">
                        <button onclick="updateQty('${k}', -1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-red-500"><i class="fa-solid fa-minus text-xs"></i></button>
                        <span class="w-6 text-center text-sm font-bold">${item.qty}</span>
                        <button onclick="updateQty('${k}', 1)" class="w-7 h-7 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-green-500"><i class="fa-solid fa-plus text-xs"></i></button>
                    </div>
                </div>
            `;
        });

        document.getElementById('totalPrice').innerText = 'Rp ' + (total + ongkir).toLocaleString('id-ID');
    }

    // Submit Custom Bucket
    function submitCustomBucket() {
        const customerName = document.getElementById('customerName').value;
        if (!customerName) {
            alert('Harap masukkan nama pelanggan atau referensi.');
            document.getElementById('customerName').focus();
            return;
        }

        const componentsArray = Object.values(cart).map(item => ({
            material_id: item.id,
            qty: item.qty
        }));

        if (componentsArray.length === 0) return;

        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingOverlay').classList.add('flex');

        fetch('{{ route('custom.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                customer_name: customerName,
                delivery_method: document.getElementById('deliveryMethod').value,
                delivery_distance: document.getElementById('deliveryDistance').value,
                delivery_fee: ongkir,
                components: componentsArray
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Draft Custom berhasil disimpan!');
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Terjadi kesalahan sistem.');
                document.getElementById('loadingOverlay').classList.add('hidden');
                document.getElementById('loadingOverlay').classList.remove('flex');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal menghubungi server.');
            document.getElementById('loadingOverlay').classList.add('hidden');
            document.getElementById('loadingOverlay').classList.remove('flex');
        });
    }
    
    // Initial State
    renderCart();
</script>
@endsection
