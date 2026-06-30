<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pilih Florist - Poppy Florist</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        if (window.top !== window.self) {
            window.top.location.href = window.self.location.href;
        }

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
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
        }

        /* Animated gradient background */
        .login-bg {
            background: linear-gradient(-45deg, #fdf2f8, #fce7f3, #ede9fe, #e0f2fe, #fdf2f8);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating petals */
        .petal {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            animation: floatPetal linear infinite;
        }
        @keyframes floatPetal {
            0% {
                opacity: 0;
                transform: translateY(0) rotate(0deg) scale(0.5);
            }
            10% { opacity: 0.7; }
            90% { opacity: 0.4; }
            100% {
                opacity: 0;
                transform: translateY(-100vh) rotate(720deg) scale(1.2);
            }
        }

        /* Glass card */
        .glass-login {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(24px) saturate(150%);
            -webkit-backdrop-filter: blur(24px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Florist card */
        .florist-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .florist-card::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(236, 72, 153, 0.08);
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }
        .florist-card:hover::before,
        .florist-card.selected::before {
            width: 300px;
            height: 300px;
        }
        .florist-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.15);
        }
        .florist-card.selected {
            transform: translateY(-4px) scale(1.02);
            border-color: #ec4899 !important;
            box-shadow: 0 12px 32px rgba(236, 72, 153, 0.25), 0 0 0 3px rgba(236, 72, 153, 0.12);
        }

        /* Avatar pulse */
        .avatar-glow {
            transition: all 0.4s ease;
        }
        .florist-card.selected .avatar-glow {
            box-shadow: 0 0 0 6px rgba(236, 72, 153, 0.15), 0 0 20px rgba(236, 72, 153, 0.2);
        }

        /* Checkmark */
        .check-badge {
            opacity: 0;
            transform: scale(0);
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .florist-card.selected .check-badge {
            opacity: 1;
            transform: scale(1);
        }

        /* Submit button shimmer */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -75%;
            width: 50%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: skewX(-25deg);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { left: -75%; }
            50% { left: 125%; }
        }
        .btn-shimmer:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 16px 40px rgba(236, 72, 153, 0.35);
        }
        .btn-shimmer:active {
            transform: translateY(0) scale(0.98);
        }
        .btn-shimmer:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
        .btn-shimmer:disabled::after {
            display: none;
        }

        /* Logo breathing */
        .logo-breathe {
            animation: breathe 3s ease-in-out infinite;
        }
        @keyframes breathe {
            0%, 100% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.3), 0 8px 24px rgba(236, 72, 153, 0.2); }
            50% { box-shadow: 0 0 0 12px rgba(236, 72, 153, 0), 0 8px 32px rgba(236, 72, 153, 0.35); }
        }

        /* Entrance animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .fade-up-delay-1 { animation-delay: 0.15s; }
        .fade-up-delay-2 { animation-delay: 0.3s; }
        .fade-up-delay-3 { animation-delay: 0.45s; }
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Live clock */
        .clock-text {
            font-variant-numeric: tabular-nums;
        }

        /* Touch */
        .touch-btn {
            touch-action: manipulation;
        }

        html.is-kiosk-mode body {
            overflow: hidden;
        }

        #kioskFrame {
            display: none;
        }
    </style>
</head>

