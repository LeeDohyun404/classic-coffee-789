<?php
// Mulai session di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definisikan alamat dasar website Anda untuk XAMPP
define('BASE_URL', 'http://localhost/classic_coffe_789');

// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'if0_39411554_kopi';

// Buat koneksi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
// =======================================================
// TAMBAHAN BARU: LOGIKA UNTUK GLOBAL DISKON TOGGLE
// =======================================================
// Definisikan path ke file status diskon
define('DISKON_STATUS_FILE', __DIR__ . '/diskon_status.txt');

// Baca status dari file. Default 'nonaktif' jika file tidak ada.
$is_diskon_aktif = (file_exists(DISKON_STATUS_FILE) && file_get_contents(DISKON_STATUS_FILE) === 'aktif');

// Buat konstanta global agar bisa diakses di semua halaman
define('GLOBAL_DISKON_AKTIF', $is_diskon_aktif);
?>
