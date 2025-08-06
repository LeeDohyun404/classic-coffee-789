<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// --- PROSES TAMBAH/UPDATE PRODUK (LOGIKA BARU & DIPERBAIKI) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $discount_percentage = (int)($_POST['discount_percentage'] ?? 0);
    $product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : null;
    $image_url = $_POST['current_image'] ?? '';

    $discount_type = $_POST['discount_type'] ?? 'metode';
    $discount_methods_db = null;
    $discount_name_db = null;

    if ($discount_type === 'metode') {
        $discount_methods_arr = $_POST['discount_methods'] ?? [];
        $discount_methods_db = !empty($discount_methods_arr) ? implode(',', $discount_methods_arr) : null;
    } else { 
        $discount_methods_db = 'pickup,delivery,cod';
        $discount_name_db = trim($_POST['discount_name']);
        if (empty($discount_name_db)) {
            $discount_name_db = 'Diskon Spesial';
        }
    }
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        if ($product_id && !empty($image_url) && file_exists($upload_dir . $image_url)) {
            unlink($upload_dir . $image_url);
        }
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'prod_' . uniqid() . '.' . $file_extension;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
            $image_url = $new_filename;
        }
    }
    
    if ($product_id) { // Proses Update
        // ================== PERBAIKAN PADA bind_param ==================
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, discount_percentage=?, image_url=?, discount_methods=?, discount_name=? WHERE id=?");
        $stmt->bind_param("ssdisssi", $name, $category, $price, $discount_percentage, $image_url, $discount_methods_db, $discount_name_db, $product_id);
    } else { // Proses Insert
        // ================== PERBAIKAN PADA bind_param ==================
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, discount_percentage, image_url, discount_methods, discount_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdisss", $name, $category, $price, $discount_percentage, $image_url, $discount_methods_db, $discount_name_db);
    }

    $stmt->execute();
    header("Location: kelola_produk.php");
    exit();
}

// ... Sisa kode file (proses hapus, ambil data, dan HTML) tetap sama seperti jawaban sebelumnya ...
// Anda bisa menyalin seluruh kode dari jawaban sebelumnya untuk file ini, dan pastikan blok `if ($_SERVER['REQUEST_METHOD'] === 'POST')` di atas sudah benar

