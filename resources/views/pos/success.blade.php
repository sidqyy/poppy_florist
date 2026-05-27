@extends('layouts.kiosk')

@section('content')
<div class="h-full w-full flex flex-col items-center justify-center bg-gradient-to-br from-florist-50 to-white p-8 relative overflow-hidden">
    
    <!-- Confetti / Decorative -->
    <div class="absolute top-20 left-1/4 text-florist-300 opacity-60 text-4xl animate-pulse"><i class="fa-solid fa-star"></i></div>
    <div class="absolute bottom-32 right-1/4 text-yellow-300 opacity-60 text-5xl animate-bounce"><i class="fa-solid fa-star"></i></div>
    <div class="absolute top-1/3 right-20 text-blue-300 opacity-40 text-6xl"><i class="fa-solid fa-circle"></i></div>

    <div class="z-10 text-center flex flex-col items-center bg-white p-16 rounded-[3rem] shadow-2xl border-4 border-florist-100 max-w-3xl w-full">
        
        <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center text-green-500 mb-8 border-8 border-green-50">
            <i class="fa-solid fa-check text-6xl"></i>
        </div>
        
        <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-2xl text-gray-500 mb-10">Terima kasih, <span class="font-bold text-gray-800">{{ $order->customer_name }}</span></p>
        
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-3xl p-8 mb-10 w-full">
            <p class="text-xl text-gray-500 mb-2 font-medium uppercase tracking-wider">Nomor Pesanan Anda</p>
            <p class="text-6xl font-black text-florist-500 tracking-widest">{{ $order->order_number }}</p>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-6 rounded-2xl mb-12 w-full flex items-center gap-6 text-left">
            <i class="fa-solid fa-bell-concierge text-4xl text-yellow-500"></i>
            <div>
                <h4 class="text-2xl font-bold mb-1">Langkah Selanjutnya:</h4>
                <p class="text-xl">Silakan tunjukkan nomor pesanan ini ke meja <strong>KASIR</strong> untuk melakukan pembayaran (Cash / Transfer / QRIS) agar pesanan segera dirangkai.</p>
            </div>
        </div>
        
        <button onclick="window.location.href='{{ route('kiosk.welcome') }}'" class="touch-btn px-12 py-6 text-2xl font-bold text-white bg-gray-800 rounded-full hover:bg-black transition-colors shadow-xl">
            Kembali ke Layar Awal
        </button>
    </div>
</div>

<script>
    // Auto redirect after 30 seconds
    setTimeout(function() {
        window.location.href = "{{ route('kiosk.welcome') }}";
    }, 30000);
</script>
@endsection
