@extends('layouts.app')

@section('title', 'Detail Pesanan')
@section('page_title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Detail Pesanan</h3>
        <p class="text-gray-500 text-sm mt-1">Dibuat pada {{ $order->created_at->format('d/m/Y H:i') }} oleh {{ $order->user->name ?? 'System' }}</p>
    </div>
    <div class="flex gap-2">
@if(in_array($order->payment_status, ['paid', 'paid_qris', 'paid_tf', 'dp']))
<a href="{{ route('orders.print', $order->id) }}" target="_blank" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
    <i class="fa-solid fa-print mr-2"></i> Cetak Struk
</a>
@else
<div class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-400 rounded-lg cursor-not-allowed shadow-sm" title="Nota hanya bisa dicetak jika pesanan sudah LUNAS">
    <i class="fa-solid fa-lock mr-2"></i> Cetak Dikunci
</div>
@endif
        <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
            Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center shadow-sm">
    <i class="fa-solid fa-check-circle text-xl mr-3"></i>
    <span class="font-medium">{{ session('success') }}</span>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Status Banner -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border {{ $order->is_urgent ? 'border-red-200 ring-2 ring-red-100' : 'border-gray-100' }} flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase block mb-1 {{ $order->is_urgent ? 'text-red-500' : 'text-gray-400' }}">
                    Status Pesanan {!! $order->is_urgent ? '<span class="text-red-500 ml-1">🔥 PRIORITAS URGENT</span>' : '' !!}
                </span>
                @if($order->status == 'pending')
                    <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full font-bold border border-yellow-200">Pending / Belum Dirangkai</span>
                @elseif($order->status == 'processing')
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-bold border border-blue-200">Sedang Dirangkai</span>
                @elseif($order->status == 'ready')
                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full font-bold border border-purple-200">Siap Diambil/Kirim</span>
                @elseif($order->status == 'completed')
                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full font-bold border border-green-200">Selesai</span>
                @endif
            </div>
            <div class="flex flex-col gap-2 items-end">
                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Pembayaran</span>
               @if(in_array($order->payment_status, ['paid', 'paid_qris', 'paid_tf']))
    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full font-bold">
        <i class="fa-solid fa-check-double mr-1"></i>
        @if($order->payment_status == 'paid_qris')
            LUNAS QRIS
        @elseif($order->payment_status == 'paid_tf')
            LUNAS TF
        @else
            LUNAS
        @endif
    </span>
@elseif($order->payment_status == 'dp')
    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-bold">DP</span>
@else
    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full font-bold">BELUM BAYAR</span>
@endif
            </div>
        </div>

        <!-- Receipt / Snapshot Items -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden">
            <div class="p-5 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-gray-800"><i class="fa-solid fa-bag-shopping mr-2 text-florist-400"></i> Rincian Pesanan</h4>
                    <span class="text-xs bg-white px-2 py-1 rounded text-gray-500 font-bold border border-gray-200 mt-1 inline-block"># {{ $order->order_number }}</span>
                </div>
                @if($order->status == 'pending')
                <a href="{{ route('orders.revision.edit', $order->id) }}" class="px-4 py-2 bg-purple-100 text-purple-700 hover:bg-purple-200 rounded-lg text-sm font-bold transition-colors">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Revisi Komponen
                </a>
                @endif
            </div>
            
            <div class="p-5">
                @if($order->source == 'online')
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-4">
                            @if($order->reference_image)
                            <div class="w-32 h-32 rounded-lg overflow-hidden shrink-0 bg-white border border-blue-200">
                                <a href="{{ asset('storage/'.$order->reference_image) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$order->reference_image) }}" class="w-full h-full object-cover">
                                </a>
                            </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold inline-block">PESANAN ONLINE {{ $order->external_id ? '('.$order->external_id.')' : '' }}</span>
                                </div>
                                <h5 class="font-bold text-gray-800 text-lg mb-1">{{ $order->product_name ?? 'Produk Custom' }}</h5>
                                <p class="text-sm text-gray-600 whitespace-pre-line mb-3">{{ $order->notes ?? 'Tidak ada rincian tambahan.' }}</p>
                                
                                @if($order->greeting_card)
                                <div class="bg-pink-50 p-3 rounded-lg border border-pink-100 mb-3">
                                    <span class="block text-xs font-bold text-pink-400 uppercase mb-1"><i class="fa-solid fa-envelope mr-1"></i> Kartu Ucapan</span>
                                    <p class="text-sm text-pink-800 font-medium whitespace-pre-line">{{ $order->greeting_card }}</p>
                                </div>
                                @endif

                                @if($order->payment_proof)
                                <div>
                                    <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" class="inline-flex items-center text-sm font-bold text-green-600 hover:text-green-700 bg-green-50 px-3 py-1.5 rounded-lg border border-green-200 transition-colors">
                                        <i class="fa-solid fa-receipt mr-2"></i> Lihat Bukti Pembayaran
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @foreach($order->items as $item)
                <div class="mb-6 last:mb-0">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h5 class="font-bold text-lg text-gray-800">{{ $item->product_name }}</h5>
                            <span class="text-sm text-gray-500">{{ $item->qty }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        <span class="font-bold text-lg text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 border-2 border-gray-200 shadow-md">
                        <h6 class="text-xs font-bold text-gray-400 uppercase mb-3"><i class="fa-solid fa-lock text-gray-300 mr-1"></i> Data Snapshot Komponen</h6>
                        <ul class="space-y-2">
                            @foreach($item->components as $comp)
                            <li class="flex justify-between text-sm">
                                <div class="text-gray-600">
                                    <span class="font-medium">{{ $comp->material_name }}</span> 
                                    <span class="text-gray-400 text-xs ml-1">({{ $comp->qty }}x)</span>
                                </div>
                                <span class="text-gray-500">Rp {{ number_format($comp->subtotal, 0, ',', '.') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="p-5 bg-gray-50 border-t border-gray-100 flex flex-col gap-2">
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-bold text-sm">Subtotal Produk</span>
                    <span class="font-bold">Rp {{ number_format($order->total_amount - $order->delivery_fee, 0, ',', '.') }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-bold text-sm">Ongkos Kirim (Jarak: {{ $order->delivery_distance }} KM)</span>
                    <span class="font-bold">+ Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-bold text-sm">Diskon</span>
                    <span class="font-bold text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <hr class="border-gray-200 my-1">
                <div class="flex justify-between items-center text-gray-700">
                    <span class="font-bold uppercase tracking-wider text-sm">Total Belanja</span>
                    <span class="text-xl font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-gray-500">
                    <span class="font-bold uppercase tracking-wider text-sm">Total Dibayar</span>
                    <span class="text-xl font-bold text-green-600">- Rp {{ number_format($order->total_dibayar, 0, ',', '.') }}</span>
                </div>
                <hr class="border-gray-200 my-1">
                <div class="flex justify-between items-center">
                    <span class="text-gray-800 font-bold uppercase tracking-wider">Sisa Tagihan</span>
                    <span class="text-3xl font-bold {{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-florist-600' }}">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Formulir Input Pembayaran -->
        @if($order->sisa_tagihan > 0)
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6 mt-6">
            <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2"><i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i> Input Pembayaran (Bayar / DP)</h4>
            
            <form action="{{ route('payments.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1" max="{{ $order->sisa_tagihan }}" value="{{ $order->sisa_tagihan }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                            <option value="Tunai / Cash">Tunai / Cash</option>
                            <option value="Transfer BCA">Transfer BCA</option>
                            <option value="Transfer Mandiri">Transfer Mandiri</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran <span id="proof_req_mark" class="text-red-500 hidden">*</span> <span id="proof_opt_mark" class="text-gray-400 text-xs">(Wajib untuk Transfer/QRIS)</span></label>
                    <input type="file" name="proof_image" id="proof_image" accept="image/*" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-florist-50 file:text-florist-700 hover:file:bg-florist-100">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" placeholder="Contoh: Titip ke satpam" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                </div>

                <button type="submit" class="w-full py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition-colors">
                    Catat Pembayaran
                </button>
            </form>
        </div>
        @endif

        <!-- Dokumentasi Hasil Akhir -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 p-6 mt-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                <h4 class="font-bold text-gray-800"><i class="fa-solid fa-camera text-blue-500 mr-2"></i> Dokumentasi Hasil Bucket</h4>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('orders.images.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="mb-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Hasil (Max 2MB)</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Foto (Opsional)</label>
                        <input type="text" name="notes" placeholder="Misal: Tampak depan..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-florist-400 outline-none">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg shadow-sm transition-colors">
                            <i class="fa-solid fa-upload mr-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>

            <!-- Gallery Grid -->
            @if($order->images && $order->images->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($order->images as $img)
                <div class="group relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50 aspect-square">
                    <a href="{{ asset('storage/'.$img->image_path) }}" target="_blank">
                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </a>
                    
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
                        <div class="flex justify-end">
                            <form action="{{ route('orders.images.destroy', $img->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110" onclick="return confirm('Hapus foto ini?')" title="Hapus Foto">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                        <div class="text-white text-xs">
                            <p class="font-bold truncate">{{ $img->notes ?? 'Tanpa Catatan' }}</p>
                            <p class="text-white/80"><i class="fa-solid fa-user-pen mr-1"></i> {{ $img->uploader->name ?? 'System' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <i class="fa-regular fa-image text-4xl text-gray-400 mb-2 block"></i>
                <p class="text-gray-500 text-sm">Belum ada foto dokumentasi untuk pesanan ini.</p>
            </div>
            @endif
        </div>

        <!-- Riwayat Pembayaran -->
        @if($order->payments->count() > 0)
        <div class="bg-white rounded-2xl shadow-md border-2 border-gray-200 overflow-hidden mt-6">
            <div class="p-5 bg-gray-50 border-b border-gray-100">
                <h4 class="font-bold text-gray-800"><i class="fa-solid fa-receipt text-blue-500 mr-2"></i> Riwayat Pembayaran</h4>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->payments()->orderBy('created_at', 'desc')->get() as $payment)
                <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $payment->status == 'pending' ? 'bg-yellow-50/30' : 'bg-white' }}">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-gray-800 text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            @if($payment->status == 'verified')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-bold"><i class="fa-solid fa-check"></i> Sah</span>
                            @else
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-bold"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            Metode: <strong>{{ $payment->payment_method }}</strong> | Tanggal: {{ $payment->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($payment->notes)
                        <div class="text-sm text-gray-500 mt-1">Catatan: {{ $payment->notes }}</div>
                        @endif
                        @if($payment->status == 'verified')
                        <div class="text-xs text-green-600 mt-1"><i class="fa-solid fa-user-check mr-1"></i> Diverifikasi oleh: {{ $payment->verifier->name ?? 'System' }}</div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if($payment->proof_image)
                        <a href="{{ asset('storage/'.$payment->proof_image) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded text-sm font-medium transition-colors border border-blue-200">
                            <i class="fa-solid fa-image mr-1"></i> Lihat Bukti
                        </a>
                        @else
                        <form action="{{ route('payments.upload_proof', $payment->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-1">
                            @csrf @method('PUT')
                            <input type="file" name="proof_image" accept="image/*" required class="text-xs w-32 border border-gray-200 rounded file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-florist-50 file:text-florist-700 bg-white" title="Pilih foto bukti/struk susulan">
                            <button type="submit" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded shadow-sm text-xs transition-colors" title="Upload susulan">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                        </form>
                        @endif

                        @if($payment->status == 'pending')
                        <form action="{{ route('payments.verify', $payment->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="px-4 py-1.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded shadow-sm transition-colors" onclick="return confirm('Verifikasi pembayaran ini secara sah?')">
                                Verifikasi (Sah)
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <!-- Sidebar Info -->
    <!-- Sidebar Info -->
    <div class="space-y-6">
@php
    $verifiedPayment = $order->payments()
        ->where('status', 'verified')
        ->latest()
        ->first();

    $paymentStatusText = 'BELUM BAYAR';

    if ($order->payment_status == 'paid_qris') {

        $paymentStatusText = 'LUNAS QRIS';

    } elseif ($order->payment_status == 'paid_tf') {

        $paymentStatusText = 'LUNAS TF';

    } elseif ($order->payment_status == 'paid') {

        $paymentStatusText = 'LUNAS';

    } elseif ($order->payment_status == 'dp') {

        $paymentStatusText = 'DP';
    }

    $lineSummary = "PESANAN MARKETING\n\n";
    $lineSummary .= "Nama pemesan : " . ($order->customer_name ?? '-') . "\n";
    $lineSummary .= "No. HP pemesan : " . ($order->customer_phone ?? '-') . "\n";

    if (!empty($order->recipient_name)) {
        $lineSummary .= "Nama penerima : " . $order->recipient_name . "\n";
    }

    if (!empty($order->recipient_phone)) {
        $lineSummary .= "No. HP penerima : " . $order->recipient_phone . "\n";
    }

    $lineSummary .= "hari/tanggal : ";

    if ($order->scheduled_at) {
        $lineSummary .= \Carbon\Carbon::parse($order->scheduled_at)
            ->locale('id')
            ->translatedFormat('l, d F Y');
    } else {
        $lineSummary .= "-";
    }

    $lineSummary .= "\n";

    $lineSummary .= "Diambil/Diantar (beserta waktu): ";

    if ($order->delivery_method == 'pickup') {
        $lineSummary .= "diambil ";
    } else {
        $lineSummary .= "diantar ";
    }

    $lineSummary .= $order->scheduled_at
        ? \Carbon\Carbon::parse($order->scheduled_at)->format('H.i')
        : '-';

    $lineSummary .= "\n";
    $lineSummary .= "No. HP : " . ($order->customer_phone ?? '-') . "\n";

    if ($order->delivery_method == 'delivery') {
        $lineSummary .= "Alamat Pengiriman :\n";
        $lineSummary .= ($order->delivery_address ?? '-') . "\n";
    } else {
        $lineSummary .= "Metode : Ambil di Toko\n";
    }

    $lineSummary .= "\n\n";
    $lineSummary .= "Rincian :\n";

    foreach ($order->items as $item) {

        if ($item->components && $item->components->count()) {

            foreach ($item->components as $comp) {

                $lineSummary .= $comp->qty . ' ' . $comp->material_name;

                if (!empty($comp->color)) {
                    $lineSummary .= ' (' . $comp->color . ')';
                }

                $lineSummary .= "\n";
            }

        } else {

            $lineSummary .= $item->qty . ' ' . $item->product_name . "\n";
        }
    }

    $lineSummary .= "\n";
    $lineSummary .= "Total : Rp. "
        . number_format($order->total_amount, 0, ',', '.')
        . " (" . $paymentStatusText . ")";
@endphp

<div class="bg-white p-6 rounded-2xl shadow-md border-2 border-green-200">
    <h4 class="font-bold text-green-700 mb-4 border-b pb-2">
        <i class="fa-brands fa-line mr-2"></i>
        Ringkasan LINE Marketing
    </h4>

    <textarea
        id="lineSummaryText"
        readonly
        class="w-full h-72 p-3 border border-green-200 rounded-xl text-sm font-mono bg-green-50 resize-none">{{ $lineSummary }}</textarea>

    <button
        type="button"
        onclick="copyLineSummary()"
        class="mt-3 w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl font-bold">
        <i class="fa-solid fa-copy mr-2"></i>
        Copy Ringkasan
    </button>
</div>
        @if(auth()->check() && auth()->user()->role !== 'florist' && !empty($order->customer_phone) && isset($waLinks))
        <!-- Action WA Notification -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-sm text-white">
            <h4 class="font-bold mb-4 border-b border-green-400 pb-2"><i class="fa-brands fa-whatsapp mr-2 text-xl"></i> Kirim Notifikasi WA</h4>
            
            <p class="text-xs text-green-100 mb-4">Pilih template pesan otomatis untuk pelanggan <strong class="text-white">{{ $order->customer_name }}</strong>:</p>
            
            <div class="space-y-2">
                <a href="{{ $waLinks['received'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    1. Terima Pesanan & Tagihan
                </a>
                
                @if($order->payment_status != 'unpaid')
                <a href="{{ $waLinks['payment_verified'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    2. Pembayaran Diverifikasi
                </a>
                @endif
                
                <a href="{{ $waLinks['processing'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    3. Bunga Sedang Dirangkai
                </a>
                
                @if($order->delivery_method == 'pickup')
                <a href="{{ $waLinks['ready'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    4. Pesanan Siap Diambil
                </a>
                @else
                <a href="{{ $waLinks['delivery'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    4. Pesanan Sedang Dikirim
                </a>
                @endif
                
                <a href="{{ $waLinks['completed'] }}" target="_blank" class="block w-full text-left px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors border border-green-400/30">
                    5. Pesanan Selesai (Thank You)
                </a>
            </div>
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Informasi Pelanggan</h4>
            <div class="mb-4">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama</span>
                <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">WhatsApp</span>
                <p class="font-medium text-gray-800">{{ $order->customer_phone ?? '-' }}</p>
                @if($order->customer_phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold hover:bg-green-200">
                    <i class="fa-brands fa-whatsapp mr-1"></i> WA Manual
                </a>
                @endif
                @if($order->recipient_name)
<hr class="my-4">

<div class="mb-3">
    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">
        Nama Penerima
    </span>
    <p class="font-medium text-gray-800">
        {{ $order->recipient_name }}
    </p>
</div>
@endif

@if($order->recipient_phone)
<div>
    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">
        WhatsApp Penerima
    </span>
    <p class="font-medium text-gray-800">
        {{ $order->recipient_phone }}
    </p>
</div>
@endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Pengiriman</h4>
            <div class="mb-4">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Metode</span>
                <p class="font-medium text-gray-800">
                    @if($order->delivery_method == 'pickup')
                        <i class="fa-solid fa-store text-gray-400 mr-2"></i> Ambil di Toko
                    @else
                        <i class="fa-solid fa-motorcycle text-gray-400 mr-2"></i> Diantar Kurir
                    @endif
                </p>
            </div>
            <div class="mb-4">
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Waktu</span>
                <p class="font-medium text-gray-800">{{ $order->scheduled_at ? $order->scheduled_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            @if($order->delivery_address)
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat / Detail</span>
                <p class="text-sm text-gray-600">{{ $order->delivery_address }}</p>
            </div>
            @endif
        </div>
        
        @if($order->notes)
        <div class="bg-yellow-50 p-6 rounded-2xl shadow-sm border border-yellow-100">
            <h4 class="font-bold text-yellow-800 mb-2 border-b border-yellow-200 pb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Catatan Khusus</h4>
            <p class="text-sm text-yellow-700">{{ $order->notes }}</p>
        </div>
        @endif

        @if($order->florist_notes)
        <div class="bg-purple-50 p-6 rounded-2xl shadow-sm border border-purple-100">
            <h4 class="font-bold text-purple-800 mb-2 border-b border-purple-200 pb-2"><i class="fa-solid fa-clipboard-check mr-2"></i> Catatan Florist (Internal)</h4>
            <p class="text-sm text-purple-700">{{ $order->florist_notes }}</p>
        </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Timeline Pengerjaan</h4>
            
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-green-500 shrink-0"></div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Pesanan Dibuat</span>
                        <span class="text-sm text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                @if($order->started_at)
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-blue-500 shrink-0"></div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Mulai Dirangkai</span>
                        <span class="text-sm text-gray-800">{{ $order->started_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @endif
                
                @if($order->completed_at)
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-purple-500 shrink-0"></div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Selesai Dirangkai</span>
                        <span class="text-sm text-gray-800">{{ $order->completed_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @if($order->started_at)
                <div class="mt-2 text-xs font-medium text-gray-500 bg-white p-2 rounded border border-gray-200 text-center">
                    Durasi Aktual: {{ $order->completed_at->diffInMinutes($order->started_at) }} Menit
                    @if($order->estimated_time)
                        <br>Target: {{ $order->estimated_time }} Menit
                    @endif
                </div>
                @endif
                @endif
            </div>
        </div>

        @if($order->histories->count() > 0)
        <div class="bg-white p-6 rounded-2xl shadow-md border-2 border-gray-200">
            <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2"><i class="fa-solid fa-shoe-prints mr-2 text-gray-400"></i> Jejak Langkah (Histori)</h4>
            
            <div class="space-y-4 relative before:absolute before:inset-0 before:ml-1 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                @foreach($order->histories()->orderBy('created_at', 'desc')->get() as $history)
                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div class="flex items-center justify-center w-3 h-3 rounded-full border border-white bg-gray-300 group-[.is-active]:bg-florist-500 text-gray-500 group-[.is-active]:text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                    <div class="w-[calc(100%-2rem)] md:w-[calc(50%-2.5rem)] bg-white p-3 rounded border-2 border-gray-200 shadow-md">
                        <div class="flex items-center justify-between space-x-2 mb-1">
                            <div class="font-bold text-gray-800 text-xs">{{ $history->user->name ?? 'System' }}</div>
                            <time class="text-[10px] font-medium text-gray-400">{{ $history->created_at->format('d/m/y H:i') }}</time>
                        </div>
                        <div class="text-xs text-gray-600">{{ $history->notes }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
        const proofImageInput = document.getElementById('proof_image');
        const proofReqMark = document.getElementById('proof_req_mark');
        const proofOptMark = document.getElementById('proof_opt_mark');

        if (paymentMethodSelect && proofImageInput) {
            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'Tunai / Cash') {
                    proofImageInput.required = false;
                    if (proofReqMark) proofReqMark.classList.add('hidden');
                    if (proofOptMark) proofOptMark.textContent = '(Opsional)';
                } else {
                    proofImageInput.required = true;
                    if (proofReqMark) proofReqMark.classList.remove('hidden');
                    if (proofOptMark) proofOptMark.textContent = '(Wajib)';
                }
            });

            paymentMethodSelect.dispatchEvent(new Event('change'));
        }
    });

    function triggerFloristNotification(orderId) {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;

            if (AudioContext) {
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

                playNote(1046.50, ctx.currentTime, 1.0, 'sine');
                playNote(1318.51, ctx.currentTime + 0.1, 1.2, 'sine');
                playNote(1567.98, ctx.currentTime + 0.2, 1.8, 'sine');
            }

            const audio = new Audio("/pesanan_masuk.mp3");
            audio.play().catch(() => {});
        } catch (e) {}
    }

    function copyLineSummary() {
        const textarea = document.getElementById('lineSummaryText');

        if (!textarea) {
            return;
        }

        textarea.removeAttribute('readonly');
        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, textarea.value.length);

        let copied = false;

        try {
            copied = document.execCommand('copy');
        } catch (e) {
            copied = false;
        }

        textarea.setAttribute('readonly', true);

        if (copied) {
            showCopyNotif('Ringkasan berhasil disalin');
        } else {
            showCopyNotif('Gagal menyalin ringkasan');
        }
    }

    function showCopyNotif(message) {
        let notif = document.getElementById('copyNotif');

        if (!notif) {
            notif = document.createElement('div');
            notif.id = 'copyNotif';
            notif.style.position = 'fixed';
            notif.style.top = '20px';
            notif.style.right = '20px';
            notif.style.zIndex = '9999';
            notif.style.background = '#22c55e';
            notif.style.color = '#ffffff';
            notif.style.padding = '12px 18px';
            notif.style.borderRadius = '12px';
            notif.style.fontWeight = '700';
            notif.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
            notif.style.transition = 'all 0.3s ease';

            document.body.appendChild(notif);
        }

        notif.textContent = message;
        notif.style.opacity = '1';
        notif.style.transform = 'translateY(0)';

        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transform = 'translateY(-10px)';
        }, 1500);
    }
</script>
@endsection