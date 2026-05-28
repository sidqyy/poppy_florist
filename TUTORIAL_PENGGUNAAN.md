# Panduan & Tutorial Penggunaan Sistem Poppy Florist

Dokumen ini adalah panduan praktis (langkah-demi-langkah) bagi setiap peran pengguna di Poppy Florist untuk menjalankan aktivitas operasional toko sehari-hari.

---

## 🔑 0. Cara Masuk ke Sistem (Login)
1. Buka browser dan akses alamat aplikasi Poppy Florist (misal: `http://localhost:8000`).
2. Masukkan **Username** dan **Password** Anda sesuai daftar akun yang diberikan.
3. Klik tombol **Login**. Sistem akan secara otomatis mengarahkan Anda ke Dashboard sesuai dengan hak akses/role Anda.

---

## 👑 1. Panduan Penggunaan untuk OWNER & ADMIN / ASMEN

Sebagai Owner/Admin, Anda memiliki hak penuh untuk mengelola katalog toko, inventaris, memantau keuangan, serta mengaudit aktivitas sistem.

### A. Mengelola Katalog & Harga Produk
1. Di sidebar kiri, masuk ke menu **Master Data & Stok** > **Katalog Produk (Barang)**.
2. **Tambah Produk Baru:**
   * Klik tombol **Tambah Produk**.
   * Isi Nama Produk, Deskripsi, Unggah Foto, pilih Kategori, dan tentukan Tipe Harga (*Fixed* atau *Range*).
   * **Mengaitkan Bahan Baku (Opsional):** Jika produk memiliki resep tetap (misal: "Buket Mawar Cantik" selalu memakai 5 tangkai Mawar Merah + 1 lembar Wrap), tambahkan bahan baku tersebut di bagian bawah. Ini akan membuat stok bahan baku terpotong otomatis saat checkout.
   * Klik **Simpan**.
3. **Mengedit / Menghapus:** Klik tombol **Edit** (ikon pensil) atau **Hapus** (ikon tempat sampah) pada tabel produk.

### B. Mengelola Stok Bahan Baku & Restock
1. Masuk ke menu **Master Data & Stok** > **Bahan Baku**.
2. **Menambah Jenis Bahan Baru:** Klik **Tambah Bahan Baku**, tentukan nama, kategori logistik (Bunga Segar/Wrapping/Aksesoris), satuan (pcs/lembar), harga beli per pcs, dan harga jual per pcs.
3. **Melakukan Restock (Tambah Stok):**
   * Pada tabel bahan baku, klik tombol **Restock** (ikon tambah `+`) pada bahan yang ingin ditambah.
   * Masukkan **Jumlah (Qty)** baru yang masuk ke toko.
   * Masukkan **Catatan** (misal: "Belanja dari Supplier A").
   * **Bunga Segar (Flower Fresh):** Untuk bunga segar, sistem akan otomatis menghitung masa kedaluwarsa kesegaran bunga sesuai konfigurasi hari kesegaran.
   * Klik **Simpan**. Stok akan bertambah dan tercatat di **Jurnal Mutasi Stok** dengan keterangan *Restock*.

### C. Memantau Jurnal Mutasi & Keuangan
1. **Laporan Penjualan:** Masuk ke menu **Laporan Penjualan**. Tentukan filter tanggal untuk memantau grafik omzet, statistik metode pembayaran terpopuler, dan volume produk terlaris.
2. **Mutasi Stok (Audit-Ready Journal):**
   * Masuk ke menu **Master Data & Stok** > **Mutasi Stok**.
   * Anda dapat memantau setiap pergerakan stok bunga/wrapping di toko secara transparan.
   * Setiap mutasi mencantumkan **Stok Awal (`stock_before`)**, **Stok Akhir (`stock_after`)**, siapa operatornya, tipe keluar/masuk, dan catatan transaksinya.
3. **Audit Log:** Masuk ke menu **Audit Log** untuk melihat catatan aktivitas detail yang dilakukan oleh seluruh tim (siapa yang mengubah harga produk, siapa yang mengedit pesanan, dll.) lengkap dengan data sebelum dan sesudahnya (*before-after data change*).

---

## 📱 2. Panduan Penggunaan untuk TIM MARKETING

Tim Marketing fokus melayani pelanggan online (WA/IG), merakit buket custom, menginput pesanan online, dan memperbarui status pembayaran.

### A. Menggunakan Kalkulator Custom (Rakits Bouquet)
*Gunakan fitur ini ketika pelanggan ingin memesan buket unik/custom dengan budget tertentu.*
1. Masuk ke menu **Kalkulator Custom**.
2. Di layar akan tampil daftar seluruh bahan baku bunga segar dan wrapping paper beserta stok riil dan harganya.
3. Pilih bunga dan wrapping yang diinginkan pelanggan, tentukan jumlahnya (Qty).
4. Sistem akan secara otomatis menghitung **Total Harga Jual** berdasarkan bahan-bahan yang Anda pilih secara real-time.
5. Jika pelanggan setuju, klik **Simpan sebagai Draft**. Masukkan Nama Pelanggan dan detail pengiriman. Draft ini akan tersimpan dan siap di-checkout oleh kasir.

