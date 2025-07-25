<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Proses hapus pelanggan
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    header("Location: kelola_pelanggan.php");
    exit();
}

$users = $conn->query("SELECT id, username, profile_picture FROM users ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pelanggan - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f0ef 100%); 
            padding: 20px; 
            margin: 0;
            min-height: 100vh;
        }
        
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        h1 { 
            color: #5a3a22; 
            margin: 0;
            font-size: 2.2em;
            font-weight: 700;
        }
        
        /* Styling button dashboard yang dipercantik */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .btn-dashboard {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }
        
        .btn-dashboard:hover {
            background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
        }
        
        .btn-dashboard:active {
            transform: translateY(0);
        }
        
        /* Stats info */
        .stats-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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
        
        .user-table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
        }
        
        .user-table td { 
            padding: 15px; 
            border-bottom: 1px solid #f0f0f0; 
            vertical-align: middle;
        }
        
        .user-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .user-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }
        
        .user-table img { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 50%; 
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .user-table img:hover {
            border-color: #5a3a22;
            transform: scale(1.1);
        }
        
        .username-cell {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-buttons a { 
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        
        .action-buttons .btn-edit {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .action-buttons .btn-edit:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .action-buttons .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .action-buttons .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .empty-state h3 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        
        .empty-state p {
            margin: 0;
            font-size: 14px;
        }
        
        /* Responsive */
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
            
            .user-table {
                font-size: 14px;
            }
            
            .user-table th,
            .user-table td {
                padding: 10px 8px;
            }
            
            .user-table img {
                width: 45px;
                height: 45px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-buttons a {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }
        
        /* Animation for page load */
        .container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .user-table tbody tr {
            animation: fadeInUp 0.4s ease-out backwards;
        }
        
        .user-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .user-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .user-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .user-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .user-table tbody tr:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Daftar Pelanggan</h1>
            <a href="index.php" class="btn btn-dashboard">
                <i class="fas fa-tachometer-alt"></i>
                Kembali ke Dashboard
            </a>
        </div>
        
        <?php 
        $total_users = $users->num_rows;
        if ($total_users > 0): 
        ?>
        <div class="stats-info">
            <p><i class="fas fa-info-circle"></i> Total Pelanggan Terdaftar: <strong><?php echo $total_users; ?></strong></p>
        </div>
        <?php endif; ?>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th><i class="fas fa-image"></i> Foto Profil</th>
                    <th><i class="fas fa-user"></i> Username</th>
                    <th><i class="fas fa-cogs"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_users > 0): ?>
                    <?php 
                    // Reset pointer hasil query
                    $users->data_seek(0);
                    while($user = $users->fetch_assoc()): 
                    ?>
                    <tr>
                        <td>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                 alt="Foto <?php echo htmlspecialchars($user['username']); ?>"
                                 onerror="this.src='../images/default-avatar.png'">
                        </td>
                        <td class="username-cell">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit_pelanggan.php?id=<?php echo $user['id']; ?>" 
                                   class="btn-edit" 
                                   title="Edit/Lihat Detail">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="kelola_pelanggan.php?delete=<?php echo $user['id']; ?>" 
                                   class="btn-delete" 
                                   title="Hapus Pelanggan" 
                                   onclick="return confirm('⚠️ Yakin ingin menghapus akun pelanggan <?php echo htmlspecialchars($user['username']); ?>?\n\nTindakan ini tidak dapat dibatalkan!');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h3>Belum Ada Pelanggan</h3>
                                <p>Saat ini tidak ada pelanggan yang terdaftar di sistem.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>