<?php
session_start();

// HANCURKAN SESI LAMA SETIAP KALI HALAMAN LOGIN DIKUNJUNGI
// Ini akan memaksa login ulang jika alur dimulai dari halaman ini.
if (isset($_SESSION['admin_logged_in'])) {
    session_unset();
    session_destroy();
    // Mulai lagi session baru setelah dihancurkan untuk proses login yang baru
    session_start();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_user = 'admin';
    $admin_pass = 'kopi789'; // Ganti password ini jika perlu

    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        // Buat sesi baru setelah login berhasil
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin_user;
        header("Location: index.php");
        exit();
    } else {
        $error = 'Username atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
        .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        .login-box h2 { margin-top:0; margin-bottom: 20px; color: #5a3a22; }
        .login-box input { box-sizing: border-box; width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        .login-box button { width: 100%; padding: 10px; border: none; background-color: #5a3a22; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] == 'logout'): ?>
            <p style="color:green;">Anda berhasil logout.</p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>