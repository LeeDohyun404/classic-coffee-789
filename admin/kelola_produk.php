<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Cek jika koneksi database gagal
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// --- PROSES TAMBAH/UPDATE PRODUK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : null;
    $image_url = $_POST['current_image'] ?? '';

    // Handle file upload jika ada file baru yang dipilih
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/'; // Folder tujuan adalah /images/
        
        // Hapus gambar lama jika ini adalah proses update dan gambar lama ada
        if ($product_id && !empty($image_url) && file_exists($upload_dir . $image_url)) {
            unlink($upload_dir . $image_url);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'prod_' . uniqid() . '.' . $file_extension;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
            $image_url = $new_filename; // Update nama file gambar dengan yang baru
        }
    }

    if ($product_id) { // Proses Update
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, image_url=? WHERE id=?");
        $stmt->bind_param("ssdsi", $name, $category, $price, $image_url, $product_id);
    } else { // Proses Insert (Tambah)
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $category, $price, $image_url);
    }
    
    $stmt->execute();
    header("Location: kelola_produk.php");
    exit();
}

// --- PROSES HAPUS PRODUK ---
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    
    // Ambil nama file gambar untuk dihapus dari server
    $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($result_img->num_rows > 0) {
        $image_to_delete = $result_img->fetch_assoc()['image_url'];
        if ($image_to_delete && file_exists('../images/' . $image_to_delete)) {
            unlink('../images/' . $image_to_delete);
        }
    }

    // Hapus data dari database
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);
    $stmt_delete->execute();
    
    header("Location: kelola_produk.php");
    exit();
}

// Ambil semua data produk untuk ditampilkan di tabel
$result = $conn->query("SELECT * FROM products ORDER BY category, name");
if ($result === false) {
    die("Query untuk mengambil produk gagal: " . $conn->error);
}
$products_list = [];
while ($row = $result->fetch_assoc()) {
    $products_list[] = $row;
}

// Ambil data produk yang akan diedit jika ada parameter 'edit'
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
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f4f7f6; 
            padding: 20px; 
            margin: 0;
        }
        .container { 
            max-width: 1200px; 
            margin: auto; 
            display: grid; 
            grid-template-columns: 1fr 2fr; 
            gap: 30px; 
        }
        .form-container, .table-container { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.07); 
        }
        h2 { 
            color: #5a3a22; 
            margin-top: 0; 
            margin-bottom: 20px; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 600; 
        }
        .form-group input, .form-group select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-sizing: border-box;
        }
        
        /* Styling untuk button yang diperbaiki */
        .btn { 
            padding: 12px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            color: white; 
            text-decoration: none; 
            display: inline-block; 
            font-size: 14px; 
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            margin: 5px;
        }
        
        .btn-primary { 
            background-color: #5a3a22; 
        }
        .btn-primary:hover {
            background-color: #4a2f1d;
            transform: translateY(-2px);
        }
        
        .btn-secondary { 
            background-color: #6c757d; 
        }
        .btn-secondary:hover {
            background-color: #5a6169;
            transform: translateY(-2px);
        }
        
        .btn-dashboard { 
            background-color: #17a2b8; 
            color: white;
        }
        .btn-dashboard:hover {
            background-color: #138496;
            transform: translateY(-2px);
        }
        
        /* Container untuk button form */
        .form-buttons {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 25px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .form-buttons .left-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .form-buttons .right-buttons {
            display: flex;
            gap: 10px;
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            .form-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-buttons .left-buttons,
            .form-buttons .right-buttons {
                justify-content: center;
                width: 100%;
            }
            
            .btn {
                flex: 1;
                min-width: 120px;
            }
        }
        
        .product-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .product-table th, .product-table td { 
            padding: 12px; 
            border-bottom: 1px solid #eee; 
            text-align: left; 
            vertical-align: middle;
        }
        .product-table img { 
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 5px; 
        }
        .action-buttons a { 
            margin-right: 10px; 
            text-decoration: none; 
            color: #333; 
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .action-buttons a:hover {
            background-color: #f8f9fa;
        }
        
        /* Style untuk preview gambar */
        .current-image {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
        }
        
        .current-image img {
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
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
                        <option value="makanan" <?php echo (($product_to_edit['category'] ?? '') == 'makanan') ? 'selected' : ''; ?>>Makanan</option>
                        <option value="minuman-kopi" <?php echo (($product_to_edit['category'] ?? '') == 'minuman-kopi') ? 'selected' : ''; ?>>Minuman Kopi</option>
                        <option value="minuman-nonkopi" <?php echo (($product_to_edit['category'] ?? '') == 'minuman-nonkopi') ? 'selected' : ''; ?>>Minuman Non-Kopi</option>
                        <option value="paket-kopi" <?php echo (($product_to_edit['category'] ?? '') == 'paket-kopi') ? 'selected' : ''; ?>>Paket Kopi</option>
                        <option value="paket-teh" <?php echo (($product_to_edit['category'] ?? '') == 'paket-teh') ? 'selected' : ''; ?>>Paket Teh</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" name="price" value="<?php echo htmlspecialchars($product_to_edit['price'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" <?php echo $product_to_edit ? '' : 'required'; ?>>
                    <?php if ($product_to_edit && !empty($product_to_edit['image_url'])): ?>
                        <div class="current-image">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6c757d;">Gambar saat ini:</p>
                            <img src="../images/<?php echo $product_to_edit['image_url']; ?>" width="80" height="80" style="object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Container button yang diperbaiki -->
                <div class="form-buttons">
                    <div class="left-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $product_to_edit ? 'Update Produk' : 'Tambah Produk'; ?>
                        </button>
                        <?php if ($product_to_edit): ?>
                            <a href="kelola_produk.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal Edit
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="right-buttons">
                        <a href="index.php" class="btn btn-dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Daftar Produk</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products_list as $product): ?>
                    <tr>
                        <td><img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Gambar produk"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>Rp <?php echo number_format($product['price']); ?></td>
                        <td class="action-buttons">
                            <a href="kelola_produk.php?edit=<?php echo $product['id']; ?>" title="Edit">
                                <i class="fas fa-edit" style="color: #28a745;"></i>
                            </a>
                            <a href="kelola_produk.php?delete=<?php echo $product['id']; ?>" title="Hapus" 
                               onclick="return confirm('Yakin ingin menghapus produk ini?');">
                                <i class="fas fa-trash" style="color: #dc3545;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products_list)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #6c757d;">
                                <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                                Belum ada produk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>