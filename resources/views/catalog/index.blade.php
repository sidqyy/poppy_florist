@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page_title', 'Katalog Etalase')

@section('content')
<div class="mb-8">
    <div class="bg-gradient-to-r from-florist-400 to-pink-300 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-2">Katalog Florist</h2>
            <p class="text-florist-50 text-lg mb-6">Temukan racikan bucket terbaik untuk momen spesial pelanggan.</p>
            
            <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 max-w-4xl">
                <!-- Search -->
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama bucket..." class="w-full pl-11 pr-4 py-3 rounded-xl text-gray-800 focus:ring-4 focus:ring-white/50 outline-none border-0 shadow-inner">
                </div>
                
                <!-- Category Filter -->
                <select name="category_id" class="px-4 py-3 rounded-xl text-gray-800 border-0 shadow-inner outline-none md:w-48">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-8 py-3 bg-white text-florist-500 font-bold rounded-xl hover:bg-gray-50 transition-colors shadow-md">
                    Cari
                </button>
            </form>
        </div>
        
        <!-- Decorative bg elements -->
        <div class="absolute -right-10 -bottom-10 opacity-20">
            <i class="fa-solid fa-leaf text-9xl"></i>
        </div>
    </div>
</div>

<!-- Additional Filters (Pills) -->
<form action="{{ route('catalog.index') }}" method="GET" id="filterForm" class="mb-8 flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl shadow-md border-2 border-gray-200">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
    
    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider mr-2"><i class="fa-solid fa-filter mr-1"></i> Filter:</span>
    
    <div class="flex bg-gray-100 rounded-lg p-1">
        <label class="cursor-pointer">
            <input type="radio" name="availability" value="" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('availability') == '' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('availability') == '' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Semua Status</div>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="availability" value="ready" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('availability') == 'ready' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('availability') == 'ready' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Ready Stock</div>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="availability" value="preorder" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('availability') == 'preorder' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('availability') == 'preorder' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Pre-Order</div>
        </label>
    </div>

    <div class="flex bg-gray-100 rounded-lg p-1">
        <label class="cursor-pointer">
            <input type="radio" name="flower_type" value="" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('flower_type') == '' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('flower_type') == '' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Semua Bunga</div>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="flower_type" value="fresh" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('flower_type') == 'fresh' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('flower_type') == 'fresh' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Fresh Flower</div>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="flower_type" value="artificial" class="hidden" onchange="document.getElementById('filterForm').submit()" {{ request('flower_type') == 'artificial' ? 'checked' : '' }}>
            <div class="px-4 py-1.5 rounded-md text-sm font-medium {{ request('flower_type') == 'artificial' ? 'bg-white shadow text-florist-600' : 'text-gray-500 hover:text-gray-700' }}">Artificial</div>
        </label>
    </div>
</form>

