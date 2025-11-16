<?php
require_once 'config.php'; // Ini memuat koneksi DB dan Kunci Google

// Cek apakah Google mengirim kode
if (isset($_GET['code'])) {
    
    try {
        // KODE BARU: Menukar kode dengan token (tanpa library)
        $token_url = 'https://oauth2.googleapis.com/token';
        $token_data = [
            'code' => $_GET['code'],
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URL,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token_response = curl_exec($ch);
        curl_close($ch);

        $token = json_decode($token_response, true);
        // AKHIR KODE BARU

        // Cek jika ada error
        if (isset($token['error'])) {
            // Gagal login, kembalikan ke halaman login
            header('Location: login.php?error=google_failed');
            exit();
        }
        
        // KODE BARU: Ambil data profil (tanpa library)
        $userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token['access_token']]);
        $userinfo_response = curl_exec($ch);
        curl_close($ch);
        
        $google_account_info = json_decode($userinfo_response, true);
        
        $google_id = $google_account_info['id'];
        $email = $google_account_info['email'];
        $name = $google_account_info['name'];
        // AKHIR KODE BARU
        
        // Sekarang, kita cek database kita
        
        // 1. Cek apakah pengguna sudah pernah login dengan Google ID ini
        $stmt_check_id = $conn->prepare("SELECT id, username FROM users WHERE google_id = ?");
        $stmt_check_id->bind_param("s", $google_id);
        $stmt_check_id->execute();
        $user_result = $stmt_check_id->get_result();
        
        if ($user_result->num_rows > 0) {
            // --- KASUS 1: PENGGUNA LAMA (SUDAH PERNAH LOGIN VIA GOOGLE) ---
            // Langsung loginkan
            $user = $user_result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
        } else {
            // Pengguna baru via Google, cek berdasarkan email
            $stmt_check_email = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt_check_email->bind_param("s", $email);
            $stmt_check_email->execute();
            $user_result_email = $stmt_check_email->get_result();
            
            if ($user_result_email->num_rows > 0) {
                // --- KASUS 2: PENGGUNA SUDAH DAFTAR MANUAL TAPI EMAIL SAMA ---
                // Kita "tautkan" akunnya ke Google ID
                $user = $user_result_email->fetch_assoc();
                $user_id = $user['id'];
                
                $stmt_link = $conn->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                $stmt_link->bind_param("si", $google_id, $user_id);
                $stmt_link->execute();
                
                // Loginkan
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $user['username'];
                
            } else {
                // --- KASUS 3: PENGGUNA BENAR-BENAR BARU ---
                // Buat akun baru untuk mereka
                
                // Gunakan nama dari Google sebagai username. Jika sudah ada, tambahkan angka acak
                $username = $name;
                $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_check_username->bind_param("s", $username);
                $stmt_check_username->execute();
                if ($stmt_check_username->get_result()->num_rows > 0) {
                    // Jika username sudah ada, gunakan bagian depan email
                    $username = explode('@', $email)[0] . rand(10, 99);
                }
                
                // Masukkan user baru ke database (tanpa password, tapi dengan google_id dan email)
                $stmt_insert = $conn->prepare("INSERT INTO users (username, email, google_id, profile_picture) VALUES (?, ?, ?, 'default.png')");
                $stmt_insert->bind_param("sss", $username, $email, $google_id);
                $stmt_insert->execute();
                
                $new_user_id = $conn->insert_id;
                
                // Loginkan
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['username'] = $username;
            }
        }
        
        // Setelah semua logika selesai, arahkan ke halaman utama
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        // Terjadi error
        die("Terjadi kesalahan: " . $e->getMessage());
    }
    
} else {
    // Jika tidak ada kode, kembalikan ke login
    header('Location: login.php');
    exit();
}
?>