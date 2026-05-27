@extends('layouts.kiosk')

@section('content')
<div class="h-full w-full flex flex-col items-center justify-center bg-gradient-to-br from-florist-100 to-florist-50 p-8 relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute -top-20 -left-20 text-florist-200 opacity-50 text-[300px]">
        <i class="fa-solid fa-leaf"></i>
    </div>
    <div class="absolute -bottom-20 -right-20 text-florist-200 opacity-50 text-[300px]">
        <i class="fa-solid fa-flower"></i>
    </div>

    <div class="z-10 text-center flex flex-col items-center">
        <div class="w-40 h-40 bg-white rounded-full flex items-center justify-center text-florist-500 shadow-xl mb-8 animate-bounce" style="animation-duration: 2s;">
            <i class="fa-solid fa-hand-pointer text-7xl"></i>
        </div>
        
        <h1 class="text-6xl font-extrabold text-gray-800 mb-4 tracking-tight">Selamat Datang</h1>
        <p class="text-2xl text-gray-600 mb-12">Pilih bunga favorit Anda, tanpa perlu antre!</p>
        
        <button onclick="window.location.href='{{ route('kiosk.catalog') }}'" class="touch-btn group relative inline-flex items-center justify-center px-12 py-6 text-3xl font-bold text-white transition-all duration-200 bg-florist-500 border-none rounded-full shadow-2xl hover:bg-florist-600 focus:outline-none focus:ring-4 focus:ring-florist-300">
            <span class="mr-4">Sentuh Layar Untuk Memulai</span>
            <i class="fa-solid fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
        </button>
    </div>
</div>
@endsection
