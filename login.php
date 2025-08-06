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
                header("Location: index.php");
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
// Koneksi tidak ditutup di sini agar bisa digunakan oleh header/footer jika ada
// if (isset($conn)) {
//     $conn->close();
// }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Registrasi - Classic Coffee 789</title>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
   <style>
    .login-body {
        background: #f4f7f6; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        min-height: 100vh; 
        padding: 20px;
        margin: 0;
    }
    .login-container {
        width: 100%;
        max-width: 450px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        margin: 20px auto;
        border: 1px solid #ddd;
    }
    .login-section {
        padding: 30px;
        border-bottom: 1px solid #eee;
    }
    .login-section:last-of-type {
        border-bottom: none;
    }
    .login-section h2 {
        color: #5a3a22;
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.5em;
        text-align: center;
    }
    .message {
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 8px;
        text-align: center;
    }
    .message.success {
        background-color: #d4edda;
        color: #155724;
    }
    .message.error {
        background-color: #f8d7da;
        color: #721c24;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box; /* Crucial Fix */
    }
    .btn {
        padding: 12px 20px;
        border: none;
        background: #8B4513;
        color: white;
        cursor: pointer;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        display: block;
        width: 100%;
        font-weight: bold;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .btn:hover {
        background: #A0522D;
    }
    .back-link {
        text-align: center;
        padding: 20px;
        background-color: #f8f9fa;
    }
    .back-link a {
        color: #5a3a22;
        text-decoration: none;
        font-weight: 500;
    }
   </style>
</head>
<body class="login-body">
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