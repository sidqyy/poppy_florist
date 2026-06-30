<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Poppy Florist - Self Service</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        florist: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            900: '#831843'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <script>
        if (window.top !== window.self) {
            document.documentElement.classList.add('is-kiosk-mode');
        }
    </script>

    <style>
        html.is-kiosk-mode #posHeader {
            display: none !important;
        }

        body {
            background-color: #fdf2f8;
            overscroll-behavior-y: none;
            user-select: none;
            -webkit-user-select: none;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(236, 72, 153, 0.3);
            border-radius: 20px;
        }

        .touch-btn {
            transition: transform 0.1s;
        }

        .touch-btn:active {
            transform: scale(0.95);
        }

        .fade-in {
            animation: fadeIn 0.25s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-out {
            animation: fadeOut 0.15s ease-in forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
    </style>
</head>

@php
    $printOrderId = session('print_order_id');
    $latestOrder = $printOrderId ? \App\Models\Order::with('items')->find($printOrderId) : null;
    $payment = $latestOrder ? \App\Models\Payment::where('order_id', $printOrderId)->first() : null;
    $change = ($latestOrder && $payment && $payment->amount >= $latestOrder->total_amount)
        ? $payment->amount - $latestOrder->total_amount
        : 0;

    $printData = null;

    if ($latestOrder) {
        $printData = [
            'url' => route('orders.print', $printOrderId),
            'order_number' => $latestOrder->order_number,
            'customer_name' => $latestOrder->customer_name,
            'items_count' => $latestOrder->items->count(),
            'total_amount' => 'Rp ' . number_format($latestOrder->total_amount, 0, ',', '.'),
            'payment_amount' => $payment ? 'Rp ' . number_format($payment->amount, 0, ',', '.') : null,
            'payment_method' => $payment ? strtoupper($payment->payment_method) : null,
            'change' => 'Rp ' . number_format($change, 0, ',', '.'),
        ];
    }
@endphp

<body class="h-screen w-full overflow-hidden flex flex-col text-gray-800">

    <header id="posHeader" class="bg-white shadow-sm p-4 flex justify-between items-center z-10 shrink-0">
        <div class="flex items-center gap-4">
            @if (request()->route()->getName() !== 'pos.index')
                <a href="{{ route('pos.index') }}"
                    class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-2xl flex items-center justify-center text-gray-600 transition-colors touch-btn">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
            @endif

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-florist-100 rounded-full flex items-center justify-center text-florist-500">
                    <i class="fa-solid fa-desktop text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 leading-tight">POPPY FLORIST</h1>
                    <p class="text-sm text-florist-500 font-medium">Point of Sale (POS) Kasir</p>
                </div>
            </div>
        </div>

        <div class="flex gap-4 items-center">
            <button id="enterKioskBtn" type="button" onclick="enterKioskMode()"
                class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-600 transition-colors touch-btn"
                title="Fullscreen Mode">
                <i class="fa-solid fa-expand"></i>
            </button>

            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl border-2 border-gray-200 shadow-md">
                <div class="w-8 h-8 bg-florist-500 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(session('pos_florist', 'K'), 0, 1) }}
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">KASIR BERTUGAS</p>
                    <p class="text-sm font-bold text-gray-800 leading-none">{{ session('pos_florist', 'Kasir') }}</p>
                </div>
            </div>

            <form action="{{ route('pos.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="touch-btn flex items-center gap-2 px-5 py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-2xl font-bold border border-red-100">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Ganti Florist / Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="flex-1 overflow-hidden relative fade-in">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 p-2 text-center text-xs text-gray-500 font-medium shrink-0 z-10">
        &copy; {{ date('Y') }} Poppy Florist. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script id="pos-data" type="application/json">
        {!! json_encode([
            'printData' => $printData,
            'successMessage' => session('success'),
            'errorMessage' => session('error'),
            'validationErrors' => $errors->any() ? $errors->all() : []
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>

    <script>
        const posDataEl = document.getElementById('pos-data');
        const posData = posDataEl ? JSON.parse(posDataEl.textContent || '{}') : {};
        const printData = posData.printData;
        const successMessage = posData.successMessage;
        const errorMessage = posData.errorMessage;
        const validationErrors = posData.validationErrors || [];

        function enterKioskMode() {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a').forEach(link => {
                if (
                    link.hostname === window.location.hostname &&
                    !link.target &&
                    !link.href.includes('#') &&
                    !link.hasAttribute('download')
                ) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        const targetUrl = this.href;
                        const mainContent = document.querySelector('main');

                        if (mainContent) {
                            mainContent.classList.remove('fade-in');
                            mainContent.classList.add('fade-out');

                            setTimeout(() => {
                                window.location.href = targetUrl;
                            }, 150);
                        } else {
                            window.location.href = targetUrl;
                        }
                    });
                }
            });

            document.querySelectorAll('form').forEach(form => {
                if (!form.target) {
                    form.addEventListener('submit', function() {
                        const action = form.getAttribute('action') || '';

                        if (
                            action.includes('/pos/cart/') ||
                            action.includes('/logout')
                        ) {
                            return;
                        }

                        const mainContent = document.querySelector('main');

                        if (mainContent) {
                            mainContent.classList.remove('fade-in');
                            mainContent.classList.add('fade-out');
                        }
                    });
                }
            });

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successMessage,
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage,
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }

            if (validationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    html: '<ul>' + validationErrors.map(err => `<li>${err}</li>`).join('') + '</ul>',
                    confirmButtonColor: '#EC4899'
                });
            }

            if (printData) {
                Swal.fire({
                    title: 'Pesanan Berhasil!',
                    html: `
                        <div class="text-left mt-2">
                            <div class="bg-gray-50 rounded-xl p-4 border-2 border-gray-200 shadow-md text-sm">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-500">No. Pesanan:</span>
                                    <span class="font-bold text-gray-800">${printData.order_number}</span>
                                </div>

                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-500">Pelanggan:</span>
                                    <span class="font-bold text-gray-800">${printData.customer_name}</span>
                                </div>

                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-500">Total Item:</span>
                                    <span class="font-bold text-gray-800">${printData.items_count} Item</span>
                                </div>

                                <div class="flex justify-between mb-2 border-t pt-2 mt-2">
                                    <span class="text-gray-600 font-bold">Total Tagihan:</span>
                                    <span class="font-extrabold text-florist-600">${printData.total_amount}</span>
                                </div>

                                ${printData.payment_amount ? `
                                    <div class="flex justify-between mb-1">
                                        <span class="text-gray-500">Nominal Bayar:</span>
                                        <span class="font-bold text-gray-800">${printData.payment_amount} (${printData.payment_method})</span>
                                    </div>

                                    <div class="flex justify-between border-t pt-2 mt-2">
                                        <span class="text-gray-600 font-bold text-base">Kembalian:</span>
                                        <span class="font-extrabold text-green-500 text-lg">${printData.change}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#EC4899',
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: '<i class="fa-solid fa-print"></i> Cetak Struk',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const printWindow = window.open(printData.url, '_blank');

                        if (!printWindow) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Popup Diblokir',
                                text: 'Izinkan popup browser agar struk bisa dicetak.'
                            });
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>