<body class="login-bg min-h-screen flex flex-col items-center justify-center py-8 px-6 m-0 relative">

    {{-- Floating Petals Background --}}
    <div id="petals" class="fixed inset-0 overflow-hidden pointer-events-none z-0"></div>

    {{-- Main Login Container --}}
    <div id="loginContainer" class="w-full flex flex-col items-center relative z-10">

        {{-- Logo & Branding --}}
        <div class="mb-8 text-center fade-up">
            <div class="w-28 h-28 bg-gradient-to-br from-florist-500 via-pink-500 to-rose-400 rounded-[28px] flex items-center justify-center text-white mx-auto mb-5 shadow-2xl logo-breathe">
                <i class="fa-solid fa-leaf text-5xl drop-shadow-lg"></i>
            </div>
            <h1 class="text-5xl font-black text-gray-800 tracking-tight leading-none">Poppy Florist</h1>
            <p class="text-lg text-gray-400 mt-2 font-medium tracking-wide">Sistem Kasir (Point of Sale)</p>
        </div>

        {{-- Live Clock --}}
        <div class="mb-6 fade-up fade-up-delay-1">
            <div class="inline-flex items-center gap-3 bg-white/50 backdrop-blur-md border border-white/60 rounded-full px-6 py-2.5 shadow-sm">
                <i class="fa-regular fa-clock text-florist-500"></i>
                <span id="liveClock" class="clock-text text-sm font-bold text-gray-600">--:--:--</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span id="liveDate" class="text-sm font-medium text-gray-400">-- --- ----</span>
            </div>
        </div>

        {{-- Login Card --}}
        <div class="glass-login p-10 rounded-[36px] shadow-2xl w-full max-w-4xl fade-up fade-up-delay-2">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Pilih Florist Yang Bertugas</h2>
                <p class="text-gray-400 mt-1 text-sm">Sentuh kartu florist Anda untuk memulai</p>
            </div>

            <form id="loginForm" action="{{ route('pos.login.post') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @php
                        $florists = [
                            ['name' => 'Florist 1', 'icon' => 'fa-user-nurse',    'gradient' => 'from-pink-400 to-rose-500',    'ring' => 'ring-pink-200'],
                            ['name' => 'Florist 2', 'icon' => 'fa-user-astronaut','gradient' => 'from-violet-400 to-purple-500', 'ring' => 'ring-violet-200'],
                            ['name' => 'Florist 3', 'icon' => 'fa-user-ninja',    'gradient' => 'from-cyan-400 to-teal-500',     'ring' => 'ring-cyan-200'],
                            ['name' => 'Florist 4', 'icon' => 'fa-user-secret',   'gradient' => 'from-amber-400 to-orange-500',  'ring' => 'ring-amber-200'],
                        ];
                    @endphp

                    @foreach($florists as $idx => $f)
                    <label class="cursor-pointer group" onclick="selectFlorist(this)">
                        <input type="radio" name="florist_name" value="{{ $f['name'] }}" class="peer sr-only" required>
                        <div class="florist-card flex flex-col items-center p-7 bg-white border-2 border-gray-100 rounded-[24px] h-full touch-btn relative">
                            {{-- Check Badge --}}
                            <div class="check-badge absolute top-3 right-3 w-7 h-7 bg-florist-500 rounded-full flex items-center justify-center text-white shadow-md">
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>

                            {{-- Avatar --}}
                            <div class="avatar-glow w-20 h-20 bg-gradient-to-br {{ $f['gradient'] }} rounded-full flex items-center justify-center text-white mb-4 shadow-lg ring-4 {{ $f['ring'] }} group-hover:shadow-xl transition-shadow">
                                <i class="fa-solid {{ $f['icon'] }} text-3xl drop-shadow-sm"></i>
                            </div>

                            {{-- Name --}}
                            <span class="text-lg font-bold text-gray-700 group-hover:text-florist-600 transition-colors">{{ $f['name'] }}</span>

                            {{-- Status dot --}}
                            <div class="flex items-center gap-1.5 mt-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-xs text-gray-400 font-medium">Online</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="mt-10 text-center fade-up fade-up-delay-3">
                    <button type="submit" id="submitBtn" disabled
                        class="btn-shimmer touch-btn w-full md:w-auto px-16 py-5 bg-gradient-to-r from-florist-500 via-pink-500 to-rose-500 text-white text-xl font-extrabold rounded-2xl shadow-xl shadow-florist-200/50 tracking-wide uppercase">
                        <i class="fa-solid fa-desktop mr-3"></i>Mulai Mesin Kasir
                        <i class="fa-solid fa-arrow-right ml-3"></i>
                    </button>
                    <p class="text-xs text-gray-400 mt-4">Layar akan masuk mode fullscreen otomatis</p>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="mt-6 text-center fade-up fade-up-delay-3">
            <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} Poppy Florist &mdash; All rights reserved.</p>
        </div>
    </div>

    <iframe id="kioskFrame" class="fixed inset-0 w-full h-full border-none z-50 bg-white" src=""></iframe>

    <script>
        // --- Floating Petals ---
        function createPetals() {
            const container = document.getElementById('petals');
            const petalEmojis = ['🌸', '🌺', '🌷', '🌹', '💮', '🏵️', '✿', '❀'];
            const count = 18;

            for (let i = 0; i < count; i++) {
                const petal = document.createElement('div');
                petal.className = 'petal';
                petal.textContent = petalEmojis[Math.floor(Math.random() * petalEmojis.length)];
                petal.style.left = Math.random() * 100 + '%';
                petal.style.bottom = '-40px';
                petal.style.fontSize = (14 + Math.random() * 18) + 'px';
                petal.style.animationDuration = (8 + Math.random() * 12) + 's';
                petal.style.animationDelay = (Math.random() * 10) + 's';
                container.appendChild(petal);
            }
        }
        createPetals();

        // --- Live Clock ---
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('liveClock').textContent = timeStr;
            document.getElementById('liveDate').textContent = dateStr;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // --- Florist Card Selection ---
        function selectFlorist(label) {
            // Deselect all
            document.querySelectorAll('.florist-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Select clicked
            const card = label.querySelector('.florist-card');
            card.classList.add('selected');

            // Enable submit
            document.getElementById('submitBtn').disabled = false;
        }

        // --- Form Submit (Fullscreen + Kiosk) ---
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            // Request fullscreen on user gesture
            const elem = document.documentElement;
            try {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen().catch(err => console.log('Fullscreen error:', err));
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } catch (e) {
                console.log(e);
            }

            // Login via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.ok) {
                    openIframe();
                } else {
                    alert('Gagal login, silakan coba lagi.');
                    if (document.exitFullscreen) document.exitFullscreen();
                }
            }).catch(err => {
                alert('Terjadi kesalahan koneksi.');
                if (document.exitFullscreen) document.exitFullscreen();
            });
        });

        function openIframe() {
            document.documentElement.classList.add('is-kiosk-mode');
            document.getElementById('loginContainer').style.display = 'none';
            const frame = document.getElementById('kioskFrame');
            frame.src = "{{ route('pos.index') }}";
            frame.style.display = 'block';
        }

        // Listen for exit from inner iframe
        window.addEventListener('message', function(event) {
            if (event.data === 'exitKiosk') {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(e => console.log(e));
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }

                document.documentElement.classList.remove('is-kiosk-mode');
                document.getElementById('loginContainer').style.display = 'flex';
                document.getElementById('kioskFrame').style.display = 'none';
                document.getElementById('kioskFrame').src = "";

                // Deselect all florist cards
                document.querySelectorAll('.florist-card').forEach(card => card.classList.remove('selected'));
                document.querySelectorAll('input[name="florist_name"]').forEach(r => r.checked = false);
                document.getElementById('submitBtn').disabled = true;

                fetch("{{ route('pos.logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }
        });

        // Handle native fullscreen exit
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.msFullscreenElement) {
                console.log("Keluar dari fullscreen (Mungkin karena Print). Sesi tetap dipertahankan.");
            }
        });
    </script>
</body>

</html>