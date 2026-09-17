# Panduan Update (Fitur PWA & Multiple Upload Foto)

Dokumen ini memuat daftar file apa saja yang berubah dan harus di-copy paste ke PC Utama Toko, serta langkah penting untuk update database.

## 1. Daftar File yang Berubah (Harus di-Copas)

Anda bisa meng-copy paste dan me-replace (menimpa) spesifik file-file berikut ini ke PC toko:

**Folder `app/`**
- `app/Models/Order.php`
- `app/Models/Payment.php`
- `app/Http/Controllers/OrderController.php`
- `app/Http/Controllers/PaymentController.php`

**Folder `database/`**
- `database/migrations/2026_09_17_144346_change_image_columns_to_text_in_orders_and_payments.php`

**Folder `resources/`**
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/pos.blade.php`
- `resources/views/orders/online_create.blade.php`
- `resources/views/orders/online_edit.blade.php`
- `resources/views/orders/show.blade.php`

**Folder `public/`**
- `public/manifest.json`
- `public/sw.js`
- Folder `public/icons/` secara utuh

*(Tips: Cara paling aman dan cepat adalah langsung saja copas folder `app`, `database`, `resources`, dan `public` untuk menimpa folder lama Anda sekaligus).*

---

## 2. Update Database (WAJIB)
Karena fitur ini memperbesar ukuran kolom penyimpanan gambar di database agar bisa menyimpan banyak foto, Anda **Wajib** memperbarui struktur tabel di PC Toko menggunakan salah satu cara di bawah ini:

### Pilihan A: Menggunakan CMD (Direkomendasikan)
Buka terminal / CMD di dalam folder proyek pada PC toko Anda, lalu ketik:
```bash
php artisan migrate
```

### Pilihan B: Menggunakan phpMyAdmin (Manual)
Jika Anda tidak bisa membuka CMD, buka browser menuju phpMyAdmin (contoh: `http://localhost/phpmyadmin`). 
Pilih database toko Anda, klik tab **SQL**, copas kode di bawah ini, lalu klik tombol **Kirim / Go**:
```sql
ALTER TABLE orders MODIFY reference_image TEXT NULL;
ALTER TABLE orders MODIFY payment_proof TEXT NULL;
ALTER TABLE payments MODIFY proof_image TEXT NULL;
```

---
Setelah file ditimpa dan database diperbarui, fitur upload banyak foto dan install web app sudah bisa dinikmati di PC toko Anda!
