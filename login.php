<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($action == 'register') {
        // Logika Registrasi
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Username sudah digunakan.";
        }
        $stmt->close();
    } elseif ($action == 'login') {
        // Logika Login
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                // --- PERUBAHAN DI SINI ---
                header("Location: index.php"); // Diarahkan ke halaman utama
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
        $stmt->close();
    }
}
// Koneksi ditutup setelah semua operasi selesai
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Registrasi - Classic Coffee 789</title>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body style="background: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">
    <div class="login-container">
        <div class="login-section">
            <h2>🌟 Registrasi Akun Baru</h2>
            <?php if($message): ?>
                <div class="message success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error && isset($_POST['action']) && $_POST['action'] == 'register'): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label for="reg-username">👤 Username</label>
                    <input type="text" id="reg-username" name="username" required placeholder="Masukkan username">
                </div>
                <div class="form-group">
                    <label for="reg-password">🔒 Password</label>
                    <input type="password" id="reg-password" name="password" required placeholder="Masukkan password">
                </div>
                <button type="submit" class="btn">✨ Daftar Sekarang</button>
            </form>
        </div>

        <div class="login-section">
            <h2>🔑 Login</h2>
            <?php if($error && isset($_POST['action']) && $_POST['action'] == 'login'): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="username">👤 Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username">
                </div>
                <div class="form-group">
                    <label for="password">🔒 Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>
                <button type="submit" class="btn">🚀 Masuk</button>
            </form>
        </div>

        <div class="back-link">
            <a href="index.php">🏠 Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>