// Ambil data produk (tidak berubah)
$result = $conn->query("SELECT * FROM products ORDER BY category, name");
$products_list = [];
if ($result) { while ($row = $result->fetch_assoc()) { $products_list[] = $row; } }
$product_to_edit = null;
if (isset($_GET['edit'])) {
    $id_to_edit = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $product_to_edit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS dari sebelumnya tetap sama, tambahkan beberapa style baru di bawah */
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; margin: 0; }
        .container { max-width: 1200px; margin: auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .form-container, .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.07); }
        h2 { color: #5a3a22; margin-top: 0; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; color: white; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 600; text-align: center; transition: all 0.3s ease; margin: 5px; }
        .btn-primary { background-color: #5a3a22; }
        .btn-secondary { background-color: #6c757d; }
        .btn-dashboard { background-color: #17a2b8; }
        .form-buttons { display: flex; align-items: center; justify-content: space-between; margin-top: 25px; flex-wrap: wrap; gap: 10px; }
        .product-table { width: 100%; }
        .product-table img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        .product-table th, .product-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        .action-buttons a { margin-right: 10px; text-decoration: none; color: #333; }
        /* === STYLE BARU UNTUK KONTROL DINAMIS === */
        .discount-controls { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-top: 5px; }
        .radio-group label { font-weight: normal; margin-right: 20px; }
        .control-box { padding-top: 15px; border-top: 1px solid #eee; margin-top: 15px; }
        #metode-checkboxes label { display: inline-flex; align-items: center; font-weight:normal; }
        #metode-checkboxes input { margin-right: 5px; }
         @media (max-width: 900px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2><?php echo $product_to_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2>
        <form action="kelola_produk.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product_to_edit['id'] ?? ''; ?>">
            <input type="hidden" name="current_image" value="<?php echo $product_to_edit['image_url'] ?? ''; ?>">
            
            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product_to_edit['name'] ?? ''); ?>" required>
            </div>
         <div class="form-group">
    <label for="category">Kategori</label>
    <select name="category" required>
        <option value="makanan" <?php if(isset($product_to_edit['category']) && $product_to_edit['category'] == 'makanan') echo 'selected'; ?>>
            Makanan
        </option>
        <option value="minuman-kopi" <?php if(isset($product_to_edit['category']) && $product_to_edit['category'] == 'minuman-kopi') echo 'selected'; ?>>
            Minuman Kopi
        </option>
        <option value="minuman-nonkopi" <?php if(isset($product_to_edit['category']) && $product_to_edit['category'] == 'minuman-nonkopi') echo 'selected'; ?>>
            Minuman Non-Kopi
        </option>
        <option value="paket-kopi" <?php if(isset($product_to_edit['category']) && $product_to_edit['category'] == 'paket-kopi') echo 'selected'; ?>>
            Paket Kopi
        </option>
        <option value="paket-teh" <?php if(isset($product_to_edit['category']) && $product_to_edit['category'] == 'paket-teh') echo 'selected'; ?>>
            Paket Teh
        </option>
    </select>
</div>
            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($product_to_edit['price'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Pengaturan Diskon</label>
                <div class="discount-controls">
                    <div class="form-group">
                         <label for="discount_percentage">Persentase Diskon (%)</label>
                         <input type="number" name="discount_percentage" value="<?php echo htmlspecialchars($product_to_edit['discount_percentage'] ?? '0'); ?>" min="0" max="100">
                    </div>
                    
                    <?php
                    // Tentukan tipe diskon saat edit. Default 'metode'.
                    $current_discount_type = 'metode';
                    if (!empty($product_to_edit['discount_name'])) {
                        $current_discount_type = 'lainnya';
                    }
                    ?>

                    <div class="radio-group">
                        <strong>Tipe Kondisi Diskon:</strong><br>
                        <label>
                            <input type="radio" name="discount_type" value="metode" <?php echo $current_discount_type === 'metode' ? 'checked' : ''; ?>> Berdasarkan Metode
                        </label>
                        <label>
                            <input type="radio" name="discount_type" value="lainnya" <?php echo $current_discount_type === 'lainnya' ? 'checked' : ''; ?>> Diskon Lainnya (Semua Metode)
                        </label>
                    </div>

                    <div id="metode-controls" class="control-box">
                        <label>Pilih Metode yang Berlaku:</label>
                        <div id="metode-checkboxes">
                             <?php
                                $allowed_methods = [];
                                if (isset($product_to_edit['discount_methods'])) {
                                    $allowed_methods = explode(',', $product_to_edit['discount_methods']);
                                }
                             ?>
                            <label><input type="checkbox" name="discount_methods[]" value="pickup" <?php echo in_array('pickup', $allowed_methods) ? 'checked' : ''; ?>> Ambil Ditempat</label>
                            <label><input type="checkbox" name="discount_methods[]" value="delivery" <?php echo in_array('delivery', $allowed_methods) ? 'checked' : ''; ?>> Ongkir</label>
                            <label><input type="checkbox" name="discount_methods[]" value="cod" <?php echo in_array('cod', $allowed_methods) ? 'checked' : ''; ?>> COD</label>
                        </div>
                    </div>

                    <div id="lainnya-controls" class="control-box">
                        <label for="discount_name">Nama Diskon (Cth: Diskon 789, Promo Gajian)</label>
                        <input type="text" name="discount_name" placeholder="Ketik nama diskon di sini..." value="<?php echo htmlspecialchars($product_to_edit['discount_name'] ?? ''); ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="image">Gambar Produk</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="form-buttons">
                 <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Produk</button>
                 <a href="index.php" class="btn btn-dashboard">Dashboard</a>
            </div>
        </form>
    </div>
    <div class="table-container">
        <h2>Daftar Produk</h2>
        <table class="product-table">
            <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($products_list as $product): ?>
                <tr>
                    <td><img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td>Rp <?php echo number_format($product['price']); ?></td>
                    <td class="action-buttons">
                        <a href="kelola_produk.php?edit=<?php echo $product['id']; ?>"><i class="fas fa-edit"></i></a>
                        <a href="kelola_produk.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Yakin?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded