<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Poppy Florist System')</title>
    <!-- Tailwind CSS (CDN for quick setup) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        florist: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4', // Pink pastel
                            400: '#f472b6',
                            500: '#ec4899', // Brand pink
                            600: '#db2777',
                            cream: '#fef3c7',
                            softpurple: '#ede9fe',
                            sidebar: '#ffffff',
                            bg: '#f8fafc'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
        }
        .card-modern {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(249, 168, 212, 0.2);
            transition: all 0.3s ease;
        }
        .card-modern:hover {
            box-shadow: 0 10px 15px -3px rgba(249, 168, 212, 0.2), 0 4px 6px -2px rgba(249, 168, 212, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="text-gray-800 antialiased flex h-[100dvh] overflow-hidden bg-slate-50">

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-slate-50">
        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6 pb-20">
            @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');

            if(btn && sidebar && overlay) {
                btn.addEventListener('click', () => {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                });
                overlay.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });
            }
        });
    </script>
    
    @if(auth()->check() && auth()->user()->role === 'florist')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Record the time this page was loaded as the baseline for checking new orders
            let lastCheckTime = Math.floor(Date.now() / 1000);
            let checkInterval = 10000; // 10 seconds

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
                        gain.gain.linearRampToValueAtTime(0.2, startTime + 0.05); // Attack lebih lembut
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration); // Menghilang perlahan
                        
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(startTime);
                        osc.stop(startTime + duration);
                    };

                    // Suara modern glassy / chime (Nada C6 - E6 - G6 berurutan cepat)
                    playNote(1046.50, ctx.currentTime, 1.0, 'sine');
                    playNote(1318.51, ctx.currentTime + 0.1, 1.2, 'sine');
                    playNote(1567.98, ctx.currentTime + 0.2, 1.8, 'sine');
                } catch (e) {
                    console.error("AudioContext not supported", e);
                }
            }

            function playVoiceNotification() {
                try {
                    // Menggunakan file MP3 lokal yang sudah di-download dari Google TTS
                    // Ini menjamin 100% suara bisa diputar di HP tanpa terblokir sistem keamanan
                    const url = "{{ asset('pesanan_masuk.mp3') }}";
                    const audio = new Audio(url);
                    audio.play().catch(e => console.log("Audio autoplay blocked", e));
                } catch (e) {
                    console.error("Failed to play TTS audio", e);
                }
            }

            setInterval(() => {
                fetch(`/api/check-new-orders?last_check=${lastCheckTime}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_pending) {
                            playBellSound();
                            setTimeout(() => { playVoiceNotification(); }, 1000);
                        }

                        if (data.has_new) {
                            lastCheckTime = Math.floor(Date.now() / 1000);
                            // Only auto-reload if we are actually on the kitchen or orders page
                            const path = window.location.pathname;
                            if (path.includes('/kitchen') || path.includes('/orders')) {
                                setTimeout(() => { window.location.reload(); }, 4000);
                            }
                        }
                    })
                    .catch(error => console.error('Error checking new orders:', error));
            }, checkInterval);
        });
    </script>
    @endif
</body>
</html>
