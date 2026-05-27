@extends('layouts.app')

@section('title', 'Revisi Komponen Pesanan')
@section('page_title', 'Revisi Pesanan #' . $order->order_number)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Revisi Komponen (Bahan Baku)</h3>
        <p class="text-gray-500 text-sm mt-1">Ubah, tambah, atau hapus bunga dan aksesoris pada pesanan ini.</p>
    </div>
    <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
        Kembali ke Pesanan
    </a>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 font-medium">
    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Kolom Kiri: Form Tambah Komponen -->
    <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200 h-fit">
        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-plus-circle mr-2 text-florist-400"></i> Tambah Material Baru</h4>
        
        <form action="{{ route('orders.revision.storeComponent', $order->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Material</label>
                <select name="material_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    <option value="">-- Pilih Bunga / Kertas / Aksesoris --</option>
                    @foreach($materials as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->name }} (Stok: {{ $m->stock }}) - Rp {{ number_format($m->price, 0, ',', '.') }}/{{ $m->unit }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" name="qty" required min="1" value="1" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Tambah mawar karena kurang" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
            </div>

            <button type="submit" class="w-full py-2 bg-florist-500 hover:bg-florist-600 text-white rounded-lg font-bold shadow-sm transition-transform active:scale-95" onclick="return confirm('Tambahkan komponen ini? Stok akan otomatis terpotong.')">
                Tambahkan Komponen
            </button>
        </form>

        <div class="mt-6 p-4 bg-yellow-50 rounded-xl border border-yellow-200 text-sm text-yellow-800">
            <strong>Penting:</strong> Menambah komponen di sini akan otomatis mengurangi stok gudang dan menambah total tagihan pesanan!
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Komponen Aktif -->
    <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h4 class="font-bold text-gray-800"><i class="fa-solid fa-list mr-2 text-florist-400"></i> Komponen Saat Ini</h4>
            <span class="text-sm font-bold text-florist-600">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        
        @foreach($order->items as $item)
        <div class="mb-6 last:mb-0">
            <h5 class="font-bold text-gray-700 mb-3 bg-gray-50 p-2 rounded">{{ $item->product_name }}</h5>
            
            <ul class="space-y-3">
                @foreach($item->components as $comp)
                <li class="flex items-center justify-between p-3 border-2 border-gray-200 shadow-md rounded-lg hover:border-red-200 hover:bg-red-50 transition-colors group">
                    <div>
                        <span class="font-medium text-gray-800 block">{{ $comp->material_name }}</span>
                        <span class="text-xs text-gray-500">{{ $comp->qty }}x @ Rp {{ number_format($comp->unit_price, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-gray-700">Rp {{ number_format($comp->subtotal, 0, ',', '.') }}</span>
                        <form action="{{ route('orders.revision.deleteComponent', [$order->id, $comp->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-300 hover:text-red-600 transition-colors" title="Hapus Komponen" onclick="return confirm('Hapus komponen ini? Stok material akan dikembalikan ke gudang.')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>
@endsection
