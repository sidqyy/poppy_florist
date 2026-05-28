# Rekapitulasi & Dokumentasi Fitur Poppy Florist

Dokumen ini berisi rekap lengkap mengenai seluruh modul, alur kerja (workflow), serta fitur-fitur yang terintegrasi di dalam aplikasi **Poppy Florist**.

---

## 👥 1. Sistem Multi-Role & Hak Akses
Sistem ini menggunakan pembagian hak akses (role-based access control) untuk memastikan keamanan dan efisiensi kerja tim toko:

1. **Owner (Pemilik Toko):**
   * Memiliki **Akses Bypass Penuh (Super Admin)** ke seluruh fitur sistem (Admin, Dapur, Kasir, Marketing, Laporan, Pengaturan, dan Inventaris).
2. **Admin & Asisten Manajer (Asmen):**
   * Hak akses penuh untuk mengelola master data produk, kategori, bahan baku, voucher promo, melihat laporan keuangan bulanan, mengaudit log aksi, dan mengonfigurasi pengaturan toko.
3. **Marketing:**
   * Akses khusus ke dashboard penjualan online, melihat etalase produk, merakit buket custom via kalkulator, menginput pesanan online (dari WA/IG/Web), dan melacak status pesanan pelanggan.
4. **Florist (Tim Produksi/Dapur):**
   * Akses khusus ke dashboard dapur rangkaian bunga, mengubah status antrian produksi ("Mulai Kerjakan" & "Selesai"), dan mencatat komponen bahan baku riil yang dihabiskan untuk pesanan custom.

---

## 📊 2. Dashboard Interaktif (Premium & Modern)
Semua dashboard dirancang dengan visual modern, gradien mewah, responsif, dan ramah pengguna:

* **Dashboard Admin & Owner (Emerald-Gold theme):**
   * Ringkasan pendapatan hari ini, pesanan diproses, antrian mendesak, persewaan aktif, dan pengembalian sewa.
   * Modul statistik produk terlaris, perbandingan performa penjualan online vs offline, pemakaian logistik hari ini, serta persentase metode pembayaran.
* **Dashboard Marketing (Violet-Pink theme):**
   * Statistik lead harian, target omzet online, antrian pending, dan *interactive checklist focus board* untuk memantau kelancaran pesanan online di dapur.
* **Dashboard Florist (Dark Indigo-Pink theme):**
   * Sambutan otomatis berdasarkan waktu (*Selamat Pagi/Siang/Sore/Malam*), ringkasan antrian, indikator stok material kritis, serta **Live Kitchen Queue Preview** yang dilengkapi animasi denyut hijau real-time.

---

## 🛍️ 3. Transaksi & Kasir POS (Point of Sales)
* **Katalog Produk Standar:** Memungkinkan kasir/kios untuk melakukan checkout produk katalog yang sudah memiliki resep tetap (seperti buket mawar merah standar). Stok bahan baku otomatis terpotong saat transaksi selesai.
* **Kalkulator Custom Bucket (Draft Builder):**
   * Membantu kasir/marketing merakit buket custom secara langsung di depan pelanggan dengan memilih bahan baku riil (mawar, wrapping paper, pita, dll.).
   * Menampilkan harga bahan baku secara real-time dan melakukan validasi otomatis agar tidak merakit bahan yang stoknya kosong.
   * Hasil rakitan dapat disimpan sebagai **Draft Custom Product** untuk di-checkout kemudian.
* **Manajemen Promo & Voucher:** Validasi kuota voucher, minimal belanja, tipe diskon (persentase atau nominal tetap), serta penghitungan otomatis di lembar kasir.
* **Cetak Nota Kasir:** Fitur cetak nota berformat struk fisik untuk pesanan yang telah lunas.

---

