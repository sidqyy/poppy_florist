<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemComponent;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month if no date range is provided
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Base Query (Hanya pesanan yang tidak dibatalkan)
        $baseQuery = Order::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', '!=', 'cancelled');

        // 1. Ringkasan Utama
        $totalRevenue = (clone $baseQuery)->sum('total_amount');
        $totalOrders = (clone $baseQuery)->count();
        $totalDeliveryFee = (clone $baseQuery)->sum('delivery_fee');
        $totalDiscount = (clone $baseQuery)->sum('discount');

        // 2. Statistik Online vs Offline
        $onlineOrders = (clone $baseQuery)->where('order_number', 'LIKE', 'ONL-%')->count();
        $offlineOrders = (clone $baseQuery)->where('order_number', 'LIKE', 'ORD-%')->count();

        // 3. Statistik Pengiriman
        $deliveryCount = (clone $baseQuery)->where('delivery_method', 'delivery')->count();
        $pickupCount = (clone $baseQuery)->where('delivery_method', 'pickup')->count();

        // 4. Tren Penjualan Harian (Untuk Grafik Garis)
        $dailySales = (clone $baseQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(id) as total_orders'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // 5. Produk Terlaris
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select('order_items.product_name', DB::raw('SUM(order_items.qty) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 6. Bunga / Material Paling Banyak Digunakan
        $topMaterials = DB::table('order_item_components')
            ->join('order_items', 'order_items.id', '=', 'order_item_components.order_item_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select('order_item_components.material_name', DB::raw('SUM(order_item_components.qty) as total_used'))
            ->groupBy('order_item_components.material_name')
            ->orderByDesc('total_used')
            ->limit(5)
            ->get();

        // 7. Penggunaan Promo
        $promoUsage = DB::table('promo_usages')
            ->join('promos', 'promos.id', '=', 'promo_usages.promo_id')
            ->join('orders', 'orders.id', '=', 'promo_usages.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('promos.code', 'promos.name', DB::raw('COUNT(promo_usages.id) as times_used'), DB::raw('SUM(promo_usages.discount_amount) as total_discount_given'))
            ->groupBy('promos.id', 'promos.code', 'promos.name')
            ->orderByDesc('times_used')
            ->get();

        $data = compact(
            'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'totalDeliveryFee', 'totalDiscount',
            'onlineOrders', 'offlineOrders', 'deliveryCount', 'pickupCount',
            'dailySales', 'topProducts', 'topMaterials', 'promoUsage'
        );

        if ($request->action == 'print') {
            return view('admin.reports.print', $data);
        }

        if ($request->action == 'export_csv') {
            return $this->exportCsv($data);
        }

        return view('admin.reports.index', $data);
    }

    private function exportCsv($data)
    {
        $fileName = 'Laporan_PoppyFlorist_' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['Tanggal', 'Total Order', 'Pendapatan (Rp)'];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RINGKASAN LAPORAN']);
            fputcsv($file, ['Periode', $data['startDate']->format('d/m/Y') . ' - ' . $data['endDate']->format('d/m/Y')]);
            fputcsv($file, ['Total Pendapatan', $data['totalRevenue']]);
            fputcsv($file, ['Total Pesanan', $data['totalOrders']]);
            fputcsv($file, ['Total Ongkir', $data['totalDeliveryFee']]);
            fputcsv($file, ['Total Diskon', $data['totalDiscount']]);
            fputcsv($file, []);

            fputcsv($file, ['TREN HARIAN']);
            fputcsv($file, $columns);

            foreach ($data['dailySales'] as $sale) {
                $row['Tanggal']  = $sale->date;
                $row['Total Order'] = $sale->total_orders;
                $row['Pendapatan']  = $sale->revenue;
                fputcsv($file, array($row['Tanggal'], $row['Total Order'], $row['Pendapatan']));
            }
            
            fputcsv($file, []);
            fputcsv($file, ['PRODUK TERLARIS']);
            fputcsv($file, ['Nama Produk', 'Terjual', 'Subtotal Pendapatan']);
            foreach ($data['topProducts'] as $prod) {
                fputcsv($file, [$prod->product_name, $prod->total_qty, $prod->total_revenue]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
