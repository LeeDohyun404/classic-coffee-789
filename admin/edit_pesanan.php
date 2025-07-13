<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Proses form jika data pelanggan di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'])) {
    $guest_name = $_POST['customer_name'];
    $guest_address = $_POST['guest_address'];
    $guest_phone = $_POST['guest_phone'];
    $guest_email = $_POST['guest_email'];
    $stmt = $conn->prepare("UPDATE orders SET guest_name = ?, guest_address = ?, guest_phone = ?, guest_email = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $guest_name, $guest_address, $guest_phone, $guest_email, $order_id);
    if($stmt->execute()){
        header("Location: edit_pesanan.php?id=" . $order_id . "&status=success");
        exit();
    } else {
        $error = "Gagal memperbarui data.";
    }
}

// Ambil data pesanan utama
$stmt = $conn->prepare("SELECT orders.id, COALESCE(guest_name, username) as customer_name, guest_address, guest_phone, guest_email FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) { die("Pesanan tidak ditemukan."); }

// Ambil semua item dari pesanan tersebut untuk ditampilkan
$sql_items = "SELECT oi.id as item_id, oi.product_id, oi.quantity, p.name AS product_name 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// --- PERUBAHAN DI SINI ---
// Ambil HANYA produk yang relevan untuk pilihan di dropdown
$active_products_query = "SELECT id, name FROM products WHERE name LIKE 'Paket%' OR name LIKE '%Burger%' OR name IN ('Dimsum', 'Spaghetti Bolognese', 'Mango Sticky Rice', 'Sakura Latte (Ralat)', 'Lychee Latte (Lyla)', 'Caramel Latte (Carla)', 'Kopi Susu Original', 'Kopi Susu Gula Aren', 'Hazelnut Latte (Ella)', 'Milk Base Mangga', 'Milk Base Red Velvet', 'Thai Tea Original', 'Thai Tea Lychee', 'Thai Tea Milk', 'Thai Tea Lemon') ORDER BY name ASC";
$all_products_result = $conn->query($active_products_query);
$all_products = [];
while ($prod_row = $all_products_result->fetch_assoc()) {
    $all_products[] = $prod_row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan #<?php echo htmlspecialchars($order_id); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; }
        .form-container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1, h2 { color: #5a3a22; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .product-select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        .form-buttons { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-submit { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }
        hr { border: none; border-top: 1px solid #eee; margin: 30px 0; }
        .item-list { list-style: none; padding: 0; }
        .item-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #f0f0f0; }
        .item-form { display: flex; align-items: center; gap: 8px; }
        .quantity-input { width: 70px; text-align: center; }
        .product-select { min-width: 150px; flex-grow:1; }
        .btn-update-item { background-color: #3498db; }
        .btn-delete-item { background-color: #e74c3c; }
        .btn-update-item, .btn-delete-item { color: white; padding: 8px 15px; font-size: 14px; border: none; border-radius: 5px; text-decoration: none; font-weight: 500; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Pesanan #<?php echo htmlspecialchars($order_id); ?></h1>

        <form method="POST">
            <h2>Data Pelanggan</h2>
            <div class="form-group"><label>Nama Pemesan</label><input type="text" name="customer_name" value="<?php echo htmlspecialchars($order['customer_name']); ?>" required></div>
            <div class="form-group"><label>Alamat</label><textarea name="guest_address" rows="4" required><?php echo htmlspecialchars($order['guest_address']); ?></textarea></div>
            <div class="form-group"><label>No. HP</label><input type="text" name="guest_phone" value="<?php echo htmlspecialchars($order['guest_phone']); ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="guest_email" value="<?php echo htmlspecialchars($order['guest_email']); ?>" required></div>
            <div class="form-buttons"><button type="submit" class="btn btn-submit">Simpan Data Pelanggan</button><a href="index.php" class="btn btn-cancel">Kembali</a></div>
        </form>

        <hr>

        <div>
            <h2>Produk dalam Pesanan</h2>
            <ul class="item-list">
                <?php while($item = $items->fetch_assoc()): ?>
                    <li>
                        <form action="update_item.php" method="POST" class="item-form">
                            <button type="submit" class="btn-update-item">Update</button>
                            <a href="hapus_item.php?item_id=<?php echo $item['item_id']; ?>&order_id=<?php echo $order_id; ?>" class="btn-delete-item" onclick="return confirm('Yakin ingin menghapus item ini?');">Hapus</a>
                            <select name="product_id" class="product-select">
                                <?php foreach($all_products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" <?php if($product['id'] == $item['product_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>