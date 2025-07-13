<?php
// Selalu mulai session di awal
session_start();

// Cek apakah ada data yang dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    
    // Ambil ID produk dan jumlahnya
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Pastikan jumlahnya valid (minimal 1)
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Jika produk sudah ada di keranjang, tambahkan jumlahnya
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        // Jika belum ada, tambahkan produk baru ke keranjang
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Setelah selesai, arahkan pengguna ke halaman keranjang
header('Location: keranjang.php');
exit();
?>