## 📱 4. Input Pesanan Online (Marketing)
* **Form Input Online Terintegrasi:** Form ringkas untuk memindahkan pesanan dari obrolan WhatsApp atau Instagram ke sistem dapur.
* **Pengunggahan Foto Referensi & Kartu Ucapan:** Marketing dapat mengunggah gambar referensi buket dari pelanggan agar tim florist dapat merakit bunga dengan tingkat kemiripan tinggi, serta menulis kartu ucapan langsung.
* **Pembedaan Sifat Pesanan (Prefix Otomatis):**
   * `PESM` (Pesanan Marketing - reguler).
   * `PJLM` (Penjualan Marketing - kilat / di bawah 3 jam).
* **Nomor Order Manual (Khusus PESW - Pesanan Web):**
   * Apabila memilih prefix `PESW`, tim marketing dapat memasukkan nomor order secara manual dari website e-commerce Anda (misalnya: Shopify/WooCommerce).
   * **Pembersihan Cerdas (Auto-clean):** Sistem otomatis merapikan format jika marketing salah mengetik (misal `"PESW-1234"` otomatis disimpan sebagai `"PESW1234"`).
   * **Validasi Keunikan:** Sistem menjamin tidak ada duplikasi nomor order di database.

---

## 🍳 5. Dapur Florist (Kitchen & Production Board)
* **Antrian Dapur Visual:** Menampilkan pesanan aktif dalam bentuk kartu modern yang diurutkan berdasarkan prioritas darurat (`is_urgent`) dan batas waktu kirim (`scheduled_at`).
* **Timeline Produksi:** Tombol cepat "Mulai Kerjakan" (mencatat waktu `started_at` kerja florist) dan "Selesai Dirangkai" (mencatat waktu `completed_at` selesai merangkai).
* **Pencatatan Bahan Baku Kustom (Penting):**
   * Florist dapat menambahkan bahan baku riil yang digunakan untuk merangkai pesanan custom via modul **Kelola Komponen Bahan**.
   * Stok gudang akan otomatis berkurang secara akurat dan tercatat di Jurnal Mutasi saat Florist menyimpan komponen tersebut.

---

## 🌿 6. Manajemen Inventaris & Jurnal Mutasi Stok
* **Sistem Jurnal Mutasi 100% Audit-Ready:**
   * Setiap pengurangan stok (baik karena transaksi katalog, penambahan komponen oleh florist, maupun restock) wajib mencatat data audit lengkap: **Stok Sebelum (`stock_before`)** dan **Stok Sesudah (`stock_after`)**.
   * Mencatat data operator (siapa yang memotong/menambah stok), waktu perubahan, tipe mutasi (`in` / `out`), serta catatan keterangan pesanan yang berkaitan.
* **Masa Kedaluwarsa Bunga Segar:** Menghitung masa segar bunga (misal: mawar segar kedaluwarsa otomatis dalam 5-7 hari) saat di-restock ke sistem.
* **Peringatan Stok Menipis:** Indikator visual merah menyala di dashboard jika stok bahan baku berada di bawah 10 pcs untuk mencegah kendala produksi.

---

## 💬 7. Integrasi WhatsApp Notification
* Membantu admin/marketing mengirimkan pesan notifikasi status pesanan ke nomor WhatsApp pelanggan secara cepat menggunakan tautan template otomatis:
  * **Received:** Pemberitahuan bahwa pesanan telah diterima sistem.
  * **Payment Verified:** Konfirmasi pembayaran DP/Lunas telah diverifikasi.
  * **Processing:** Informasikan bahwa bunga sedang mulai dirangkai oleh Florist.
  * **Ready:** Foto hasil rangkaian siap kirim.
  * **Delivery:** Informasi kurir sedang mengantar pesanan ke alamat tujuan.
  * **Completed:** Ucapan terima kasih setelah pesanan sampai dengan selamat.

---

## 🔒 8. Fitur Keamanan & Pemeliharaan Sistem
* **Audit Log Aktivitas:** Mencatat setiap aksi penting (seperti merubah harga produk, menambah stok, menghapus data, mencetak ulang nota) lengkap dengan detail data sebelum dan sesudah perubahan.
* **Backup Database:** Fitur pencadangan database sekali klik dari panel admin untuk mencegah kehilangan data transaksi.
