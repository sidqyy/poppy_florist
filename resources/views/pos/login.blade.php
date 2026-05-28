<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pilih Florist - Poppy Florist</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .touch-btn {
            touch-action: manipulation;
            transition: transform 0.1s, box-shadow 0.1s;
        }

        html.is-kiosk-mode body {
            overflow: hidden;
        }

        #kioskFrame {
            display: none;
        }
    </style>
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
</head>

<body class="bg-florist-50 min-h-screen flex flex-col items-center justify-center py-10 px-6 m-0">

    <div id="loginContainer" class="w-full flex flex-col items-center">
        <div class="mb-10 text-center">
            <div class="w-24 h-24 bg-florist-500 rounded-full flex items-center justify-center text-white mx-auto mb-4 shadow-xl">
                <i class="fa-solid fa-leaf text-5xl"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Poppy Florist</h1>
            <p class="text-xl text-gray-500 mt-2">Sistem Kasir (Point of Sale)</p>
        </div>

        <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-4xl border border-florist-100">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Pilih Florist Yang Bertugas</h2>

            <form id="loginForm" action="{{ route('pos.login.post') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Florist 1 -->
                    <label class="cursor-pointer">
                        <input type="radio" name="florist_name" value="Florist 1" class="peer sr-only" required>
                        <div class="flex flex-col items-center p-6 border-4 border-gray-100 rounded-2xl peer-checked:border-florist-500 peer-checked:bg-florist-50 transition-all touch-btn h-full">
                            <div class="w-16 h-16 bg-florist-100 rounded-full flex items-center justify-center text-florist-600 mb-4">
                                <i class="fa-solid fa-circle-user text-3xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-700">Florist 1</span>
                        </div>
                    </label>

                    <!-- Florist 2 -->
                    <label class="cursor-pointer">
                        <input type="radio" name="florist_name" value="Florist 2" class="peer sr-only">
                        <div class="flex flex-col items-center p-6 border-4 border-gray-100 rounded-2xl peer-checked:border-florist-500 peer-checked:bg-florist-50 transition-all touch-btn h-full">
                            <div class="w-16 h-16 bg-florist-100 rounded-full flex items-center justify-center text-florist-600 mb-4">
                                <i class="fa-solid fa-circle-user text-3xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-700">Florist 2</span>
                        </div>
                    </label>

                    <!-- Florist 3 -->
                    <label class="cursor-pointer">
                        <input type="radio" name="florist_name" value="Florist 3" class="peer sr-only">
                        <div class="flex flex-col items-center p-6 border-4 border-gray-100 rounded-2xl peer-checked:border-florist-500 peer-checked:bg-florist-50 transition-all touch-btn h-full">
                            <div class="w-16 h-16 bg-florist-100 rounded-full flex items-center justify-center text-florist-600 mb-4">
                                <i class="fa-solid fa-circle-user text-3xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-700">Florist 3</span>
                        </div>
                    </label>

                    <!-- Florist 4 -->
                    <label class="cursor-pointer">
                        <input type="radio" name="florist_name" value="Florist 4" class="peer sr-only">
                        <div class="flex flex-col items-center p-6 border-4 border-gray-100 rounded-2xl peer-checked:border-florist-500 peer-checked:bg-florist-50 transition-all touch-btn h-full">
                            <div class="w-16 h-16 bg-florist-100 rounded-full flex items-center justify-center text-florist-600 mb-4">
                                <i class="fa-solid fa-circle-user text-3xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-700">Florist 4</span>
                        </div>
                    </label>
                </div>

                <div class="mt-12 text-center">
                    <button type="submit" class="touch-btn w-full md:w-auto px-16 py-5 bg-florist-500 hover:bg-florist-600 text-white text-2xl font-bold rounded-2xl shadow-xl shadow-florist-200">
                        MULAI MESIN KASIR <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>


    </div>

    <iframe id="kioskFrame" class="fixed inset-0 w-full h-full border-none z-50 bg-white" src=""></iframe>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            // 1. Minta fullscreen secara sinkron tepat saat tombol diklik (User Gesture)
            const elem = document.documentElement;
            try {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen().catch(err => console.log('Fullscreen dipaksa batal atau tidak didukung:', err));
                } else if (elem.webkitRequestFullscreen) {
                    /* Safari */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    /* IE11 */
                    elem.msRequestFullscreen();
                }
            } catch (e) {
                console.log(e);
            }

            // 2. Lakukan login di background
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
                document.getElementById('kioskFrame').src = ""; // Unload iframe to logout

                fetch("{{ route('pos.logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }
        });

        // Handle native fullscreen exit (Esc key)
        // Auto-logout dihilangkan agar print dialog tidak memicu logout otomatis
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.msFullscreenElement) {
                console.log("Keluar dari fullscreen (Mungkin karena Print). Sesi tetap dipertahankan.");
            }
        });
    </script>
</body>

</html>