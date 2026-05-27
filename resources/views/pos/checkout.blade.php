@extends('layouts.kiosk')

@section('content')
<div class="h-full w-full flex bg-gray-50">
    <!-- Left: Form -->
    <div class="flex-1 flex flex-col justify-center items-center p-8">
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl p-10 border border-florist-100 relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-florist-50 rounded-full opacity-50"></div>
            
            <h2 class="text-4xl font-extrabold text-gray-800 mb-2 relative z-10">Data Pemesan</h2>
            <p class="text-xl text-gray-500 mb-10 relative z-10">Siapa nama Anda untuk panggilan pesanan?</p>
            
            <form action="{{ route('kiosk.store') }}" method="POST" id="checkoutForm" class="relative z-10">
                @csrf
                
                <div class="mb-8">
                    <label class="block text-gray-700 text-2xl font-bold mb-4">Nama Anda <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" required autocomplete="off" 
                        class="w-full bg-gray-50 border-2 border-gray-200 text-gray-800 text-3xl rounded-2xl focus:ring-0 focus:border-florist-400 block p-6 transition-all outline-none" 
                        placeholder="Contoh: Budi">
                </div>
                
                <div class="mb-10">
                    <label class="block text-gray-700 text-2xl font-bold mb-4">Nomor WhatsApp (Opsional)</label>
                    <input type="tel" name="customer_phone" autocomplete="off" 
                        class="w-full bg-gray-50 border-2 border-gray-200 text-gray-800 text-3xl rounded-2xl focus:ring-0 focus:border-florist-400 block p-6 transition-all outline-none" 
                        placeholder="0812...">
                </div>
                
                <div class="flex gap-4">
                    <button type="button" onclick="window.location.href='{{ route('kiosk.catalog') }}'" class="touch-btn w-1/3 py-6 bg-gray-200 hover:bg-gray-300 text-gray-700 text-2xl font-bold rounded-2xl transition-colors">
                        Kembali
                    </button>
                    <button type="submit" class="touch-btn w-2/3 py-6 bg-florist-500 hover:bg-florist-600 text-white text-2xl font-bold rounded-2xl flex justify-center items-center gap-3 shadow-lg shadow-florist-200">
                        Selesaikan Pesanan <i class="fa-solid fa-check-circle"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Right: Order Summary Sidebar -->
    <div class="w-[450px] bg-white shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.05)] z-20 flex flex-col border-l border-gray-100">
        <div class="p-8 border-b border-gray-100 bg-gray-50">
            <h2 class="text-2xl font-bold text-gray-800">Ringkasan Pesanan</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @foreach($cart as $item)
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">{{ $item['name'] }}</h4>
                    <p class="text-gray-500">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                </div>
                <div class="text-lg font-bold text-gray-800">
                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="p-8 bg-florist-50 border-t border-florist-100">
            <div class="flex justify-between items-center mb-6">
                <span class="text-xl text-gray-600 font-bold">Total Pembayaran</span>
                <span class="text-4xl font-extrabold text-florist-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-florist-100 text-center text-gray-600 font-medium">
                <i class="fa-solid fa-info-circle text-florist-500 mb-2 text-xl"></i>
                <p>Pembayaran dilakukan di meja Kasir setelah pemesanan selesai.</p>
                <p class="text-sm mt-1">Menerima: Cash / Transfer Bank / QRIS</p>
            </div>
        </div>
    </div>
</div>
@endsection
