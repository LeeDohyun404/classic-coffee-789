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
?>