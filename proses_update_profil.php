<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- PROSES UPDATE PROFIL (USERNAME & FOTO) ---
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $file = $_FILES['profile_picture'];

    // Update username
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $_SESSION['username'] = $username; // Update session username

    // Handle upload foto jika ada
    if ($file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('profile_', true) . '.' . $file_extension;
        
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
            // Hapus foto lama jika bukan default
            $stmt_old_pic = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
            $stmt_old_pic->bind_param("i", $user_id);
            $stmt_old_pic->execute();
            $old_pic = $stmt_old_pic->get_result()->fetch_assoc()['profile_picture'];
            if ($old_pic != 'default.png' && file_exists($upload_dir . $old_pic)) {
                unlink($upload_dir . $old_pic);
            }

            // Update database dengan nama file baru
            $stmt_update_pic = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt_update_pic->bind_param("si", $new_filename, $user_id);
            $stmt_update_pic->execute();
        }
    }
    $_SESSION['message'] = "Profil berhasil diperbarui!";
    header('Location: profil.php');
    exit();
}

// --- PROSES UBAH PASSWORD ---
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Konfirmasi password baru tidak cocok.";
        header('Location: profil.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($current_password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_new_password, $user_id);
        $stmt_update->execute();
        $_SESSION['message'] = "Password berhasil diubah.";
    } else {
        $_SESSION['error'] = "Password saat ini salah.";
    }
    header('Location: profil.php');
    exit();
}

// --- PROSES HAPUS AKUN ---
if (isset($_POST['delete_account'])) {
    $password_verification = $_POST['password_verification'];

    $stmt = $conn->prepare("SELECT password, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (password_verify($password_verification, $user['password'])) {
        // Hapus foto profil jika bukan default
        if ($user['profile_picture'] != 'default.png' && file_exists('uploads/profiles/' . $user['profile_picture'])) {
            unlink('uploads/profiles/' . $user['profile_picture']);
        }

        // Hapus user dari database
        $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_delete->bind_param("i", $user_id);
        $stmt_delete->execute();

        // Logout dan hancurkan session
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = "Password salah. Akun tidak dapat dihapus.";
        header('Location: profil.php');
        exit();
    }
}

// Jika tidak ada aksi yang cocok, kembali ke profil
header('Location: profil.php');
exit();
?>