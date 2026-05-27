<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppService
{
    /**
     * Format phone number to international format (62...)
     */
    public function formatPhoneNumber($phone)
    {
        // Hapus karakter selain angka
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika diawali dengan 0, ubah menjadi 62
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Generate template message based on notification type
     */
    public function generateTemplate(Order $order, $type)
    {
        $shopName = 'Poppy Florist';
        $customerName = $order->customer_name;
        $orderNumber = $order->order_number;
        $totalAmount = 'Rp ' . number_format($order->total_amount, 0, ',', '.');
        
        // Cek URL untuk tracking (Asumsi web berada di domain tertentu, pakai url() helper)
        $trackingUrl = url('/orders/' . $order->id); // Nantinya bisa diubah ke link khusus pelacakan pelanggan jika ada
        
        $message = "";

        switch ($type) {
            case 'received':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Terima kasih telah memesan di *$shopName*! 🌷\n";
                $message .= "Pesanan Kakak dengan nomor *$orderNumber* telah kami terima.\n\n";
                $message .= "Total Tagihan: *$totalAmount*\n";
                $message .= "Status Pembayaran: *" . strtoupper($order->payment_status) . "*\n\n";
                
                if ($order->payment_status == 'unpaid') {
                    $message .= "Silakan lakukan pembayaran agar pesanan dapat segera kami proses ya Kak. 😊\n\n";
                }
                $message .= "Terima kasih,\n*$shopName*";
                break;
                
            case 'payment_verified':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Pembayaran untuk pesanan *$orderNumber* telah kami *TERIMA* dan diverifikasi. ✅\n\n";
                $message .= "Pesanan Kakak akan segera kami jadwalkan untuk dirangkai sesuai urutan antrian.\n";
                $message .= "Terima kasih Kak! 🌺\n\n*$shopName*";
                break;
                
            case 'processing':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Kabar gembira! Pesanan bunga Kakak (*$orderNumber*) saat ini sedang *DIRANGKAI* oleh Florist terbaik kami. ✂️🌹\n\n";
                $message .= "Kami akan mengabari Kakak lagi jika pesanan sudah siap.\n\n";
                $message .= "Salam Hangat,\n*$shopName*";
                break;
                
            case 'ready':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Pesanan bunga Kakak (*$orderNumber*) sudah *SIAP*! 🎉✨\n\n";
                if ($order->delivery_method == 'pickup') {
                    $message .= "Kakak sudah bisa mengambil pesanannya di toko kami ya. Ditunggu kedatangannya! 😊\n\n";
                } else {
                    $message .= "Pesanan sudah siap dan saat ini sedang menunggu kurir untuk dikirim ke alamat Kakak.\n\n";
                }
                $message .= "Terima kasih,\n*$shopName*";
                break;
                
            case 'delivery':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Pesanan bunga Kakak (*$orderNumber*) sedang *DALAM PERJALANAN* menuju lokasi pengiriman! 🛵💨\n\n";
                $message .= "Alamat Pengiriman:\n" . ($order->delivery_address ?? '-') . "\n\n";
                $message .= "Mohon dipastikan ada penerima di lokasi ya Kak. Terima kasih! 🌸\n\n*$shopName*";
                break;
                
            case 'completed':
                $message = "Halo Kak *$customerName*,\n\n";
                $message .= "Pesanan *$orderNumber* telah *SELESAI*. ✅\n\n";
                $message .= "Terima kasih banyak telah mempercayakan momen spesial Kakak kepada *$shopName*. Kami tunggu pesanan selanjutnya! 🥰🌹\n\n";
                $message .= "Salam,\n*$shopName*";
                break;
                
            default:
                $message = "Halo Kak *$customerName*, ada pembaruan mengenai pesanan *$orderNumber* Anda di $shopName.";
        }

        return $message;
    }

    /**
     * Generate WA.me Link
     */
    public function getWaLink(Order $order, $type)
    {
        if (empty($order->customer_phone)) {
            return null;
        }

        $phone = $this->formatPhoneNumber($order->customer_phone);
        $message = $this->generateTemplate($order, $type);
        
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
