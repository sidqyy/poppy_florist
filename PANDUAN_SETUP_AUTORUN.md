# Panduan Setup Auto-Run Server (Tanpa Ribet Terminal)

Dokumen ini menjelaskan langkah-langkah teknis agar aplikasi **Poppy Florist** otomatis menyala di latar belakang saat komputer utama (PC One) dinyalakan. Tim Florist dan Marketing tidak perlu membuka Command Prompt (CMD), mengetik perintah `php artisan serve`, atau membuka aplikasi Laragon secara manual.

---

## 🚀 Langkah 1: Aktifkan Fitur Auto-Start di Laragon
Laragon memiliki opsi bawaan untuk otomatis berjalan saat Windows booting tanpa menampilkan jendela aplikasi yang mengganggu layar.

1. Buka aplikasi **Laragon** di PC Utama (PC One).
2. Klik ikon **Roda Gigi / Settings** di pojok kanan atas tampilan Laragon.
3. Pada tab **General**, centang dua opsi berikut:
   * **`[✓] Run Laragon when Windows starts`** (Menjalankan Laragon otomatis saat Windows dinyalakan).
   * **`[✓] Start All automatically`** (Otomatis menghidupkan Apache, MySQL, dll. secara instan di latar belakang).
4. Tutup jendela pengaturan Laragon.

---

## 💻 Langkah 2: Buat Script Auto-Run untuk `php artisan serve`
Kita memerlukan server web untuk memancarkan sinyal IP (agar HP/Laptop tim Marketing dan Florist lain bisa mengakses via Wi-Fi). Kita akan mendaftarkan perintah ini di folder Startup Windows.

1. Di area kosong Desktop Anda, klik kanan > **New** > **Text Document**.
2. Buka file teks baru tersebut, lalu salin dan tempel kode script berikut:
   ```bat
   @echo off
   title Server Poppy Florist
   cd /d "c:\laragon\www\poppy_florist"
   start /min php artisan serve --host=0.0.0.0 --port=8000
   ```
3. Simpan file tersebut dengan menekan tombol **File** > **Save As...**.
   * Ubah nama file menjadi: **`Mulai_Poppy_Florist.bat`** (penting: ganti ekstensi `.txt` menjadi **`.bat`**).
   * Pada kolom *Save as type*, pilih *All Files (`*.*`)*.
   * Klik **Save**.
4. **Daftarkan Script ke Windows Startup:**
   * Tekan tombol tombol **Windows + R** di keyboard Anda untuk membuka menu pencarian cepat Windows (*Run*).
   * Ketik **`shell:startup`** pada kolom pencarian lalu tekan Enter.
   * Folder **Startup** Windows akan otomatis terbuka.
   * **Pindahkan (Cut & Paste)** file `Mulai_Poppy_Florist.bat` yang Anda buat tadi di desktop ke dalam folder *Startup* yang baru terbuka ini.

*Sekarang, setiap kali komputer utama dinyalakan, server database dan server internal Laravel akan otomatis menyala di latar belakang tanpa disadari oleh pengguna.*

---

## 🔗 Langkah 3: Buat Ikon Jalan Pintas (Desktop Shortcut) untuk Florist
Agar Florist cukup mengeklik satu kali dari layar komputer utama untuk langsung masuk ke aplikasi:

1. Di area kosong Desktop, klik kanan > **New** > **Shortcut**.
2. Pada kolom lokasi (*Type the location of the item*), ketik alamat lokal aplikasi Anda:
   `http://localhost:8000`
3. Klik **Next**.
4. Beri nama shortcut ini:
   **`Aplikasi Poppy Florist`**
5. Klik **Finish**.
6. *(Opsional)* Agar tampilan lebih cantik, Anda bisa mengganti ikon shortcut tersebut:
   * Klik kanan pada ikon shortcut > **Properties**.
   * Di tab *Web Document*, klik **Change Icon...**.
   * Pilih ikon browser Chrome, Edge, atau logo yang Anda sukai, lalu klik **OK** dan **Apply**.

---

## 📱 Langkah 4: Cara Mengakses dari HP / Laptop Lain (Via Wi-Fi)
Selama PC Utama menyala dan terhubung ke jaringan Wi-Fi toko yang sama, tim lain dapat langsung mengakses sistem dari perangkat mereka sendiri tanpa perlu menyalakan server apa pun lagi.

1. Cari tahu IP Address lokal dari PC Utama Anda (misalnya IP Anda adalah: `192.168.100.194`).
2. Tim Marketing & Florist lain cukup membuka Google Chrome di HP / Laptop masing-masing, lalu mengetik alamat IP tersebut beserta portnya:
   `http://192.168.100.194:8000`
3. Aplikasi akan langsung terbuka, dan mereka bisa login menggunakan akun masing-masing (seperti akun `marketing1`, `florist1`, dll.).
