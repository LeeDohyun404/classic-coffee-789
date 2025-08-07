<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
$message = '';
$error = '';

// --- PROSES FORM (TAMBAH/EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slide_id = $_POST['slide_id'] ?? null;
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Logika Upload Gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $upload_dir = '../images/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($file['type'], $allowed_types)) {
            // Hapus gambar lama jika ini adalah proses edit
            if ($slide_id) {
                $stmt_old = $conn->prepare("SELECT image_url FROM hero_slides WHERE id = ?");
                $stmt_old->bind_param("i", $slide_id);
                $stmt_old->execute();
                $old_image = $stmt_old->get_result()->fetch_assoc()['image_url'];
                if ($old_image && $old_image !== 'background.jpg' && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'slide_' . uniqid() . '.' . $file_extension;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $image_url = $new_filename;
            }
        } else {
            $error = "Tipe file tidak valid.";
        }
    } else {
        $image_url = $_POST['current_image'] ?? '';
    }

    if (empty($error)) {
        if ($slide_id) { // Update
            $stmt = $conn->prepare("UPDATE hero_slides SET title=?, subtitle=?, image_url=?, is_active=?, sort_order=? WHERE id=?");
            $stmt->bind_param("sssiii", $title, $subtitle, $image_url, $is_active, $sort_order, $slide_id);
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO hero_slides (title, subtitle, image_url, is_active, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssii", $title, $subtitle, $image_url, $is_active, $sort_order);
        }
        
        if($stmt->execute()){
            header("Location: kelola_slider.php?status=success");
            exit();
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}

// --- PROSES HAPUS ---
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $stmt_img = $conn->prepare("SELECT image_url FROM hero_slides WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $image_to_delete = $stmt_img->get_result()->fetch_assoc()['image_url'];
    if ($image_to_delete && $image_to_delete !== 'background.jpg' && file_exists('../images/' . $image_to_delete)) {
        unlink('../images/' . $image_to_delete);
    }
    $stmt = $conn->prepare("DELETE FROM hero_slides WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    header("Location: kelola_slider.php?status=deleted");
    exit();
}

// Ambil data untuk ditampilkan atau diedit
$slides = $conn->query("SELECT * FROM hero_slides ORDER BY sort_order ASC");
$slide_to_edit = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM hero_slides WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $slide_to_edit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Slider - Admin</title>
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
        }
        
        .header-controls { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .form-container, .table-container { 
            background: white; 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            margin-bottom: 30px; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-container:hover, .table-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        h1, h2 { 
            color: #5a3a22; 
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .form-group { 
            margin-bottom: 20px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #333;
            font-size: 0.95rem;
        }
        
        .form-group input, .form-group textarea { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #e1e5e9; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #5a3a22;
            background: white;
            box-shadow: 0 0 0 3px rgba(90, 58, 34, 0.1);
        }
        
        .btn { 
            padding: 12px 24px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            color: white; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
            box-shadow: 0 4px 15px rgba(90, 58, 34, 0.3);
        }
        
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(90, 58, 34, 0.4);
        }
        
        .btn-secondary { 
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); 
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .slide-table { 
            width: 100%; 
            border-collapse: collapse; 
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .slide-table th { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
            padding: 15px 12px; 
            text-align: left; 
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .slide-table td { 
            padding: 15px 12px; 
            border-bottom: 1px solid #f0f0f0; 
            text-align: left; 
            vertical-align: middle; 
            transition: background-color 0.3s ease;
        }
        
        .slide-table tr:hover td {
            background-color: #f8f9fa;
        }
        
        .slide-table img { 
            width: 120px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .slide-table img:hover {
            transform: scale(1.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
        }
        
        .action-btn.edit {
            background: #17a2b8;
            color: white;
        }
        
        .action-btn.edit:hover {
            background: #138496;
            transform: scale(1.1);
        }
        
        .action-btn.delete {
            background: #dc3545;
            color: white;
        }
        
        .action-btn.delete:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .image-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }
        
        .image-preview img {
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 25px;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .notification.error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                max-width: 100%;
            }
            
            .header-controls {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            h1 {
                font-size: 1.8rem;
                margin-bottom: 15px;
            }
            
            .form-container, .table-container {
                padding: 20px;
                border-radius: 15px;
            }
            
            .form-buttons {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
                width: 100%;
            }
            
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .slide-table {
                min-width: 600px;
            }
            
            .slide-table th,
            .slide-table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            
            .slide-table img {
                width: 80px;
                height: 40px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-btn {
                min-width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }
            
            h2 {
                font-size: 1.2rem;
            }
            
            .form-container, .table-container {
                padding: 15px;
            }
            
            .slide-table th,
            .slide-table td {
                padding: 8px 6px;
                font-size: 0.8rem;
            }
            
            .slide-table img {
                width: 60px;
                height: 30px;
            }
            
            .notification {
                right: 10px;
                left: 10px;
                transform: translateY(-100px);
            }
            
            .notification.show {
                transform: translateY(0);
            }
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-controls">
            <h1><i class="fas fa-images"></i> Kelola Slider</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <div class="form-container fade-in">
            <h2><i class="fas fa-<?php echo $slide_to_edit ? 'edit' : 'plus-circle'; ?>"></i> <?php echo $slide_to_edit ? 'Edit Slide' : 'Tambah Slide Baru'; ?></h2>
            <form id="slideForm" action="kelola_slider.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="slide_id" value="<?php echo $slide_to_edit['id'] ?? ''; ?>">
                <input type="hidden" name="current_image" value="<?php echo $slide_to_edit['image_url'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Judul (Title)</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($slide_to_edit['title'] ?? ''); ?>" placeholder="Masukkan judul slide..." required>
                </div>
                
                <div class="form-group">
                    <label for="subtitle"><i class="fas fa-align-left"></i> Subjudul</label>
                    <textarea id="subtitle" name="subtitle" rows="3" placeholder="Masukkan subjudul atau deskripsi slide..."><?php echo htmlspecialchars($slide_to_edit['subtitle'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Gambar Slide (Rasio 16:9 direkomendasikan)</label>
                    <input type="file" id="image" name="image" accept="image/*" <?php echo $slide_to_edit ? '' : 'required'; ?>>
                    <div id="imagePreview" class="image-preview" style="display: none;">
                        <p><i class="fas fa-eye"></i> Preview Gambar:</p>
                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; height: auto;">
                    </div>
                    <?php if ($slide_to_edit && !empty($slide_to_edit['image_url'])): ?>
                        <div class="image-preview">
                            <p><i class="fas fa-image"></i> Gambar saat ini:</p>
                            <img src="../images/<?php echo $slide_to_edit['image_url']; ?>" alt="Current Image" style="max-width: 200px; height: auto;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="sort_order"><i class="fas fa-sort-numeric-up"></i> Urutan Tampil</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($slide_to_edit['sort_order'] ?? '0'); ?>" min="0" placeholder="0" required>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo ($slide_to_edit['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <i class="fas fa-toggle-on"></i> Aktifkan Slide
                    </label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-<?php echo $slide_to_edit ? 'save' : 'plus'; ?>"></i>
                        <?php echo $slide_to_edit ? 'Update Slide' : 'Tambah Slide'; ?>
                    </button>
                    <?php if ($slide_to_edit): ?>
                        <a href="kelola_slider.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="table-container slide-in">
            <h2><i class="fas fa-list"></i> Daftar Slide</h2>
            <table class="slide-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-sort-numeric-up"></i> Urutan</th>
                        <th><i class="fas fa-image"></i> Gambar</th>
                        <th><i class="fas fa-heading"></i> Judul</th>
                        <th><i class="fas fa-toggle-on"></i> Status</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     <?php while($slide = $slides->fetch_assoc()): ?>
                    <tr class="slide-row" data-id="<?php echo $slide['id']; ?>">
                        <td><span class="sort-badge"><?php echo $slide['sort_order']; ?></span></td>
                        <td>
                            <img src="../images/<?php echo htmlspecialchars($slide['image_url']); ?>" 
                                 alt="Slide" 
                                 onclick="openImageModal(this.src)"
                                 style="cursor: pointer;">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($slide['title']); ?></strong>
                            <?php if (!empty($slide['subtitle'])): ?>
                                <br><small style="color: #666;"><?php echo htmlspecialchars(substr($slide['subtitle'], 0, 50)) . (strlen($slide['subtitle']) > 50 ? '...' : ''); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $slide['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $slide['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="kelola_slider.php?edit=<?php echo $slide['id']; ?>" 
                                   class="action-btn edit" 
                                   title="Edit Slide">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" 
                                   class="action-btn delete" 
                                   onclick="confirmDelete(<?php echo $slide['id']; ?>, '<?php echo htmlspecialchars($slide['title']); ?>')"
                                   title="Hapus Slide">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                     <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" src="" alt="Full Size Image">
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification" class="notification"></div>

    <script>
        // Image Preview Functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    preview.classList.add('fade-in');
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Form Submission with Loading State
        document.getElementById('slideForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const form = this;
            
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Validate form
            const title = document.getElementById('title').value.trim();
            const sortOrder = document.getElementById('sort_order').value;
            
            if (!title) {
                e.preventDefault();
                showNotification('Judul slide harus diisi!', 'error');
                resetSubmitButton();
                return;
            }
            
            if (sortOrder < 0) {
                e.preventDefault();
                showNotification('Urutan tampil tidak boleh negatif!', 'error');
                resetSubmitButton();
                return;
            }
        });

        function resetSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const isEdit = <?php echo $slide_to_edit ? 'true' : 'false'; ?>;
            
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = isEdit ? 
                '<i class="fas fa-save"></i> Update Slide' : 
                '<i class="fas fa-plus"></i> Tambah Slide';
            submitBtn.disabled = false;
        }

        // Enhanced Delete Confirmation
        function confirmDelete(id, title) {
            if (confirm(`Apakah Anda yakin ingin menghapus slide "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
                // Add loading effect to the row
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                }
                
                // Redirect to delete
                window.location.href = `kelola_slider.php?delete=${id}`;
            }
        }

        // Image Modal Functionality
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            modal.style.display = 'flex';
            modalImg.src = src;
            
            // Add fade-in animation
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.opacity = '0';
            
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Notification System
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Check for status messages
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            
            if (status === 'success') {
                showNotification('Slide berhasil disimpan!', 'success');
            } else if (status === 'deleted') {
                showNotification('Slide berhasil dihapus!', 'success');
            }
            
            // Add entrance animations
            const containers = document.querySelectorAll('.form-container, .table-container');
            containers.forEach((container, index) => {
                setTimeout(() => {
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Mobile Touch Enhancements
        if ('ontouchstart' in window) {
            // Add touch feedback for buttons
            document.querySelectorAll('.btn, .action-btn').forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                
                btn.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 100);
                });
            });
        }

        // Keyboard Navigation
        document.addEventListener('keydown', function(e) {
            // ESC to close modal
            if (e.key === 'Escape') {
                closeImageModal();
            }
            
            // Ctrl+S to save form
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('slideForm').submit();
            }
        });

        // Auto-resize textarea
        const textarea = document.getElementById('subtitle');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // Form field animations
        document.querySelectorAll('.form-group input, .form-group textarea').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            field.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }

        .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #ccc;
        }

        .sort-badge {
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .form-group.focused label {
            color: #5a3a22;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .checkbox-label {
            display: flex !important;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .checkbox-label:hover {
            background-color: #f8f9fa;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto !important;
            margin: 0;
        }

        /* Loading Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        /* Enhanced Mobile Styles */
        @media (max-width: 768px) {
            .modal-content {
                max-width: 95%;
                max-height: 80%;
            }
            
            .close {
                top: -30px;
                font-size: 25px;
            }
        }
    </style>
</body>
</html>