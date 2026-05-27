<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Poppy Florist</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #ec4899; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #ec4899; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
        .periode { text-align: right; margin-bottom: 20px; font-weight: bold; }
        
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 10px; }
        .box { border: 1px solid #ddd; padding: 15px; width: 23%; text-align: center; border-radius: 5px; }
        .box h3 { margin: 0 0 5px 0; font-size: 12px; color: #666; text-transform: uppercase; }
        .box p { margin: 0; font-size: 18px; font-weight: bold; }
        
        h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; font-size: 16px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f9f9f9; font-size: 12px; text-transform: uppercase; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #ec4899; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Document</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #ccc; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h1>POPPY FLORIST</h1>
        <p>Jl. Bunga Raya No. 123, Kota Kembang | WA: 0812-3456-7890</p>
        <p><strong>DOKUMEN LAPORAN PENJUALAN & OPERASIONAL</strong></p>
    </div>

    <div class="periode">
        Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
    </div>

    <div class="summary-box">
        <div class="box">
            <h3>Total Pendapatan</h3>
            <p>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="box">
            <h3>Total Transaksi</h3>
            <p>{{ number_format($totalOrders) }}</p>
        </div>
        <div class="box">
            <h3>Total Ongkir</h3>
            <p>Rp {{ number_format($totalDeliveryFee, 0, ',', '.') }}</p>
        </div>
        <div class="box">
            <h3>Total Diskon</h3>
            <p>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div style="width: 50%;">
            <h2>Statistik Tipe Pesanan</h2>
            <table>
                <tr><th>Online (WA/IG)</th><td class="text-right">{{ $onlineOrders }} pesanan</td></tr>
                <tr><th>Offline (Datang ke Toko)</th><td class="text-right">{{ $offlineOrders }} pesanan</td></tr>
            </table>
        </div>
        <div style="width: 50%;">
            <h2>Metode Pengambilan</h2>
            <table>
                <tr><th>Dikirim Kurir (Delivery)</th><td class="text-right">{{ $deliveryCount }} pesanan</td></tr>
                <tr><th>Ambil Sendiri (Pickup)</th><td class="text-right">{{ $pickupCount }} pesanan</td></tr>
            </table>
        </div>
    </div>

    <h2>Top 5 Produk Terlaris</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th class="text-center">Jumlah Terjual</th>
                <th class="text-right">Subtotal Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $index => $prod)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $prod->product_name }}</td>
                <td class="text-center">{{ $prod->total_qty }}</td>
                <td class="text-right">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($topProducts->isEmpty())
            <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
            @endif
        </tbody>
    </table>

    <h2>Top 5 Bunga/Material Terpakai</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Bunga/Material</th>
                <th class="text-right">Total Terpakai (Pcs/Tangkai)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topMaterials as $index => $mat)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $mat->material_name }}</td>
                <td class="text-right">{{ $mat->total_used }}</td>
            </tr>
            @endforeach
            @if($topMaterials->isEmpty())
            <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
        <p>Oleh: {{ auth()->user()->name ?? 'Administrator' }}</p>
    </div>

</body>
</html>
