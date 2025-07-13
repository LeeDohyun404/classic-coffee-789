<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['code'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$voucher_code = $_GET['code'];

// Cek apakah voucher valid dan milik user
$sql = "SELECT id FROM vouchers WHERE user_id = ? AND voucher_code = ? AND status = 'tersedia' AND expires_at >= CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $voucher_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Jika valid, simpan kode voucher ke sesi dan arahkan ke keranjang
    $_SESSION['applied_voucher'] = $voucher_code;
}

header('Location: keranjang.php');
exit();
?>