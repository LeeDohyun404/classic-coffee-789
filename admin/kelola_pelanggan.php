<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Handle delete request
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Delete profile picture if not default
    $stmt_get_pic = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt_get_pic->bind_param("i", $id_to_delete);
    $stmt_get_pic->execute();
    $pic_data = $stmt_get_pic->get_result()->fetch_assoc();
    if ($pic_data && $pic_data['profile_picture'] !== 'default.png') {
        $file_path = '../uploads/profiles/' . $pic_data['profile_picture'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete user from database
    $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    
    header("Location: kelola_pelanggan.php?status=deleted");
    exit();
}

$users_result = $conn->query("SELECT id, username, profile_picture, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f0ef 100%); 
            padding: 20px; 
            min-height: 100vh; 
        }
        
        .container { 
            max-width: 1200px; 
            margin: auto; 
            background: white; 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #5a3a22;
        }
        
        h1 { 
            color: #5a3a22; 
            font-size: 2.2em; 
            font-weight: 700; 
        }
        
        .btn { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 25px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        
        .btn-dashboard { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
            color: white; 
        }
        
        .btn-dashboard:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(90, 58, 34, 0.4); 
        }
        
        .search-container {
            margin-bottom: 20px;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }
        
        .search-input:focus {
            outline: none;
            border-color: #5a3a22;
            box-shadow: 0 0 0 3px rgba(90, 58, 34, 0.1);
        }
        
        .stats-info { 
            background: linear-gradient(135deg, rgba(90, 58, 34, 0.1) 0%, rgba(139, 111, 71, 0.1) 100%); 
            padding: 15px 20px; 
            border-radius: 12px; 
            margin-bottom: 25px; 
            border-left: 4px solid #5a3a22; 
        }
        
        .stats-info p { 
            margin: 0; 
            color: #5a3a22; 
            font-weight: 600; 
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .user-table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            min-width: 700px;
        }
        
        .user-table thead { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
        }
        
        .user-table th { 
            padding: 18px 15px; 
            color: white; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            font-size: 13px; 
            text-align: center;
        }
        
        .user-table td { 
            padding: 18px 15px; 
            border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle; 
            text-align: center;
        }
        
        .user-table tbody tr { 
            transition: all 0.3s ease; 
        }
        
        .user-table tbody tr:hover { 
            background: linear-gradient(135deg, rgba(90, 58, 34, 0.05) 0%, rgba(139, 111, 71, 0.05) 100%);
            transform: translateY(-1px); 
        }
        
        .user-table img { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 50%; 
            border: 3px solid #5a3a22;
            transition: all 0.3s ease; 
            cursor: pointer;
        }
        
        .user-table img:hover { 
            transform: scale(1.1); 
            box-shadow: 0 8px 20px rgba(90, 58, 34, 0.3);
        }
        
        .username-cell { 
            font-weight: 600; 
            color: #5a3a22; 
            font-size: 15px; 
        }
        
        .date-cell {
            color: #6b7280;
            font-weight: 500;
        }
        
        .action-buttons { 
            display: flex; 
            gap: 8px; 
            justify-content: center;
        }
        
        .action-buttons a { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            font-size: 14px; 
        }
        
        .btn-edit { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
        }
        
        .btn-edit:hover { 
            transform: translateY(-2px) scale(1.05); 
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4); 
        }
        
        .btn-delete { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); 
            color: white; 
        }
        
        .btn-delete:hover { 
            transform: translateY(-2px) scale(1.05); 
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4); 
        }
        
        .empty-state { 
            text-align: center; 
            padding: 60px 20px; 
            color: #6b7280; 
        }
        
        .empty-state i { 
            font-size: 64px; 
            margin-bottom: 20px; 
            color: #d1d5db;
        }
        
        .empty-state h3 {
            margin-bottom: 8px;
            color: #4b5563;
            font-size: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn-cancel {
            padding: 10px 20px;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #f9fafb;
        }
        
        .btn-confirm {
            padding: 10px 20px;
            border: none;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        
        .status-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            z-index: 1001;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            h1 {
                font-size: 1.8em;
            }
            
            .user-table th,
            .user-table td {
                padding: 12px 8px;
                font-size: 13px;
            }
            
            .user-table img {
                width: 45px;
                height: 45px;
            }
            
            .action-buttons a {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .container {
            animation: slideIn 0.6s ease-out;
        }
        
        .user-table tbody tr {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Kelola Pelanggan</h1>
            <a href="index.php" class="btn btn-dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
        
        <?php 
        $total_users = $users_result ? $users_result->num_rows : 0;
        if ($total_users > 0): 
        ?>
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="🔍 Cari pelanggan berdasarkan username...">
        </div>
        
        <div class="stats-info">
            <p id="statsText"><i class="fas fa-info-circle"></i> Total Pelanggan: <strong><?php echo $total_users; ?></strong></p>
        </div>
        <?php endif; ?>
        
        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-image"></i> Foto</th>
                        <th><i class="fas fa-user"></i> Username</th>
                        <th><i class="fas fa-calendar"></i> Bergabung</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php if ($total_users > 0): ?>
                        <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr class="user-row">
                            <td>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                     alt="<?php echo htmlspecialchars($user['username']); ?>"
                                     onclick="showImage(this.src)">
                            </td>
                            <td class="username-cell">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td class="date-cell">
                                <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_pelanggan.php?id=<?php echo $user['id']; ?>" 
                                       class="btn-edit" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn-delete" 
                                       title="Hapus" 
                                       onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h3>Belum Ada Pelanggan</h3>
                                    <p>Tidak ada pelanggan yang terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div style="position: relative; max-width: 90%; max-height: 90%;">
            <img id="modalImage" style="max-width: 100%; max-height: 100%; border-radius: 10px;">
            <button onclick="closeModal('imageModal')" 
                    style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: white; 
                           border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">&times;</button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 48px; margin-bottom: 15px;"></i>
            <h3 style="margin-bottom: 10px; color: #374151;">Konfirmasi Hapus</h3>
            <p id="deleteMessage" style="color: #6b7280; margin-bottom: 20px;"></p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Batal</button>
                <button class="btn-confirm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const userRows = document.querySelectorAll('.user-row');
        const statsText = document.getElementById('statsText');
        const totalUsers = <?php echo $total_users; ?>;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                userRows.forEach(row => {
                    const username = row.querySelector('.username-cell').textContent.toLowerCase();
                    if (username.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (statsText) {
                    if (searchTerm) {
                        statsText.innerHTML = `<i class="fas fa-search"></i> Menampilkan <strong>${visibleCount}</strong> dari <strong>${totalUsers}</strong> pelanggan`;
                    } else {
                        statsText.innerHTML = `<i class="fas fa-info-circle"></i> Total Pelanggan: <strong>${totalUsers}</strong>`;
                    }
                }
            });
        }

        // Image preview
        function showImage(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').style.display = 'flex';
        }

        // Delete confirmation
        function confirmDelete(userId, username) {
            document.getElementById('deleteMessage').innerHTML = 
                `Yakin ingin menghapus akun <strong>${username}</strong>?<br><small style="color: #ef4444;">Tindakan ini tidak dapat dibatalkan!</small>`;
            document.getElementById('confirmDeleteBtn').onclick = () => executeDelete(userId);
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function executeDelete(userId) {
            window.location.href = `kelola_pelanggan.php?delete=${userId}`;
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        });

        // Show success message
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'deleted') {
            const statusDiv = document.createElement('div');
            statusDiv.className = 'status-message';
            statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Pelanggan berhasil dihapus!';
            document.body.appendChild(statusDiv);
            
            setTimeout(() => statusDiv.remove(), 4000);
            
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('imageModal');
                closeModal('deleteModal');
            }
        });

        console.log('✅ Kelola Pelanggan loaded successfully!');
    </script>
</body>
</html>