<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 57mm auto; /* Cocok untuk 57x30mm thermal paper */
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px; /* Diperkecil agar pas di lebar 57mm */
            color: #000;
            margin: 0 auto;
            padding: 5px;
            width: 100%;
            max-width: 57mm; /* Lebar printer thermal 57mm */
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .border-dashed {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 1px 0;
            vertical-align: top;
        }
        .w-full { width: 100%; }
        
        @media print {
            body {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .page-break {
            page-break-after: always;
            break-after: page;
            height: 0;
            margin: 0;
            border: none;
        }
        
        .btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            margin-top: 10px;
            box-sizing: border-box;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background-color: #ec4899; color: white; }
        .btn-secondary { background-color: #4b5563; color: white; }
    </style>
</head>
<body onload="window.print()">

    @for($copy = 1; $copy <= 2; $copy++)
    <div class="receipt-container">
        <div class="text-center mb-2">
            <div style="font-size: 15px; font-weight: bold;">Poppy Florist</div>
            Jl. Anang Adenansi No.2 (Kamboja, Antasan Besar, Kec. Banjarmasin Tengah, Kota Banjarmasin, Kalimantan Selatan 70231<br>
            Web: www.poppyflorist.com<br>
            WA: 081345070618<br>
            IG: @poppy_florist<br>
            Telp: 081345070618
        </div>

        <div class="border-dashed"></div>

        <table class="mb-2">
            <tr>
                <td style="width: 70px;">ID</td>
                <td>: {{ $order->order_number }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $order->created_at->format('d/m/Y, H.i.s') }}</td>
            </tr>
            <tr>
                <td>Pembeli</td>
                <td>: {{ $order->customer_name }}</td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td>: {{ $order->customer_phone ?? '-' }}</td>
            </tr>
        </table>

        <table class="mb-2">
            <tr>
                <td style="width: 70px;">Florist/Kasir</td>
                <td>: {{ $order->handled_by ?? auth()->user()->name }}</td>
            </tr>
            <tr>
                <td>Tipe</td>
                <td>: {{ $order->delivery_method === 'pickup' ? 'Ambil di tempat' : 'Delivery' }}</td>
            </tr>
            <tr>
                <td>{{ $order->delivery_method === 'pickup' ? 'Diambil' : 'Diantar' }}</td>
                <td>: {{ $order->scheduled_at ? \Carbon\Carbon::parse($order->scheduled_at)->format('d/m/Y, H.i.s') : '-' }}</td>
            </tr>
            @if($order->delivery_method === 'delivery')
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td>: {{ $order->delivery_address ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <table class="mb-2">
            <tr>
                <td style="width: 70px;">Bayar</td>
                @php
                    $verifiedPayment = $order->payments()->where('status', 'verified')->first();
                @endphp
                <td>: {{ $verifiedPayment ? strtoupper($verifiedPayment->payment_method) : 'BELUM LUNAS' }}</td>
            </tr>
        </table>

        <div class="border-dashed"></div>

        <!-- Items -->
        <div class="mb-2">
            @if($order->source === 'online')
                <div>{{ $order->product_name ?? 'Produk Custom' }}</div>
                <div style="padding-left: 10px; font-size: 11px; margin-bottom: 2px;">
                    @if($order->notes)
                        @foreach(explode("\n", $order->notes) as $noteLine)
                            @if(trim($noteLine) !== '')
                                <div>- {{ trim($noteLine) }}</div>
                            @endif
                        @endforeach
                    @endif
                    
                    @if($order->greeting_card)
                        <div style="margin-top: 5px; font-weight: bold;">[Kartu Ucapan]:</div>
                        @foreach(explode("\n", $order->greeting_card) as $cardLine)
                            @if(trim($cardLine) !== '')
                                <div style="font-style: italic;">"{{ trim($cardLine) }}"</div>
                            @endif
                        @endforeach
                    @endif
                </div>
                <table style="width: 100%;">
                    <tr>
                        <td>1 x Rp {{ number_format($order->total_amount - $order->delivery_fee + $order->discount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($order->total_amount - $order->delivery_fee + $order->discount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            @else
                @php $hasRental = false; @endphp
                @foreach($order->items as $item)
                @php if($item->is_rented) $hasRental = true; @endphp
                <div>{{ $item->product_name }} @if($item->is_rented) <strong>(Sewa {{ $item->rental_duration }} Hari)</strong> @endif</div>
                @if($item->is_rented)
                <div style="padding-left: 10px; font-size: 11px; margin-bottom: 2px;">
                    <em>Tgl Kembali: {{ $order->created_at->addDays($item->rental_duration)->format('d/m/Y') }}</em>
                </div>
                @endif
                @if($item->components && $item->components->count() > 0)
                <div style="padding-left: 10px; font-size: 11px; margin-bottom: 2px;">
                    @foreach($item->components as $comp)
                    <div>- {{ $comp->qty }}x {{ $comp->material_name }}</div>
                    @endforeach
                </div>
                @endif
                <table style="width: 100%;">
                    <tr>
                        <td>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
                @endforeach
            @endif
        </div>

        <div class="border-dashed"></div>

        <!-- Calculation -->
        <div class="mb-2">
            <table style="width: 100%;">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp {{ number_format($order->source === 'online' ? ($order->total_amount - $order->delivery_fee + $order->discount) : $order->items->sum('subtotal'), 0, ',', '.') }}</td>
                </tr>
                
                @if($order->delivery_fee > 0)
                <tr>
                    <td>Ongkir</td>
                    <td class="text-right">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($order->discount > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
            
            <div class="text-right mt-2" style="font-weight: bold;">
                TOTAL: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </div>
        </div>
        
        @if($order->payment_status !== 'paid')
        <div class="text-center mt-2 mb-2">
            Bawa ini ke kasir untuk pembayaran
        </div>
        @endif

        <div class="border-dashed"></div>

        <div class="text-center mt-2 mb-2" style="font-size: 11px;">
            KEBIJAKAN PEMBATALAN:<br>
            Jika pembeli membatalkan pesanan,<br>
            maka DP hangus / potong 50%<br>
            dari total belanja
        </div>
        
        @if(isset($hasRental) && $hasRental)
        <div class="border-dashed"></div>
        <div class="text-center mt-2 mb-2" style="font-size: 11px;">
            <strong>PERHATIAN (SEWA):</strong><br>
            Barang sewaan wajib dikembalikan tepat waktu. Keterlambatan dapat dikenakan denda.
        </div>
        @endif

    </div>
    
    @if($copy === 1)
        <div class="page-break"></div>
    @endif
    @endfor

    <div class="no-print" style="margin-top: 30px;">
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Cetak Ulang</button>
        <button onclick="window.close()" class="btn btn-primary">⬅ Tutup Layar Ini</button>
    </div>
</body>
</html>
