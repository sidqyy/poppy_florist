# Poppy Florist - Sistem Manajemen & POS Kasir

Selamat datang di Sistem Manajemen & Point of Sale (POS) Toko Bunga Poppy Florist! Sistem ini dibangun khusus untuk menangani alur kerja end-to-end toko bunga modern, mulai dari penerimaan bahan baku, perangkaian bunga di dapur florist, pesanan online via WhatsApp/IG, hingga kasir berlayar sentuh untuk pelanggan yang datang langsung (walk-in).

---

## 🔐 1. Daftar Akun Default (Role)

Sistem ini memiliki 4 hak akses (Role) dengan tampilan dashboard dan menu yang berbeda-beda. Gunakan akun berikut untuk masuk ke sistem:

| Peran (Role) | Email | Password | Fungsi Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@florist.com` | `password` | Mengatur Master Data (Bahan, Produk, Supplier), Stok, Backup DB, Audit Log |
| **Kasir / Florist** | `florist@florist.com` | `password` | Mengoperasikan Mesin Kasir POS Layar Sentuh, mengelola Antrian Dapur, dan mengeksekusi pesanan offline. |
| **Marketing** | `marketing@florist.com` | `password` | Menginput pesanan pelanggan dari Online (WhatsApp / Instagram DM). |
| **Owner** | `owner@florist.com` | `password` | Melihat Laporan Penjualan (Harian/Bulanan), Produk Terlaris, dan Performa Marketing. |

---

## 💻 2. Cara Menjalankan Sistem di PC Server Toko (On-Premise)

Sistem ini ditanamkan di satu PC utama yang berfungsi sebagai **Server Lokal Toko**.
Setiap pagi ketika PC dinyalakan, staf harus melakukan langkah berikut:

1. **Nyalakan Laragon**: 
   Buka aplikasi `Laragon` di desktop, lalu klik tombol **"Start All"**. Pastikan indikator **Apache** dan **MySQL** berwarna biru.
2. **Jalankan Background Worker (Penting untuk Antrian)**:
   Buka terminal/CMD di dalam folder `c:\laragon\www\poppy_florist` lalu jalankan perintah:
   ```bash
   php artisan queue:work
   ```
   *(Biarkan jendela hitam ini tetap terbuka dan di-minimize).*
3. **Buka Aplikasi**:
   Buka Google Chrome dan ketikkan alamat: **`http://127.0.0.1:8000`** atau **`http://localhost`** (tergantung konfigurasi Laragon Anda).

---

## 📱 3. Cara Mengakses Sistem dari Device Lain (iPad / PC Kasir) via LAN/WiFi

Keunggulan sistem ini adalah bisa diakses oleh beberapa perangkat sekaligus (misal: 1 PC Admin di ruang belakang, 1 Tablet Layar Sentuh di meja kasir depan, 1 HP milik Marketing) **tanpa perlu koneksi internet**, asalkan tersambung ke WiFi toko yang sama.

**Langkah-langkah untuk Perangkat Lain:**
1. Pastikan PC Server Toko (Langkah 2) sudah menyala.
2. Pastikan Tablet Kasir atau HP Marketing **tersambung ke WiFi yang sama** dengan PC Server Toko.
3. Cari tahu **IP Address PC Server Toko**. (Di PC Server, buka CMD dan ketik `ipconfig`, cari baris *IPv4 Address*, misalnya `192.168.1.10`).
4. Di Tablet Kasir / HP Marketing, buka Google Chrome / Safari dan ketikkan IP tersebut diikuti port 8000:
   👉 **`http://192.168.1.10:8000`**
5. Selesai! Tablet tersebut kini berubah menjadi mesin Kasir nirkabel Anda.

---

## 💳 4. Mengakses Mesin Kasir Layar Sentuh (POS)

Untuk staf kasir yang menggunakan PC Layar Sentuh / Tablet di meja kasir depan:
1. Login menggunakan akun `florist@florist.com`.
2. Di menu kiri atas (atau langsung ketik di browser), buka alamat khusus ini:
   👉 **`http://127.0.0.1:8000/pos`** (Atau `http://192.168.1.10:8000/pos` jika dari tablet).
3. Anda akan masuk ke layar POS Penuh tanpa gangguan menu lain untuk memproses pembayaran dengan sangat cepat!

---

## 🛡️ 5. Fitur Jaring Pengaman (Backup Otomatis)

Data toko Anda aman. Sistem otomatis mem-backup (*export*) seluruh database setiap jam 23:00 malam ke dalam folder lokal komputer.
Pemilik (Owner/Admin) bisa mengunduh file `.sql` tersebut ke Flashdisk secara berkala melalui menu **"Laporan & Sistem > Backup Database"**. Jika suatu hari PC Server rusak, data bisa dikembalikan 100% menggunakan file tersebut di menu yang sama.

---
*Dibuat khusus untuk Poppy Florist - 2026*
