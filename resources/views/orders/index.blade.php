@extends('layouts.app')

@section('title', 'Daftar Pesanan')
@section('page_title', 'Daftar Pesanan')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h3>
            <p class="text-gray-500 text-sm mt-1">Kelola semua transaksi pesanan pelanggan.</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-gray-200">
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kode Pesanan</label>
                <select name="prefix" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200 min-w-[120px]">
                    <option value="">Semua Kode</option>
                    <option value="PES" {{ request('prefix') == 'PES' ? 'selected' : '' }}>PES</option>
                    <option value="PJL" {{ request('prefix') == 'PJL' ? 'selected' : '' }}>PJL</option>
                    <option value="PESM" {{ request('prefix') == 'PESM' ? 'selected' : '' }}>PESM</option>
                    <option value="PESW" {{ request('prefix') == 'PESW' ? 'selected' : '' }}>PESW</option>
                    <option value="KSK" {{ request('prefix') == 'KSK' ? 'selected' : '' }}>KSK</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pesanan</label>
                <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status Pengerjaan</label>
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200 min-w-[120px]">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Siap Ambil/Kirim</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-florist-200 min-w-[120px]">
                    <option value="">Semua Pembayaran</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="dp" {{ request('payment_status') == 'dp' ? 'selected' : '' }}>DP</option>
                    <option value="paid_qris" {{ request('payment_status') == 'paid_qris' ? 'selected' : '' }}>Lunas QRIS</option>
                    <option value="paid_tf" {{ request('payment_status') == 'paid_tf' ? 'selected' : '' }}>Lunas TF</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas Lama</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-florist-500 text-white font-bold rounded-lg hover:bg-florist-600 transition-colors text-sm shadow-sm">
                    Terapkan Filter
                </button>

                @if(request('q') || request('date') || request('status') || request('payment_status') || request('prefix'))
                    <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Reset
                    </a>
                @endif
            </div>

            @if(in_array(auth()->user()->role, ['admin','asmen','it support']))
            <button type="submit" formaction="{{ route('admin.orders.export.excel') }}"
                class="px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 transition-colors text-sm shadow-sm">
                <i class="fa-solid fa-file-excel mr-1"></i>
                Export Excel
            </button>
            @endif
        </form>
    </div>
</div>

<div class="card-modern overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                <tr>
                    <th class="py-4 px-4 font-medium">Order ID</th>
                    <th class="py-4 px-4 font-medium">Pelanggan</th>
                    <th class="py-4 px-4 font-medium">Waktu Pesan</th>
                    <th class="py-4 px-4 font-medium">Total</th>
                    <th class="py-4 px-4 font-medium">Pembayaran</th>
                    <th class="py-4 px-4 font-medium">Status Pesanan</th>
                    <th class="py-4 px-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-600 divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('orders.show', $order->id) }}'">
                    <td class="py-4 px-4 font-bold text-florist-600 hover:underline">
                        <a href="{{ route('orders.show', $order->id) }}" onclick="event.stopPropagation();">
                            {{ $order->order_number }}
                        </a>
                    </td>

                    <td class="py-4 px-4">
                        <div class="font-bold text-gray-800">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer_phone ?? '-' }}</div>
                    </td>

                    <td class="py-4 px-4 text-xs">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="py-4 px-4 font-bold text-florist-600">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>

                    <td class="py-4 px-4">
                        @if($order->payment_status == 'paid_qris')
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold">
                                LUNAS QRIS
                            </span>
                        @elseif($order->payment_status == 'paid_tf')
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold">
                                LUNAS TF
                            </span>
                        @elseif($order->payment_status == 'paid')
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs font-bold">
                                LUNAS CASH
                            </span>
                        @elseif($order->payment_status == 'dp')
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-bold">
                                DP
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-bold">
                                BELUM BAYAR
                            </span>
                        @endif
                    </td>

                    <td class="py-4 px-4">
                        @if($order->status == 'pending')
                            <span class="px-2 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold border border-yellow-100">Pending</span>
                        @elseif($order->status == 'processing')
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold border border-blue-100">Proses</span>
                        @elseif($order->status == 'ready')
                            <span class="px-2 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-bold border border-purple-100">Siap Ambil</span>
                        @elseif($order->status == 'completed')
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold border border-green-100">Selesai</span>
                        @elseif($order->status == 'cancelled')
                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold border border-red-100">Dibatalkan</span>
                        @endif
                    </td>

                <td class="py-4 px-4 text-right">
                    <div class="flex justify-end gap-2" onclick="event.stopPropagation();">
                        <a href="{{ route('orders.show', $order->id) }}"
                            class="inline-block px-3 py-1.5 bg-white border border-gray-200 text-florist-500 hover:bg-florist-50 rounded-lg shadow-sm font-medium text-xs transition-colors">
                            Lihat Detail
                        </a>

                        @if($order->status != 'completed' && $order->status != 'cancelled')
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">

                                <button type="submit"
                                    onclick="return confirm('Ubah status pesanan ini menjadi selesai?')"
                                    class="inline-block px-3 py-1.5 bg-green-500 text-white hover:bg-green-600 rounded-lg shadow-sm font-medium text-xs transition-colors">
                                    Selesai
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-search text-4xl mb-3 text-gray-300"></i>

                        @if(request('q') || request('date') || request('status') || request('payment_status') || request('prefix'))
                            <p>Pencarian tidak menemukan hasil apapun.</p>
                            <a href="{{ route('orders.index') }}" class="text-florist-500 hover:underline mt-2 inline-block text-sm">
                                Hapus Filter
                            </a>
                        @else
                            <p>Belum ada transaksi pesanan.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100 bg-gray-50">
        {{ $orders->links() }}
    </div>
</div>
@endsection