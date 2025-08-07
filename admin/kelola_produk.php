<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';
$message = '';
$error = '';

// --- PROSES TAMBAH/UPDATE PRODUK ---
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
        $file = $_FILES['image'];
        $upload_dir = '../images/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        
        if (in_array($file['type'], $allowed_types)) {
            // Hapus gambar lama jika ini adalah proses edit
            if ($product_id && !empty($image_url) && file_exists($upload_dir . $image_url)) {
                unlink($upload_dir . $image_url);
            }
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'prod_' . uniqid() . '.' . $file_extension;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $image_url = $new_filename;
            }
        } else {
            $error = "Tipe file tidak valid. Hanya JPG, JPEG, dan PNG yang diperbolehkan.";
        }
    }
    
    if (empty($error)) {
        if ($product_id) { // Proses Update
            $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, discount_percentage=?, image_url=?, discount_methods=?, discount_name=? WHERE id=?");
            $stmt->bind_param("ssdisssi", $name, $category, $price, $discount_percentage, $image_url, $discount_methods_db, $discount_name_db, $product_id);
        } else { // Proses Insert
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, discount_percentage, image_url, discount_methods, discount_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdisss", $name, $category, $price, $discount_percentage, $image_url, $discount_methods_db, $discount_name_db);
        }

        if ($stmt->execute()) {
            header("Location: kelola_produk.php?status=success");
            exit();
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}

// --- PROSES HAPUS ---
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $stmt_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($result_img && $row_img = $result_img->fetch_assoc()) {
        $image_to_delete = $row_img['image_url'];
        if ($image_to_delete && file_exists('../images/' . $image_to_delete)) {
            unlink('../images/' . $image_to_delete);
        }
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    header("Location: kelola_produk.php?status=deleted");
    exit();
}

// Ambil data produk
$result = $conn->query("SELECT * FROM products ORDER BY category, name");
$products_list = [];
if ($result) { 
    while ($row = $result->fetch_assoc()) { 
        $products_list[] = $row; 
    } 
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
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
            max-width: 1400px; 
            margin: auto; 
            display: grid; 
            grid-template-columns: 1fr 1.5fr; 
            gap: 30px; 
        }
        
        .header-controls {
            grid-column: 1 / -1;
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
        
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #e1e5e9; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
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
        
        .btn-dashboard { 
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); 
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }
        
        .btn-dashboard:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
        }
        
        .form-buttons { 
            display: flex; 
            gap: 15px; 
            flex-wrap: wrap; 
            margin-top: 25px; 
        }
        
        .product-table { 
            width: 100%; 
            border-collapse: collapse; 
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .product-table th { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
            padding: 15px 12px; 
            text-align: left; 
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .product-table td { 
            padding: 15px 12px; 
            border-bottom: 1px solid #f0f0f0; 
            text-align: left; 
            vertical-align: middle; 
            transition: background-color 0.3s ease;
        }
        
        .product-table tr:hover td {
            background-color: #f8f9fa;
        }
        
        .product-table img { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .product-table img:hover {
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
        
        /* Discount Controls Styling */
        .discount-controls { 
            border: 2px solid #e1e5e9; 
            border-radius: 12px; 
            padding: 20px; 
            margin-top: 10px; 
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .discount-controls:hover {
            border-color: #5a3a22;
            background: white;
        }
        
        .radio-group { 
            margin: 15px 0; 
        }
        
        .radio-group label { 
            font-weight: normal !important; 
            margin-right: 20px; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        
        .radio-group label:hover {
            background-color: rgba(90, 58, 34, 0.1);
        }
        
        .control-box { 
            padding-top: 15px; 
            border-top: 1px solid #dee2e6; 
            margin-top: 15px; 
        }
        
        #metode-checkboxes { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            margin-top: 10px;
        }
        
        #metode-checkboxes label { 
            display: inline-flex !important; 
            align-items: center; 
            font-weight: normal !important; 
            background: white;
            border: 2px solid #e1e5e9;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
        }
        
        #metode-checkboxes label:hover {
            border-color: #5a3a22;
            background: #f8f9fa;
        }
        
        #metode-checkboxes input { 
            margin-right: 8px; 
            width: auto !important;
        }
        
        .category-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .category-makanan { background: #d4edda; color: #155724; }
        .category-minuman-kopi { background: #d1ecf1; color: #0c5460; }
        .category-minuman-nonkopi { background: #fce4ec; color: #880e4f; }
        .category-paket-kopi { background: #fff3cd; color: #856404; }
        .category-paket-teh { background: #e2e3e5; color: #383d41; }
        
        .price-display {
            font-weight: 600;
            color: #5a3a22;
            font-size: 1.1rem;
        }
        
        .discount-info {
            font-size: 0.8rem;
            color: #dc3545;
            margin-top: 2px;
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
        @media (max-width: 1024px) {
            .container { 
                grid-template-columns: 1fr; 
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
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
            
            .product-table {
                min-width: 600px;
            }
            
            .product-table th,
            .product-table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            
            .product-table img {
                width: 50px;
                height: 50px;
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
            
            #metode-checkboxes {
                flex-direction: column;
            }
            
            .radio-group label {
                margin-right: 0;
                margin-bottom: 10px;
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
            
            .product-table th,
            .product-table td {
                padding: 8px 6px;
                font-size: 0.8rem;
            }
            
            .product-table img {
                width: 40px;
                height: 40px;
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
        
        .form-group.focused label {
            color: #5a3a22;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        /* Loading Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-controls">
            <h1><i class="fas fa-box-open"></i> Kelola Produk</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        
        <div class="form-container fade-in">
            <h2><i class="fas fa-<?php echo $product_to_edit ? 'edit' : 'plus-circle'; ?>"></i> <?php echo $product_to_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2>
            <form id="productForm" action="kelola_produk.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product_to_edit['id'] ?? ''; ?>">
                <input type="hidden" name="current_image" value="<?php echo $product_to_edit['image_url'] ?? ''; ?>">
                
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Nama Produk</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product_to_edit['name'] ?? ''); ?>" placeholder="Masukkan nama produk..." required>
                </div>
                
                <div class="form-group">
                    <label for="category"><i class="fas fa-list"></i> Kategori</label>
                    <select id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
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
                    <label for="price"><i class="fas fa-money-bill-wave"></i> Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product_to_edit['price'] ?? ''); ?>" min="0" placeholder="0" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-percent"></i> Pengaturan Diskon</label>
                    <div class="discount-controls">
                        <div class="form-group">
                            <label for="discount_percentage">Persentase Diskon (%)</label>
                            <input type="number" id="discount_percentage" name="discount_percentage" value="<?php echo htmlspecialchars($product_to_edit['discount_percentage'] ?? '0'); ?>" min="0" max="100" placeholder="0">
                        </div>
                        
                        <?php
                        // Tentukan tipe diskon saat edit. Default 'metode'.
                        $current_discount_type = 'metode';
                        if (!empty($product_to_edit['discount_name'])) {
                            $current_discount_type = 'lainnya';
                        }
                        ?>

                        <div class="radio-group">
                            <strong><i class="fas fa-cog"></i> Tipe Kondisi Diskon:</strong><br>
                            <label>
                                <input type="radio" name="discount_type" value="metode" <?php echo $current_discount_type === 'metode' ? 'checked' : ''; ?>> 
                                <i class="fas fa-shipping-fast"></i> Berdasarkan Metode
                            </label>
                            <label>
                                <input type="radio" name="discount_type" value="lainnya" <?php echo $current_discount_type === 'lainnya' ? 'checked' : ''; ?>> 
                                <i class="fas fa-star"></i> Diskon Lainnya (Semua Metode)
                            </label>
                        </div>

                        <div id="metode-controls" class="control-box">
                            <label><i class="fas fa-check-square"></i> Pilih Metode yang Berlaku:</label>
                            <div id="metode-checkboxes">
                                <?php
                                    $allowed_methods = [];
                                    if (isset($product_to_edit['discount_methods'])) {
                                        $allowed_methods = explode(',', $product_to_edit['discount_methods']);
                                    }
                                ?>
                                <label>
                                    <input type="checkbox" name="discount_methods[]" value="pickup" <?php echo in_array('pickup', $allowed_methods) ? 'checked' : ''; ?>> 
                                    <i class="fas fa-store"></i> Ambil Ditempat
                                </label>
                                <label>
                                    <input type="checkbox" name="discount_methods[]" value="delivery" <?php echo in_array('delivery', $allowed_methods) ? 'checked' : ''; ?>> 
                                    <i class="fas fa-truck"></i> Ongkir
                                </label>
                                <label>
                                    <input type="checkbox" name="discount_methods[]" value="cod" <?php echo in_array('cod', $allowed_methods) ? 'checked' : ''; ?>> 
                                    <i class="fas fa-hand-holding-usd"></i> COD
                                </label>
                            </div>
                        </div>

                        <div id="lainnya-controls" class="control-box">
                            <label for="discount_name"><i class="fas fa-gift"></i> Nama Diskon (Cth: Diskon 789, Promo Gajian)</label>
                            <input type="text" id="discount_name" name="discount_name" placeholder="Ketik nama diskon di sini..." value="<?php echo htmlspecialchars($product_to_edit['discount_name'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Gambar Produk</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div id="imagePreview" class="image-preview" style="display: none;">
                        <p><i class="fas fa-eye"></i> Preview Gambar:</p>
                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; height: auto;">
                    </div>
                    <?php if ($product_to_edit && !empty($product_to_edit['image_url'])): ?>
                        <div class="image-preview">
                            <p><i class="fas fa-image"></i> Gambar saat ini:</p>
                            <img src="../images/<?php echo $product_to_edit['image_url']; ?>" alt="Current Image" style="max-width: 200px; height: auto;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-<?php echo $product_to_edit ? 'save' : 'plus'; ?>"></i>
                        <?php echo $product_to_edit ? 'Update Produk' : 'Simpan Produk'; ?>
                    </button>
                    <?php if ($product_to_edit): ?>
                        <a href="kelola_produk.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-container slide-in">
            <h2><i class="fas fa-list"></i> Daftar Produk</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-image"></i> Gambar</th>
                        <th><i class="fas fa-tag"></i> Nama</th>
                        <th><i class="fas fa-list"></i> Kategori</th>
                        <th><i class="fas fa-money-bill-wave"></i> Harga</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products_list as $product): ?>
                    <tr class="product-row" data-id="<?php echo $product['id']; ?>">
                        <td>
                            <img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onclick="openImageModal(this.src)">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                            <?php if ($product['discount_percentage'] > 0): ?>
                                <div class="discount-info">
                                    <i class="fas fa-percent"></i> Diskon <?php echo $product['discount_percentage']; ?>%
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="category-badge category-<?php echo $product['category']; ?>">
                                <?php echo str_replace('-', ' ', $product['category']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="price-display">Rp <?php echo number_format($product['price']); ?></span>
                            <?php if ($product['discount_percentage'] > 0): ?>
                                <div class="discount-info">
                                    Rp <?php echo number_format($product['price'] * (100 - $product['discount_percentage']) / 100); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="kelola_produk.php?edit=<?php echo $product['id']; ?>" 
                                   class="action-btn edit" 
                                   title="Edit Produk">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" 
                                   class="action-btn delete" 
                                   onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')"
                                   title="Hapus Produk">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
        // Discount Type Controls
        function toggleDiscountControls() {
            const discountType = document.querySelector('input[name="discount_type"]:checked').value;
            const metodeControls = document.getElementById('metode-controls');
            const lainnyaControls = document.getElementById('lainnya-controls');
            
            if (discountType === 'metode') {
                metodeControls.style.display = 'block';
                lainnyaControls.style.display = 'none';
            } else {
                metodeControls.style.display = 'none';
                lainnyaControls.style.display = 'block';
            }
        }

        // Initialize discount controls on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleDiscountControls();
            
            // Add event listeners to radio buttons
            document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
                radio.addEventListener('change', toggleDiscountControls);
            });
        });

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
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Validate form
            const name = document.getElementById('name').value.trim();
            const category = document.getElementById('category').value;
            const price = document.getElementById('price').value;
            
            if (!name) {
                e.preventDefault();
                showNotification('Nama produk harus diisi!', 'error');
                resetSubmitButton();
                return;
            }
            
            if (!category) {
                e.preventDefault();
                showNotification('Kategori harus dipilih!', 'error');
                resetSubmitButton();
                return;
            }
            
            if (!price || price <= 0) {
                e.preventDefault();
                showNotification('Harga harus diisi dan lebih dari 0!', 'error');
                resetSubmitButton();
                return;
            }
        });

        function resetSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const isEdit = <?php echo $product_to_edit ? 'true' : 'false'; ?>;
            
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = isEdit ? 
                '<i class="fas fa-save"></i> Update Produk' : 
                '<i class="fas fa-plus"></i> Simpan Produk';
            submitBtn.disabled = false;
        }

        // Enhanced Delete Confirmation
        function confirmDelete(id, name) {
            if (confirm(`Apakah Anda yakin ingin menghapus produk "${name}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
                // Add loading effect to the row
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                }
                
                // Redirect to delete
                window.location.href = `kelola_produk.php?delete=${id}`;
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
                showNotification('Produk berhasil disimpan!', 'success');
            } else if (status === 'deleted') {
                showNotification('Produk berhasil dihapus!', 'success');
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
                document.getElementById('productForm').submit();
            }
        });

        // Form field animations
        document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
            field.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            field.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Price formatting
        document.getElementById('price').addEventListener('input', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = value;
            }
        });

        // Auto-format discount percentage
        document.getElementById('discount_percentage').addEventListener('input', function() {
            let value = parseInt(this.value);
            if (value > 100) this.value = 100;
            if (value < 0) this.value = 0;
        });
    </script>
</body>
</html>