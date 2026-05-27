<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

<aside id="main-sidebar" class="fixed md:static inset-y-0 left-0 w-64 bg-white shadow-xl flex-shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50 flex flex-col border-r border-gray-100">
    <div class="h-16 flex items-center justify-center border-b border-gray-100 px-6 bg-white">
        <h1 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-orange-400 tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-leaf text-green-500"></i> Poppy Florist
        </h1>
    </div>
    
    <div class="flex-1 overflow-y-auto py-6 px-3">
        <ul class="space-y-1">
            <!-- General Dashboard Link based on Role -->
            @auth
            @php
                $dashRoute = '/' . auth()->user()->role;
                if (in_array(auth()->user()->role, ['asmen', 'it support'])) {
                    $dashRoute = '/admin';
                }
            @endphp
            <li>
                <a href="{{ $dashRoute }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-pink-600 hover:bg-pink-50 rounded-xl transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-pink-100/50 flex items-center justify-center group-hover:bg-pink-200/50 transition-colors">
                        <i class="fa-solid fa-house text-pink-500"></i>
                    </div>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            @endauth

            <!-- Global Links (Except Florist and Marketing) -->
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'asmen', 'it support', 'owner']))
            <li class="pt-5 pb-2 px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Katalog & Transaksi</li>
            <li>
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('catalog.index') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('catalog.index') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-store {{ request()->routeIs('catalog.index') ? 'text-pink-600' : 'text-indigo-400' }}"></i>
                    </div>
                    <span>Katalog Etalase</span>
                </a>
            </li>
            <li>
                <a href="{{ route('custom.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('custom.index') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('custom.index') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-wand-magic-sparkles {{ request()->routeIs('custom.index') ? 'text-pink-600' : 'text-amber-400' }}"></i>
                    </div>
                    <span>Buat Custom Bucket</span>
                </a>
            </li>
            <li>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('orders.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('orders.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-receipt {{ request()->routeIs('orders.*') ? 'text-pink-600' : 'text-emerald-400' }}"></i>
                    </div>
                    <span>Daftar Pesanan</span>
                </a>
            </li>
            @endif

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'asmen', 'it support', 'owner']))
            <li class="pt-5 pb-2 px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                Laporan & Sistem
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-chart-pie {{ request()->routeIs('admin.reports.*') ? 'text-pink-600' : 'text-blue-400' }}"></i>
                    </div>
                    <span>Laporan Penjualan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.audit-logs.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-shield-halved {{ request()->routeIs('admin.audit-logs.*') ? 'text-pink-600' : 'text-purple-400' }}"></i>
                    </div>
                    <span>Audit Log</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-sliders {{ request()->routeIs('admin.settings.*') ? 'text-pink-600' : 'text-orange-400' }}"></i>
                    </div>
                    <span>Pengaturan Toko</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.backups.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.backups.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-cloud-arrow-down {{ request()->routeIs('admin.backups.*') ? 'text-pink-600' : 'text-cyan-400' }}"></i>
                    </div>
                    <span>Backup Database</span>
                </a>
            </li>
            @endif

            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'asmen', 'it support']))
            <li class="pt-5 pb-2 px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Master Data & Stok</li>
            <li>
                <a href="{{ route('marketing.products.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('marketing.products.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('marketing.products.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-box-open {{ request()->routeIs('marketing.products.*') ? 'text-pink-600' : 'text-amber-500' }}"></i>
                    </div>
                    <span>Katalog Produk (Barang)</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-tags {{ request()->routeIs('admin.categories.*') ? 'text-pink-600' : 'text-rose-400' }}"></i>
                    </div>
                    <span>Kategori (Occasion)</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.materials.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.materials.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.materials.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-leaf {{ request()->routeIs('admin.materials.*') ? 'text-pink-600' : 'text-lime-500' }}"></i>
                    </div>
                    <span>Bahan Baku</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.stocks.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.stocks.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('admin.stocks.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-boxes-stacked {{ request()->routeIs('admin.stocks.*') ? 'text-pink-600' : 'text-sky-400' }}"></i>
                    </div>
                    <span>Mutasi Stok</span>
                </a>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->role === 'florist')
            <li class="pt-5 pb-2 px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Dapur Florist</li>
            <li>
                <a href="{{ route('kitchen.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kitchen.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('kitchen.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-fire-burner {{ request()->routeIs('kitchen.*') ? 'text-pink-600' : 'text-orange-500' }}"></i>
                    </div>
                    <span>Antrian Dapur</span>
                </a>
            </li>
            <li>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-list-check {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'text-pink-600' : 'text-cyan-500' }}"></i>
                    </div>
                    <span>Daftar Pesanan</span>
                </a>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->role === 'marketing')
            <li class="pt-5 pb-2 px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Marketing & Order</li>
            <li>
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('catalog.index') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('catalog.index') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-store {{ request()->routeIs('catalog.index') ? 'text-pink-600' : 'text-indigo-400' }}"></i>
                    </div>
                    <span>Lihat Etalase</span>
                </a>
            </li>
            <li>
                <a href="{{ route('marketing.products.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('marketing.products.*') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('marketing.products.*') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-box-open {{ request()->routeIs('marketing.products.*') ? 'text-pink-600' : 'text-amber-500' }}"></i>
                    </div>
                    <span>Kelola Katalog Produk</span>
                </a>
            </li>
            <li>
                <a href="{{ route('custom.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('custom.index') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('custom.index') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-calculator {{ request()->routeIs('custom.index') ? 'text-pink-600' : 'text-amber-400' }}"></i>
                    </div>
                    <span>Kalkulator Custom</span>
                </a>
            </li>
            <li>
                <a href="{{ route('orders.online.create') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('orders.online.create') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('orders.online.create') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-mobile-screen-button {{ request()->routeIs('orders.online.create') ? 'text-pink-600' : 'text-teal-400' }}"></i>
                    </div>
                    <span>Input Order WA/IG</span>
                </a>
            </li>
            <li>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'bg-gradient-to-r from-pink-50 to-white border-l-4 border-pink-500 text-pink-600 font-bold' : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600 border-l-4 border-transparent' }} rounded-r-xl transition-all">
                    <div class="w-8 h-8 rounded-lg {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'bg-pink-100' : 'bg-gray-50' }} flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-list-check {{ request()->routeIs('orders.*') && !request()->routeIs('orders.online.create') ? 'text-pink-600' : 'text-emerald-400' }}"></i>
                    </div>
                    <span>Lacak Pesanan</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
    
    <div class="p-4 bg-white border-t border-gray-100">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 rounded-xl font-medium transition-colors group">
                <div class="w-8 h-8 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-power-off"></i>
                </div>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarScrollArea = document.querySelector('aside .overflow-y-auto');
        if (sidebarScrollArea) {
            const scrollPos = sessionStorage.getItem('sidebarScrollPos');
            if (scrollPos) {
                sidebarScrollArea.scrollTop = parseInt(scrollPos, 10);
            }

            // Save position on scroll
            sidebarScrollArea.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebarScrollArea.scrollTop);
            });
            
            // Also save position on link click, to be absolutely sure
            sidebarScrollArea.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('sidebarScrollPos', sidebarScrollArea.scrollTop);
                });
            });
        }
    });
</script>
