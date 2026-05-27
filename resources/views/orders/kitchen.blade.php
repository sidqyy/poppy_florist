@extends('layouts.app')

@section('title', 'Dapur Florist (Kitchen)')
@section('page_title', 'Dapur Florist (Kitchen Dashboard)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Antrian Rangkaian Bunga</h3>
        <p class="text-gray-500 text-sm mt-1">Daftar pesanan yang harus dikerjakan florist hari ini.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 shadow-sm font-medium">
    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($orders as $order)
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border {{ $order->is_urgent ? 'border-red-400 ring-2 ring-red-100' : ($order->status == 'pending' ? 'border-yellow-300' : 'border-blue-200') }} overflow-hidden flex flex-col relative">
        
        @if($order->is_urgent)
        <div class="absolute -right-8 top-4 bg-red-500 text-white font-bold text-xs py-1 px-10 transform rotate-45 shadow-sm z-10">
            URGENT 🔥
        </div>
        @endif

        <!-- Header: Status & Waktu -->
        <div class="p-4 flex justify-between items-start {{ $order->is_urgent ? 'bg-red-50' : ($order->status == 'pending' ? 'bg-yellow-50' : 'bg-blue-50') }} border-b border-gray-100">
            <div class="pr-6">
                <span class="text-xs font-bold uppercase tracking-wider {{ $order->is_urgent ? 'text-red-600' : ($order->status == 'pending' ? 'text-yellow-600' : 'text-blue-600') }}">
                    @if($order->is_urgent)
                        🔥 PRIORITAS UTAMA
                    @elseif($order->status == 'pending')
                        Belum Dikerjakan
                    @else
                        Sedang Dirangkai
                    @endif
                </span>
                <h4 class="font-bold text-gray-800 text-lg leading-tight mt-1">{{ $order->customer_name }}</h4>
                @if($order->product_name)
                    <p class="text-sm text-florist-600 font-bold mt-1"><i class="fa-solid fa-gift mr-1"></i> {{ $order->product_name }}</p>
                @elseif($order->items && $order->items->count() > 0)
                    <p class="text-sm text-florist-600 font-bold mt-1"><i class="fa-solid fa-gift mr-1"></i> {{ $order->items->pluck('product_name')->join(', ') }}</p>
                @endif
            </div>
            <div class="text-right shrink-0">
                <span class="text-xs text-gray-500 block mb-1">Deadline Kirim</span>
                <span class="px-2 py-1 bg-white rounded text-xs font-bold {{ $order->is_urgent ? 'text-red-600 border-red-200' : 'text-gray-700 border-gray-200' }} border shadow-sm">
                    {{ $order->scheduled_at ? $order->scheduled_at->format('H:i') : 'Flexible' }}
                </span>
            </div>
        </div>
        
        <!-- Image Reference (Jika Ada) -->
        @if($order->reference_image)
        <div class="aspect-video bg-gray-100 relative group cursor-pointer" onclick="window.open('{{ asset('storage/'.$order->reference_image) }}', '_blank')">
            <img src="{{ asset('storage/'.$order->reference_image) }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                <span class="text-white font-bold"><i class="fa-solid fa-expand mr-2"></i>Perbesar</span>
            </div>
            <div class="absolute top-2 left-2 bg-black/60 backdrop-blur px-2 py-1 rounded text-xs text-white font-medium">
                Referensi Online
            </div>
        </div>
        @endif

        <div class="p-4 flex-1 flex flex-col">
            <div class="mb-3 flex justify-between items-end">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Budget Rakitan</span>
                    <p class="font-bold text-florist-600 text-lg">Rp {{ number_format($order->budget > 0 ? $order->budget : $order->total_amount, 0, ',', '.') }}</p>
                </div>
                @if($order->estimated_time)
                <div class="text-right">
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Target Waktu</span>
                    <span class="text-sm font-bold text-gray-700"><i class="fa-solid fa-clock text-gray-400 mr-1"></i> {{ $order->estimated_time }} Menit</span>
                </div>
                @endif
            </div>
            
            <div class="mb-4 bg-gray-50 p-3 rounded-lg border-2 border-gray-200 shadow-md">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Catatan Produk / Pelanggan</span>
                <p class="text-sm text-gray-700 font-medium whitespace-pre-line">{{ $order->notes ?? '-' }}</p>
            </div>

            @if($order->greeting_card)
            <div class="mb-4 bg-pink-50 p-3 rounded-lg border border-pink-100">
                <span class="block text-xs font-bold text-pink-400 uppercase mb-1"><i class="fa-solid fa-envelope mr-1"></i> Kartu Ucapan</span>
                <p class="text-sm text-pink-800 font-medium whitespace-pre-line">{{ $order->greeting_card }}</p>
            </div>
            @endif
            
            <!-- Jika Offline, tampilkan snapshot -->
            @if($order->source == 'offline')
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100 mb-4">
                <span class="block text-xs font-bold text-blue-400 uppercase mb-2"><i class="fa-solid fa-list-check mr-1"></i> Snapshot Resep Kasir</span>
                <ul class="space-y-1 text-sm text-blue-800 font-medium">
                    @foreach($order->items as $item)
                        @foreach($item->components as $comp)
                        <li>- {{ $comp->qty }}x {{ $comp->material_name }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Florist Notes Form -->
            <div class="mt-auto pt-4 border-t border-gray-100">
                <form action="{{ route('orders.updateFloristNotes', $order->id) }}" method="POST" class="flex gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="florist_notes" value="{{ $order->florist_notes }}" placeholder="Ketik catatan florist di sini..." class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-bold transition-colors">Simpan</button>
                </form>
            </div>
        </div>

        <div class="p-4 border-t border-gray-100 {{ $order->status == 'processing' ? 'bg-blue-50' : 'bg-gray-50' }}">
            
            @if($order->started_at)
            <div class="mb-3 text-center text-xs font-medium text-blue-600">
                <i class="fa-solid fa-stopwatch animate-pulse mr-1"></i> Mulai dikerjakan pukul {{ $order->started_at->format('H:i') }}
            </div>
            @endif

            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                @csrf @method('PUT')
                @if($order->status == 'pending')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="w-full py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold shadow-sm transition-transform active:scale-95">
                        <i class="fa-solid fa-play mr-1"></i> Mulai Kerjakan
                    </button>
                @elseif($order->status == 'processing')
                    <input type="hidden" name="status" value="ready">
                    <button type="submit" class="w-full py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold shadow-sm transition-transform active:scale-95">
                        <i class="fa-solid fa-check-double mr-1"></i> Selesai Dirangkai
                    </button>
                @endif
            </form>
            <div class="mt-3 text-center">
                <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-bold text-gray-400 hover:text-florist-500 uppercase tracking-wider">Lihat Detail Lengkap</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center bg-white rounded-2xl border-2 border-gray-200 shadow-md">
        <i class="fa-solid fa-mug-hot text-5xl text-gray-300 mb-4"></i>
        <h4 class="text-xl font-bold text-gray-500">Dapur Sedang Kosong</h4>
        <p class="text-gray-400 text-sm mt-1">Belum ada pesanan yang perlu dirangkai saat ini. Waktunya istirahat!</p>
    </div>
    @endforelse
</div>
</div>

@endsection
