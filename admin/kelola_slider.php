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
    <title>Kelola Slider - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .form-container, .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.07); margin-bottom: 30px; }
        h1, h2 { color: #5a3a22; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; transition: all 0.3s ease; }
        .btn-primary { background-color: #5a3a22; }
        .btn-primary:hover { background-color: #4a2f1d; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .slide-table { width: 100%; border-collapse: collapse; }
        .slide-table th, .slide-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        .slide-table img { width: 120px; height: 60px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-controls">
            <h1><i class="fas fa-images"></i> Kelola Slider</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <div class="form-container">
            <h2><?php echo $slide_to_edit ? 'Edit Slide' : 'Tambah Slide Baru'; ?></h2>
            <form action="kelola_slider.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="slide_id" value="<?php echo $slide_to_edit['id'] ?? ''; ?>">
                <input type="hidden" name="current_image" value="<?php echo $slide_to_edit['image_url'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="title">Judul (Title)</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($slide_to_edit['title'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="subtitle">Subjudul</label>
                    <textarea name="subtitle" rows="3"><?php echo htmlspecialchars($slide_to_edit['subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Gambar Slide (Rasio 16:9 direkomendasikan)</label>
                    <input type="file" name="image" accept="image/*" <?php echo $slide_to_edit ? '' : 'required'; ?>>
                    <?php if ($slide_to_edit && !empty($slide_to_edit['image_url'])): ?>
                        <p style="margin-top:10px;">Gambar saat ini: <img src="../images/<?php echo $slide_to_edit['image_url']; ?>" height="50"></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="sort_order">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="<?php echo htmlspecialchars($slide_to_edit['sort_order'] ?? '0'); ?>" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" <?php echo ($slide_to_edit['is_active'] ?? 1) ? 'checked' : ''; ?>> Aktifkan Slide
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $slide_to_edit ? 'Update Slide' : 'Tambah Slide'; ?></button>
                <?php if ($slide_to_edit): ?>
                    <a href="kelola_slider.php" class="btn btn-secondary">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="table-container">
            <h2>Daftar Slide</h2>
            <table class="slide-table">
                <thead>
                    <tr><th>Urutan</th><th>Gambar</th><th>Judul</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                     <?php while($slide = $slides->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $slide['sort_order']; ?></td>
                        <td><img src="../images/<?php echo htmlspecialchars($slide['image_url']); ?>" alt="Slide"></td>
                        <td><?php echo htmlspecialchars($slide['title']); ?></td>
                        <td><?php echo $slide['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?></td>
                        <td>
                            <a href="kelola_slider.php?edit=<?php echo $slide['id']; ?>"><i class="fas fa-edit"></i></a>
                            <a href="kelola_slider.php?delete=<?php echo $slide['id']; ?>" onclick="return confirm('Yakin ingin menghapus slide ini?');"><i class="fas fa-trash" style="color:red;"></i></a>
                        </td>
                    </tr>
                     <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>