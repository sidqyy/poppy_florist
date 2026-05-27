<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Poppy Florist - Kiosk Mode</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <style>
        body { font-family: 'Outfit', sans-serif; background: #fdf2f8; margin: 0; overflow: hidden; height: 100vh; width: 100vw; }
        #kioskFrame { width: 100%; height: 100%; border: none; display: none; }
        #startScreen { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(236, 72, 153, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(236, 72, 153, 0); }
        }
    </style>
</head>
<body>
    <div id="startScreen">
        <i class="fa-solid fa-desktop text-6xl text-florist-500 mb-6"></i>
        <h1 class="text-4xl font-extrabold text-gray-800 mb-2">POS KIOSK MODE</h1>
        <p class="text-lg text-gray-500 mb-10">Sistem Kasir Layar Sentuh Poppy Florist</p>
        
        <button onclick="startKiosk()" class="pulse bg-florist-500 hover:bg-florist-600 text-white font-bold py-5 px-10 rounded-full shadow-xl shadow-florist-200 text-2xl transition-transform active:scale-95 flex items-center gap-4">
            <i class="fa-solid fa-expand"></i> Sentuh untuk Mulai Layar Penuh
        </button>
        <a href="{{ route('pos.index') }}" class="mt-8 text-florist-500 font-medium hover:underline">Kembali ke Tampilan Biasa</a>
    </div>

    <iframe id="kioskFrame" src=""></iframe>

    <script>
        function startKiosk() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen().then(openIframe).catch(err => {
                    alert('Gagal masuk mode layar penuh: ' + err.message);
                });
            } else if (elem.webkitRequestFullscreen) { /* Safari */
                elem.webkitRequestFullscreen();
                openIframe();
            } else if (elem.msRequestFullscreen) { /* IE11 */
                elem.msRequestFullscreen();
                openIframe();
            }
        }

        function openIframe() {
            document.getElementById('startScreen').style.display = 'none';
            const frame = document.getElementById('kioskFrame');
            frame.src = "{{ route('pos.index') }}"; // Load only when entering
            frame.style.display = 'block';
        }

        document.addEventListener('fullscreenchange', checkFullscreen);
        document.addEventListener('webkitfullscreenchange', checkFullscreen);
        document.addEventListener('msfullscreenchange', checkFullscreen);

        function checkFullscreen() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.msFullscreenElement) {
                document.getElementById('startScreen').style.display = 'flex';
                document.getElementById('kioskFrame').style.display = 'none';
                document.getElementById('kioskFrame').src = ""; // Unload iframe
            }
        }

        window.addEventListener('message', function(event) {
            if (event.data === 'exitKiosk') {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) { /* Safari */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) { /* IE11 */
                    document.msExitFullscreen();
                }
            }
        });
    </script>
</body>
</html>
