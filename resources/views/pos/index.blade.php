@extends('layouts.pos')

@section('content')
    <div class="h-full w-full bg-transparent flex items-center justify-center p-6">
        <div class="max-w-6xl w-full">
            <div class="text-center mb-10">
                <h2 id="kioskExitTrigger"
                    class="text-4xl font-extrabold text-gray-800 tracking-tight select-none cursor-pointer"
                    style="touch-action: manipulation; -webkit-user-select: none; user-select: none;">
                    Selamat Datang di Poppy Florist
                </h2>

                <p class="text-lg text-gray-500 mt-2 font-medium">
                    Silakan pilih jenis transaksi yang akan Anda lakukan saat ini
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <a href="{{ route('pos.catalog') }}"
                    class="group glass-card p-8 rounded-[32px] shadow-lg hover:shadow-2xl hover:shadow-florist-200/40 border border-white/40 hover:border-florist-400/50 transition-all duration-300 touch-btn flex items-center gap-6">
                    <div
                        class="w-24 h-24 bg-gradient-to-tr from-florist-500 to-pink-400 rounded-[24px] flex items-center justify-center text-white group-hover:scale-105 transition-transform shadow-lg shadow-florist-200/50">
                        <i class="fa-solid fa-gift text-4xl"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <h3
                            class="text-2xl font-black text-gray-800 mb-2 tracking-tight group-hover:text-florist-600 transition-colors">
                            Katalog Produk</h3>
                        <p class="text-gray-500 text-[15px] leading-snug font-medium">Menjual Buket Jadi, Papan Bunga, atau
                            Parcel yang sudah tersedia di toko.</p>
                    </div>
                    <i
                        class="fa-solid fa-chevron-right text-gray-400 text-xl group-hover:text-florist-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <a href="{{ route('pos.custom') }}"
                    class="group glass-card p-8 rounded-[32px] shadow-lg hover:shadow-2xl hover:shadow-purple-200/40 border border-white/40 hover:border-purple-400/50 transition-all duration-300 touch-btn flex items-center gap-6">
                    <div
                        class="w-24 h-24 bg-gradient-to-tr from-purple-600 to-indigo-400 rounded-[24px] flex items-center justify-center text-white group-hover:scale-105 transition-transform shadow-lg shadow-purple-200/50">
                        <i class="fa-solid fa-wand-magic-sparkles text-4xl"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <h3
                            class="text-2xl font-black text-gray-800 mb-2 tracking-tight group-hover:text-purple-600 transition-colors">
                            Custom Produk</h3>
                        <p class="text-gray-500 text-[15px] leading-snug font-medium">Merakit buket baru dari nol atau
                            tambah produk custom pesanan pelanggan.</p>
                    </div>
                    <i
                        class="fa-solid fa-chevron-right text-gray-400 text-xl group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                @php
                    $artificialCat = \App\Models\Category::where('slug', 'bunga-artificial')->first();
                @endphp

                <a href="{{ route('pos.catalog', ['category' => $artificialCat ? $artificialCat->id : '']) }}"
                    class="group glass-card p-8 rounded-[32px] shadow-lg hover:shadow-2xl hover:shadow-teal-200/40 border border-white/40 hover:border-teal-400/50 transition-all duration-300 touch-btn flex items-center gap-6">
                    <div
                        class="w-24 h-24 bg-gradient-to-tr from-teal-500 to-emerald-400 rounded-[24px] flex items-center justify-center text-white group-hover:scale-105 transition-transform shadow-lg shadow-teal-200/50">
                        <i class="fa-solid fa-fan text-4xl"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <h3
                            class="text-2xl font-black text-gray-800 mb-2 tracking-tight group-hover:text-teal-600 transition-colors">
                            Bunga Artificial</h3>
                        <p class="text-gray-500 text-[15px] leading-snug font-medium">Menjual buket atau produk bunga
                            buatan/artificial yang sudah dirangkai jadi.</p>
                    </div>
                    <i
                        class="fa-solid fa-chevron-right text-gray-400 text-xl group-hover:text-teal-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <a href="{{ route('pos.materials', ['type' => 'flower_fresh']) }}"
                    class="group glass-card p-8 rounded-[32px] shadow-lg hover:shadow-2xl hover:shadow-orange-200/40 border border-white/40 hover:border-orange-400/50 transition-all duration-300 touch-btn flex items-center gap-6">
                    <div
                        class="w-24 h-24 bg-gradient-to-tr from-orange-500 to-amber-400 rounded-[24px] flex items-center justify-center text-white group-hover:scale-105 transition-transform shadow-lg shadow-orange-200/50">
                        <i class="fa-solid fa-basket-shopping text-4xl"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <h3
                            class="text-2xl font-black text-gray-800 mb-2 tracking-tight group-hover:text-orange-600 transition-colors">
                            Bunga Batangan</h3>
                        <p class="text-gray-500 text-[15px] leading-snug font-medium">Menjual bunga batangan segar, boneka,
                            aksesoris, serta packaging secara eceran.</p>
                    </div>
                    <i
                        class="fa-solid fa-chevron-right text-gray-400 text-xl group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>
    </div>

    <div id="secretMenuModal" class="hidden fixed inset-0 z-[9999] bg-black/35 flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Menu Akses</h2>
                <button type="button" id="closeSecretMenu"
                    class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white text-2xl leading-none">
                    &times;
                </button>
            </div>

            <div class="p-6 space-y-3">
                <a href="{{ route('login') }}"
                    class="block w-full text-center bg-pink-500 hover:bg-pink-600 text-white py-4 rounded-xl font-bold">
                    🔐 Admin Login
                </a>
                <p class="text-xs text-gray-500 pt-2">
                    💡 Tip: Ketuk judul halaman 3x berturut-turut untuk membuka menu ini.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let clickCount = 0;
            let clickTimeout;

            const trigger = document.getElementById('kioskExitTrigger');
            const secretMenuModal = document.getElementById('secretMenuModal');
            const closeSecretMenu = document.getElementById('closeSecretMenu');
            const toggleFullscreenBtn = document.getElementById('toggleFullscreenBtn');
            const exitFullscreenBtn = document.getElementById('exitFullscreenBtn');

            function handleSecretClick(e) {
                e.preventDefault();
                clickCount++;
                console.log('Click count:', clickCount);
                clearTimeout(clickTimeout);
                
                clickTimeout = setTimeout(function() {
                    clickCount = 0;
                }, 1000);

                if (clickCount >= 3) {
                    clickCount = 0;
                    console.log('Opening secret menu');
                    if (secretMenuModal) {
                        secretMenuModal.classList.remove('hidden');
                    }
                }
            }

            if (trigger) {
                trigger.addEventListener('click', handleSecretClick);
                trigger.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    handleSecretClick(e);
                }, { passive: false });
            }

            if (closeSecretMenu) {
                closeSecretMenu.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    secretMenuModal.classList.add('hidden');
                });
            }

            if (secretMenuModal) {
                secretMenuModal.addEventListener('click', function(e) {
                    if (e.target === secretMenuModal) {
                        e.preventDefault();
                        e.stopPropagation();

                        secretMenuModal.classList.add('hidden');
                    }
                });
            }

            if (toggleFullscreenBtn) {
                toggleFullscreenBtn.addEventListener('click', function() {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        document.documentElement.requestFullscreen();
                    }
                });
            }

            if (exitFullscreenBtn) {
                exitFullscreenBtn.addEventListener('click', function() {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    }
                });
            }
        });
    </script>
@endsection
