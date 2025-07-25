<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: kelola_pelanggan.php");
    exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    // Update username
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();

    // Update password jika diisi
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_pass->bind_param("si", $hashed_password, $user_id);
        $stmt_pass->execute();
    }
    
    // Handle ganti foto
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profiles/';
        
        // Hapus foto lama jika ada
        if (!empty($user['profile_picture']) && file_exists($upload_dir . $user['profile_picture'])) {
            unlink($upload_dir . $user['profile_picture']);
        }
        
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $new_filename)) {
            $stmt_photo = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt_photo->bind_param("si", $new_filename, $user_id);
            $stmt_photo->execute();
        }
    }

    header("Location: kelola_pelanggan.php");
    exit();
}

// Ambil data user
$stmt_user = $conn->prepare("SELECT id, username, profile_picture FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

if (!$user) {
    header("Location: kelola_pelanggan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            max-width: 500px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.05) 10px,
                rgba(255,255,255,0.05) 20px
            );
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateX(-50px) translateY(-50px); }
            100% { transform: translateX(50px) translateY(50px); }
        }

        .header h1 {
            font-size: 1.8em;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .header .subtitle {
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .profile-preview {
            text-align: center;
            padding: 30px;
            background: #f8f9fc;
            position: relative;
        }

        .profile-img-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }

        .profile-img-container:hover .img-overlay {
            opacity: 1;
        }

        .img-overlay i {
            color: white;
            font-size: 24px;
        }

        .form-content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            transition: color 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #5a3a22;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(90, 58, 34, 0.1);
        }

        .form-group input:focus + label,
        .form-group.focused label {
            color: #5a3a22;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #5a3a22;
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
        }

        .file-input-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .file-input-btn input {
            position: absolute;
            left: -9999px;
            opacity: 0;
        }

        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(90, 58, 34, 0.3);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #495057;
        }

        .btn-secondary:hover {
            background: #dee2e6;
            transform: translateY(-2px);
        }

        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .success-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .strength-meter {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
            width: 0%;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #fd7e14; width: 50%; }
        .strength-good { background: #ffc107; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }

        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.5em;
            }

            .profile-preview {
                padding: 20px;
            }

            .profile-img {
                width: 100px;
                height: 100px;
            }

            .form-content {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Edit Pelanggan</h1>
            <div class="subtitle">Kelola informasi pelanggan</div>
        </div>

        <div class="profile-preview">
            <div class="profile-img-container">
                <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                     alt="Profile Picture" 
                     class="profile-img" 
                     id="profilePreview"
                     onerror="this.src='../images/default-avatar.png'">
                <div class="img-overlay" onclick="document.getElementById('profile_picture').click()">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
        </div>

        <div class="form-content">
            <form action="edit_pelanggan.php?id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data" id="editForm">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" 
                           name="username" 
                           id="username"
                           value="<?php echo htmlspecialchars($user['username']); ?>" 
                           required
                           minlength="3"
                           maxlength="50">
                </div>

                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                    <div class="password-container">
                        <input type="password" 
                               name="new_password" 
                               id="new_password"
                               placeholder="Kosongkan jika tidak ingin diubah">
                        <i class="fas fa-eye password-toggle" id="passwordToggle"></i>
                    </div>
                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Ganti Foto Profil</label>
                    <div class="file-input-container">
                        <label class="file-input-btn">
                            <i class="fas fa-upload"></i>
                            <span id="fileButtonText">Pilih Foto Baru</span>
                            <input type="file" 
                                   name="profile_picture" 
                                   id="profile_picture"
                                   accept="image/*">
                        </label>
                        <div class="file-name" id="fileName"></div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        <span>Update Pelanggan</span>
                    </button>
                    <a href="kelola_pelanggan.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password toggle visibility
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('new_password');

        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            if (password.length === 0) {
                strengthFill.className = 'strength-fill';
                strengthText.textContent = '';
                return;
            }

            let strength = 0;
            let strengthLabel = '';

            // Check password strength
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthFill.className = 'strength-fill strength-weak';
                    strengthText.textContent = 'Lemah';
                    strengthText.style.color = '#dc3545';
                    break;
                case 2:
                    strengthFill.className = 'strength-fill strength-fair';
                    strengthText.textContent = 'Cukup';
                    strengthText.style.color = '#fd7e14';
                    break;
                case 3:
                case 4:
                    strengthFill.className = 'strength-fill strength-good';
                    strengthText.textContent = 'Baik';
                    strengthText.style.color = '#ffc107';
                    break;
                case 5:
                    strengthFill.className = 'strength-fill strength-strong';
                    strengthText.textContent = 'Kuat';
                    strengthText.style.color = '#28a745';
                    break;
            }
        });

        // File input handler
        const fileInput = document.getElementById('profile_picture');
        const fileName = document.getElementById('fileName');
        const fileButtonText = document.getElementById('fileButtonText');
        const profilePreview = document.getElementById('profilePreview');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileButtonText.textContent = 'Foto Terpilih';

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = '';
                fileButtonText.textContent = 'Pilih Foto Baru';
            }
        });

        // Form animation on focus
        const formInputs = document.querySelectorAll('.form-group input');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Form submission with loading state
        const form = document.getElementById('editForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            
            if (username.length < 3) {
                e.preventDefault();
                alert('Username minimal 3 karakter!');
                return;
            }

            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'Memproses...';
        });

        // Smooth scroll to top on load
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Add floating animation to profile image
        let isFloating = true;
        setInterval(() => {
            if (isFloating) {
                profilePreview.style.transform = 'translateY(-5px)';
            } else {
                profilePreview.style.transform = 'translateY(0px)';
            }
            isFloating = !isFloating;
        }, 2000);

        // Username validation
        const usernameInput = document.getElementById('username');
        usernameInput.addEventListener('input', function() {
            const value = this.value;
            const isValid = /^[a-zA-Z0-9_]+$/.test(value) && value.length >= 3;
            
            if (value.length > 0 && !isValid) {
                this.style.borderColor = '#dc3545';
                this.style.background = '#ffe6e6';
            } else {
                this.style.borderColor = '#e1e5e9';
                this.style.background = '#f8f9fc';
            }
        });

        // Konfirmasi sebelum meninggalkan halaman jika ada perubahan
        let formChanged = false;
        const originalValues = {
            username: usernameInput.value,
            password: '',
            file: ''
        };

        form.addEventListener('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });
    </script>
</body>
</html>