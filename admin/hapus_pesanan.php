<?php
require_once '../config.php';

// Idealnya, tambahkan pengecekan login admin di sini

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman dashboard admin
        header("Location: index.php?status=deleted");
        exit();
    } else {
        // Jika gagal
        header("Location: index.php?status=error");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Jika tidak ada ID, kembali ke dashboard
    header("Location: index.php");
    exit();
}
?>