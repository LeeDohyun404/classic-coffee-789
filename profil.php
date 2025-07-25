<?php
include 'header.php';

// Pastikan hanya user yang sudah login yang bisa akses
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek notifikasi dari proses update
$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['message'], $_SESSION['error']);


// Ambil Riwayat Pembelian Pengguna
$history_stmt = $conn->prepare("SELECT id, order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$purchase_history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<title>Profil Saya - Classic Coffee 789</title>
<style>
    .profile-container { max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
    .profile-header { text-align: center; margin-bottom: 30px; }
    .profile-picture { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; border: 5px solid #fff7ed; }
    .profile-section { margin-top: 30px; border-top: 1px solid #eee; padding-top: 30px; }
    .form-group label { font-weight: 600; }
    .btn-danger { background-color: #dc3545; }
    .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #fff; }
    .success { background-color: #28a745; }
    .error { background-color: #dc3545; }

    /* STYLE UNTUK TABEL RIWAYAT */
    .history-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .history-table th, .history-table td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; }
    .history-table th { background-color: #f8f9fa; font-weight: 600; }
    .status-badge { padding: 5px 10px; border-radius: 15px; color: white; font-size: 12px; font-weight: bold; text-transform: capitalize; }
    .status-pending { background-color: #f39c12; }
    .status-paid { background-color: #28a745; }
    .status-free { background-color: #6c757d; } /* Tambahan Warna Abu-abu */
</style>

<div class="profile-container">
    <div class="profile-header">
        <img src="uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Foto Profil" class="profile-picture">
        <h1>Profil Saya</h1>
        <p>Kelola informasi akun Anda di sini.</p>
    </div>

    <?php if ($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

    <div class="profile-section">
        <h3>Ubah Informasi Profil</h3>
        <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Ganti Foto Profil (Opsional)</label>
                <input type="file" class="form-control" name="profile_picture" accept="image/png, image/jpeg">
            </div>
            <button type="submit" name="update_profile" class="btn">Simpan Perubahan</button>
        </form>
    </div>

    <div class="profile-section">
        <h3>Ubah Password</h3>
        <form action="proses_update_profil.php" method="POST">
            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn">Ubah Password</button>
        </form>
    </div>

    <div class="profile-section">
        <h3>Riwayat Pembelian</h3>
        <?php if (!empty($purchase_history)): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchase_history as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                            <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                            <td>
                                <?php
                                    // PERBAIKAN LOGIKA WARNA STATUS
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'paid':
                                            $status_class = 'status-paid';
                                            break;
                                        case 'free':
                                            $status_class = 'status-free';
                                            break;
                                        default: // pending
                                            $status_class = 'status-pending';
                                            break;
                                    }
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Anda belum memiliki riwayat pembelian.</p>
        <?php endif; ?>
    </div>

    <div class="profile-section">
        <h3>Hapus Akun</h3>
        <p>Tindakan ini tidak dapat diurungkan. Semua data Anda akan dihapus secara permanen.</p>
        <form action="proses_update_profil.php" method="POST" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin menghapus akun Anda secara permanen?');">
            <div class="form-group">
                <label for="password_verification">Masukkan Password untuk Konfirmasi</label>
                <input type="password" class="form-control" name="password_verification" required>
            </div>
            <button type="submit" name="delete_account" class="btn btn-danger">Hapus Akun Saya</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>