<!-- Grid Katalog -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($products as $product)
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border-2 border-gray-200 shadow-md group cursor-pointer" onclick="openDetailModal({{ $product->id }})">
        <!-- Image Area -->
        <div class="aspect-square bg-gray-50 relative overflow-hidden flex items-center justify-center">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <i class="fa-solid fa-camera text-4xl text-gray-300"></i>
            @endif
            
            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-col gap-2">
                <span class="px-3 py-1 bg-white/90 backdrop-blur rounded-full text-xs font-bold text-gray-700 shadow-sm border border-gray-200">
                    {{ $product->categories->first()->name ?? 'Umum' }}
                </span>
                
                @if($product->availability == 'ready')
                    <span class="px-3 py-1 bg-green-500/90 backdrop-blur rounded-full text-xs font-bold text-white shadow-sm">
                        <i class="fa-solid fa-check-circle mr-1"></i> Ready
                    </span>
                @elseif($product->availability == 'preorder')
                    <span class="px-3 py-1 bg-yellow-500/90 backdrop-blur rounded-full text-xs font-bold text-white shadow-sm">
                        <i class="fa-solid fa-clock mr-1"></i> Pre-Order
                    </span>
                @else
                    <span class="px-3 py-1 bg-purple-500/90 backdrop-blur rounded-full text-xs font-bold text-white shadow-sm">
                        <i class="fa-solid fa-palette mr-1"></i> Custom
                    </span>
                @endif
            </div>

            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <span class="px-4 py-2 bg-white rounded-lg text-florist-600 font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform">
                    Lihat Detail
                </span>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-1 truncate group-hover:text-florist-600 transition-colors">{{ $product->name }}</h3>
            <p class="text-xs text-gray-500 mb-4 line-clamp-2 min-h-[2rem]">{{ $product->description ?? 'Bucket spesial yang dirangkai dengan sepenuh hati.' }}</p>
            
            <div class="flex items-end justify-between">
                <div>
                    <span class="block text-xs text-gray-400 mb-1">Mulai dari</span>
                    <span class="text-xl font-bold text-florist-600">{{ $product->formatted_price }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Data for Modal -->
    <template id="product-data-{{ $product->id }}">
        <div class="product-data-title">{{ $product->name }}</div>
        <div class="product-data-price">{{ $product->formatted_price }}</div>
        <div class="product-data-desc">{{ $product->description ?? '-' }}</div>
        <div class="product-data-status">{{ strtoupper($product->availability) }}</div>
        <div class="product-data-category">{{ $product->categories->first()->name ?? 'Umum' }}</div>
        <div class="product-data-components">
            @foreach($product->components as $comp)
                <li class="py-2 flex justify-between items-center text-sm">
                    <div>
                        <span class="font-medium text-gray-800">{{ $comp->material->name }}</span>
                        @if($comp->notes)
                            <span class="text-xs text-gray-500 block">Catatan: {{ $comp->notes }}</span>
                        @endif
                    </div>
                    <span class="font-bold text-gray-600">{{ $comp->qty }} {{ $comp->material->unit }}</span>
                </li>
            @endforeach
        </div>
    </template>
    @empty
    <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-2xl border-2 border-gray-200 shadow-md">
        <i class="fa-solid fa-box-open text-6xl mb-4 text-gray-200"></i>
        <h4 class="text-xl font-bold text-gray-500 mb-2">Produk Tidak Ditemukan</h4>
        <p>Tidak ada bucket yang sesuai dengan filter pencarian Anda.</p>
        <a href="{{ route('catalog.index') }}" class="inline-block mt-4 text-florist-500 hover:underline">Reset Filter</a>
    </div>
    @endforelse
</div>

<!-- Modal Detail Produk -->
<div id="detailModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform transition-all relative">
        <button onclick="closeDetailModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-red-100 hover:text-red-600 text-gray-500 rounded-full transition-colors">
            <i class="fa-solid fa-times"></i>
        </button>
        
        <div class="p-8">
            <div class="flex items-center gap-3 mb-2">
                <span id="modalCategory" class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">Kategori</span>
                <span id="modalStatus" class="px-3 py-1 bg-florist-50 text-florist-600 text-xs font-bold rounded-full">STATUS</span>
            </div>
            
            <h2 id="modalTitle" class="text-3xl font-bold text-gray-800 mb-2">Nama Produk</h2>
            <div id="modalPrice" class="text-2xl font-bold text-florist-600 mb-6">Rp 0</div>
            
            <p id="modalDesc" class="text-gray-600 mb-8 text-sm leading-relaxed">Deskripsi produk...</p>
            
            <h4 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2"><i class="fa-solid fa-list-check mr-2 text-gray-400"></i> Resep / Komponen Bunga</h4>
            <ul id="modalComponents" class="divide-y divide-gray-100 bg-gray-50 rounded-lg p-4 mb-6">
                <!-- Injected via JS -->
            </ul>
            
            <div class="flex justify-end gap-3 mt-4">
                <button onclick="closeDetailModal()" class="px-6 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-bold">Tutup</button>
                <a id="modalCheckoutLink" href="#" class="inline-flex items-center px-6 py-2 bg-florist-500 text-white rounded-xl hover:bg-florist-600 font-bold shadow-md"><i class="fa-solid fa-cart-plus mr-2"></i> Proses ke Kasir</a>
            </div>
        </div>
    </div>
</div>

<script>
    function openDetailModal(id) {
        const template = document.getElementById(`product-data-${id}`);
        if(template) {
            document.getElementById('modalTitle').textContent = template.querySelector('.product-data-title').textContent;
            document.getElementById('modalPrice').textContent = template.querySelector('.product-data-price').textContent;
            document.getElementById('modalDesc').textContent = template.querySelector('.product-data-desc').textContent;
            document.getElementById('modalStatus').textContent = template.querySelector('.product-data-status').textContent;
            document.getElementById('modalCategory').textContent = template.querySelector('.product-data-category').textContent;
            document.getElementById('modalComponents').innerHTML = template.querySelector('.product-data-components').innerHTML;
            
            // Set dynamic checkout link
            document.getElementById('modalCheckoutLink').href = `/checkout/${id}`;
            
            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Close modal on click outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if(e.target === this) closeDetailModal();
    });
</script>
@endsection
