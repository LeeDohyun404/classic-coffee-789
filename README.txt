Classic Coffee 789

Classic Coffee 789** adalah aplikasi web e-commerce sederhana yang dibuat untuk kedai kopi fiktif. Proyek ini mencakup fitur-fitur standar seperti penjelajahan produk, keranjang belanja, checkout untuk tamu dan anggota, serta program loyalitas untuk pelanggan terdaftar. Website ini juga dilengkapi dengan dashboard admin untuk manajemen pesanan.

Fitur Utama

Untuk Pelanggan:

  - Menu Dinamis: Menampilkan produk yang terbagi dalam kategori Paket, Makanan, dan Minuman.
  - Keranjang Belanja: Pelanggan dapat menambah, mengurangi, dan melihat item di keranjang belanja.
  - Login & Registrasi: Sistem registrasi dan login untuk pelanggan.
  - Program Loyalitas: Fitur "Beli 10 Minuman Gratis 1" yang memberikan voucher otomatis kepada pelanggan terdaftar.
  - Halaman Voucher: Pelanggan dapat melihat daftar voucher yang mereka miliki beserta masa berlakunya.
  - Checkout: Proses pemesanan yang dapat dilakukan oleh tamu maupun pelanggan terdaftar.

Untuk Admin:

  - Login Aman: Halaman login terpisah untuk admin.
  - Dashboard Statistik: Menampilkan ringkasan total pesanan, pendapatan, dan pesanan berdasarkan periode waktu.
  - Manajemen Pesanan:
      - Melihat semua pesanan yang masuk.
      - Mengedit detail pelanggan pada pesanan.
      - Menghapus item dari pesanan dan total harga akan ter-update otomatis.
      - Menghapus pesanan secara keseluruhan.
      - Mencetak struk untuk setiap pesanan.

Teknologi yang Digunakan

Proyek ini dibangun tanpa menggunakan framework (PHP Native) untuk memaksimalkan pemahaman dasar pengembangan web.

  - Bahasa Sisi Server (Backend): PHP
  - Bahasa Sisi Klien (Frontend): HTML, CSS, JavaScript (untuk interaktivitas)
  - Database: MySQL
  - Lingkungan Pengembangan Lokal: XAMPP (Apache, MySQL, PHP, phpMyAdmin)
  - Manajemen Kode: Git & GitHub

Cara Instalasi di Localhost (XAMPP)

1.  Clone Repositori:

    bash
    git clone [URL_REPOSITORI_ANDA]
    

    Atau unduh ZIP dan ekstrak ke `C:\xampp\htdocs\`

2.  Buat Database:

      - Buka phpMyAdmin (`http://localhost/phpmyadmin`).
      - Buat database baru dengan nama `db_classic_coffee`.
      - Pilih database tersebut, buka tab SQL, lalu impor file `db_classic_coffee.sql` yang ada di repositori ini.

3.  Konfigurasi Koneksi:

      - Buka file `config.php`.
      - Pastikan detail koneksi sudah sesuai dengan pengaturan XAMPP Anda.
        ```php
        define('BASE_URL', 'http://localhost/nama_folder_proyek');
        $db_host = 'localhost';
        $db_user = 'root';
        $db_pass = '';
        $db_name = 'db_classic_coffee';
        ```

4.  Jalankan:

      - Buka browser dan akses `http://localhost/nama_folder_proyek/`.