### B. Menginput Pesanan Online (WA/IG/Web)
1. Masuk ke menu **Input Order WA/IG**.
2. **Langkah 1 (Informasi Pelanggan):**
   * Pilih **Prefix / Sumber Pesanan**:
     * `PESM` (Pesanan reguler dari marketing - jadwal di atas 3 jam).
     * `PJLM` (Pesanan kilat marketing - jadwal di bawah 3 jam).
     * **`PESW` (Pesanan Web):** Pilih ini jika pesanan berasal dari website e-commerce Anda. **Kotak input Nomor Order Manual akan muncul**. Masukkan nomor order persis seperti yang tertera di website Anda (misal website order ID: `9842`, ketik saja `9842`, sistem akan menyimpannya sebagai `PESW9842`).
   * Isi Nama Pemesan dan No. WhatsApp aktif.
3. **Langkah 2 (Detail Pesanan & Desain):**
   * Ketik **Nama Produk yang Dipesan** (bebas mengetik, contoh: *"Buket Mawar Pink 12 Tangkai Custom"*).
   * Isi **Harga Kesepakatan (Rp)**.
   * Unggah **Foto Referensi Desain** (contoh buket yang disepakati dari obrolan WA/IG).
   * Ketik **Rincian Tambahan** (contoh: *"Kertas pembungkus hitam, pita warna goni"*).
   * Ketik isi teks **Kartu Ucapan**.
4. **Langkah 3 (Jadwal & Pengiriman):**
   * Tentukan **Jadwal Kirim / Ambil**.
   * Pilih **Metode** (*Ambil di Toko* atau *Diantar Kurir*). Jika diantar kurir, lengkapi alamat dan hitung jarak ongkirnya menggunakan tombol **Hitung**.
5. **Langkah 4 (Pembayaran):**
   * Pilih Status Pembayaran (*Lunas*, *DP*, atau *Belum Bayar*).
   * Masukkan **Nominal Dibayar (Rp)**.
   * Unggah bukti transfer jika ada.
6. Klik **Simpan Pesanan**. Pesanan berhasil disimpan dan akan langsung tampil secara real-time di layar **Dapur Florist**.

### C. Mengirim Notifikasi WhatsApp ke Pelanggan
1. Masuk ke menu **Lacak Pesanan** atau buka detail pesanan.
2. Di bagian bawah detail pesanan, Anda akan melihat tombol notifikasi berwarna hijau berlogo WhatsApp (seperti *Received, Payment Verified, Processing, Ready, Delivery, Completed*).
3. Klik tombol notifikasi yang sesuai dengan tahapan pesanan saat ini.
4. Browser akan otomatis membuka tautan WhatsApp dengan pesan template yang sudah jadi. Anda tinggal menekan kirim untuk mengabari pelanggan tanpa perlu mengetik manual lagi.

---

## 🌸 3. Panduan Penggunaan untuk TIM FLORIST / DAPUR PRODUKSI

Tim Florist fokus merangkai bunga di dapur produksi, memperbarui status kerja, dan mengelola stok bahan baku kustom.

### A. Memantau Antrian Dapur
1. Masuk ke menu **Antrian Dapur** (atau klik tombol **Buka Antrian Dapur** di dashboard).
2. Anda akan melihat antrean pesanan dalam bentuk kartu-kartu yang rapi.
3. **Prioritas Utama (Urgent):** Kartu pesanan berwarna merah menyala dengan logo **URGENT 🔥** wajib dikerjakan terlebih dahulu karena memiliki deadline kirim terdekat atau prioritas tinggi.

### B. Memproses Rangkaian Bunga & Mengurangi Stok Custom (Krusial!)
*Karena pesanan online dari marketing bersifat custom bebas, Florist wajib mendaftarkan bahan yang dihabiskan agar stok di sistem tetap akurat.*

1. Pada kartu pesanan di Dapur, klik **"Mulai Kerjakan"**. Status pesanan akan berubah menjadi **Sedang Dirangkai** (mencatat waktu pengerjaan Anda).
2. Bawa bunga fisik dan mulailah merangkai di meja kerja sesuai dengan foto referensi dan catatan tambahan dari marketing.
3. Setelah rangkaian selesai atau di sela-sela merangkai, Florist wajib memasukkan bahan baku yang dihabiskan ke sistem:
   * Pada kartu pesanan tersebut, klik tautan **"Lihat Detail Lengkap"** di bagian paling bawah.
   * Anda akan diarahkan ke halaman detail pesanan. Klik tombol **"Kelola Komponen Bahan"** (atau tombol kelola bahan).
   * Pilih bahan baku yang benar-benar Anda pakai (misal: *Mawar Merah* sebanyak *10*, *Kertas Wrap Hitam* sebanyak *1*).
   * Klik **Simpan**.
   * **Efek Sistem:** Stok bahan baku terpilih di toko seketika itu juga berkurang secara riil, dan sistem langsung mencatat pengeluaran stok tersebut ke Jurnal Mutasi Stok.
4. Kembali ke halaman **Antrian Dapur**, lalu klik tombol **"Selesai Dirangkai"** pada kartu pesanan tersebut.
5. Status pesanan akan otomatis terbarui menjadi **Siap Kirim (Ready)** dan tim kurir/marketing akan segera bersiap mengirimkannya ke pelanggan.
