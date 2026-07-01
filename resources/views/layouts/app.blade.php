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
            @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex flex-col gap-1 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                    <p class="font-bold">Terjadi kesalahan:</p>
                </div>
                <ul class="list-disc list-inside ml-8 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
        // Register service worker for background notifications
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW registration failed:', err));
        }

        // Request notification permission on first click
        document.addEventListener('click', function requestNotifPermission() {
            document.removeEventListener('click', requestNotifPermission);
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
            // Subscribe to push notifications
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                subscribeToPush();
            }
        }, { once: true });

        async function subscribeToPush() {
            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array('{{ env('VAPID_PUBLIC_KEY') }}')
                });
                
                await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        endpoint: subscription.endpoint,
                        public_key: arrayBufferToBase64(subscription.getKey('p256dh')),
                        auth_token: arrayBufferToBase64(subscription.getKey('auth'))
                    })
                });
            } catch (err) {
                console.log('Push subscription failed:', err);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
        }

        function arrayBufferToBase64(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary);
        }

        // BroadcastChannel for cross-tab sync
        const orderChannel = new BroadcastChannel('orders');
        orderChannel.addEventListener('message', (event) => {
            if (event.data.type === 'play_notification') {
                playBellSound();
                setTimeout(() => { playVoiceNotification(); }, 1000);
            }
        });

        // Helper for browser notifications
        window.showBrowserNotification = function(title, body) {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(title, { body, icon: '/favicon.ico', tag: 'new-order' });
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Unlock audio on first user interaction (required for autoplay policy)
            let audioUnlocked = false;
            const unlockAudio = () => {
                if (!audioUnlocked) {
                    audioUnlocked = true;
                    document.removeEventListener('click', unlockAudio);
                    document.removeEventListener('keydown', unlockAudio);
                    document.removeEventListener('touchstart', unlockAudio);
                }
            };
            document.addEventListener('click', unlockAudio);
            document.addEventListener('keydown', unlockAudio);
            document.addEventListener('touchstart', unlockAudio);

// Record the time this page was loaded as the baseline for checking new orders
            let lastCheckTime = Math.floor(Date.now() / 1000);
            let checkInterval = 10000; // 10 seconds
            let lastNotifiedCount = 0;

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
                        gain.gain.linearRampToValueAtTime(0.2, startTime + 0.05);
                        gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                        
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
                    const url = "{{ asset('pesanan_masuk.mp3') }}";
                    const audio = new Audio(url);
                    audio.play().catch(e => console.log("Audio autoplay blocked", e));
} catch (e) {
                    console.error("Failed to play TTS audio", e);
                }
            }
            
            // Expose for manual trigger from kitchen page
            window.playKitchenNotification = function() {
                playBellSound();
                playVoiceNotification();
            }

// Check both new orders AND pending count to play repeat notifications
            let lastPendingCount = 0;
            let notificationPlayed = false;
            
            async function checkAndNotify() {
                try {
                    const response = await fetch(`/api/check-new-orders?last_check=${lastCheckTime}`);
                    const data = await response.json();
                    
                    console.log('Check orders API response:', data);
                    
                    // Play for new orders
                    if (data.has_new) {
                        playBellSound();
                        setTimeout(() => { playVoiceNotification(); }, 1000);
                        lastCheckTime = Math.floor(Date.now() / 1000);
                    }
                    
                    // Play once after user interaction if there are pending orders
                    if (data.pending > 0 && !notificationPlayed && audioUnlocked) {
                        playBellSound();
                        setTimeout(() => { playVoiceNotification(); }, 1000);
                        notificationPlayed = true;
                    }
                    
                    // Reset when no pending orders
                    if (data.pending === 0) {
                        notificationPlayed = false;
                    }
                    
                    // Sync pending count via BroadcastChannel
                    if (data.pending > 0) {
                        orderChannel.postMessage({type: 'pending_update', count: data.pending});
                    }
                } catch (error) {
                    console.error('Error checking new orders:', error);
                }
            }

            setInterval(checkAndNotify, checkInterval);
        });
    </script>
    @endif
</body>
</html>
