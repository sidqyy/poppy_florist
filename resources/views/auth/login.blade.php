<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Poppy Florist</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
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

<body class="bg-florist-50 text-gray-800 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-florist-100 overflow-hidden">

        <div class="p-8 text-center bg-gradient-to-br from-florist-100 to-white">
            <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-florist-100">
                <i class="fa-solid fa-leaf text-3xl text-florist-500"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Poppy Florist</h2>
            <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Toko Internal</p>
        </div>

        <div class="p-8 pt-4">

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        Username
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-gray-400"></i>
                        </div>

                        <input type="text" name="username" id="username"
                            class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 focus:border-florist-400 outline-none transition-all"
                            placeholder="Misal: admin, florist1" required value="{{ old('username') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>

                        <input type="password" name="password" id="password"
                            class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 focus:border-florist-400 outline-none transition-all"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit"
                        class="w-full py-3 px-4 bg-florist-500 hover:bg-florist-600 text-white font-semibold rounded-lg shadow-md shadow-florist-200 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-florist-500">
                        Masuk ke Sistem
                    </button>

                    <a href="{{ route('pos.login') }}"
                        class="block w-full text-center py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Kembali ke POS
                    </a>
                </div>
            </form>

            <div class="mt-8 text-center text-xs text-gray-400 border-t border-gray-100 pt-4">
                <p>Gunakan akun yang tersedia:</p>

                <div class="mt-2 flex flex-wrap justify-center gap-2">
                    <span class="bg-gray-100 px-2 py-1 rounded">admin</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">marketing1</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">florist1</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">asmen</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">owner</span>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
