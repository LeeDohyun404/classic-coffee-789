<?php
ob_start();
include 'header.php';
require_once 'config.php';

// === Logika untuk memproses ulasan ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id_post = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $review_text = $_POST['review_text'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    if ($rating < 1 || $rating > 5) {
        $error_message = "Peringkat tidak valid.";
        goto display_page;
    }

    $conn->begin_transaction();
    try {
        $stmt_insert = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("iiss", $product_id_post, $user_id, $rating, $review_text);
        $stmt_insert->execute();

        $stmt_avg = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(id) AS review_count FROM reviews WHERE product_id = ?");
        $stmt_avg->bind_param("i", $product_id_post);
        $stmt_avg->execute();
        $result_avg = $stmt_avg->get_result()->fetch_assoc();
        
        $new_avg = $result_avg['avg_rating'];
        $new_count = $result_avg['review_count'];

        $stmt_update = $conn->prepare("UPDATE products SET average_rating = ?, review_count = ? WHERE id = ?");
        $stmt_update->bind_param("dii", $new_avg, $new_count, $product_id_post);
        $stmt_update->execute();

        $conn->commit();
        ob_end_clean();
        header("Location: detail_produk.php?id=" . $product_id_post . "&status=success");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Terjadi kesalahan database: " . $e->getMessage();
        goto display_page;
    }
}
// === Akhir logika untuk memproses ulasan ===

display_page:
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Produk tidak ditemukan.");
}

$reviews = [];
$reviews_stmt = $conn->prepare("SELECT r.*, COALESCE(u.username, 'Tamu') AS username FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
while ($review = $reviews_result->fetch_assoc()) {
    $reviews[] = $review;
}

?>

<title><?php echo htmlspecialchars($product['name']); ?> - Classic Coffee 789</title>
<style>
    .product-detail-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .product-detail-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .product-detail-header h1 {
        font-family: 'Playfair Display', serif;
        color: #5a3a22;
        font-size: 2.5em;
        margin-bottom: 10px;
    }
    .product-image {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .price-section {
        font-size: 1.5em;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }
    .rating-display .fas.fa-star {
        color: #d1d1d1;
    }
    .rating-display .fas.fa-star.rated {
        color: #ffc107;
    }
    .review-section {
        margin-top: 40px;
        border-top: 1px solid #eee;
        padding-top: 30px;
    }
    .review-form-section h3 {
        color: #5a3a22;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    .form-group textarea, .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .btn-submit-review {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .review-list {
        margin-top: 30px;
    }
    .review-item {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .review-item:last-child {
        border-bottom: none;
    }
    .review-author {
        font-weight: bold;
        color: #5a3a22;
    }
    .review-rating {
        font-size: 1.2em;
        margin: 5px 0;
    }
    .review-text {
        font-style: italic;
        color: #666;
    }
    .review-date {
        font-size: 0.8em;
        color: #999;
        display: block;
    }
    .notification {
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
    .error-notification {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
</style>

<div class="product-detail-container">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="notification">Ulasan Anda berhasil dikirim!</div>
    <?php elseif (isset($error_message)): ?>
        <div class="error-notification"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="product-detail-header">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <img src="images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
        <div class="price-section">
            <?php if (GLOBAL_DISKON_AKTIF && $product['discount_percentage'] > 0): ?>
                <span style="text-decoration: line-through; color: #999; font-size: 0.8em;">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                <br>Rp <?php echo number_format($product['price'] * (1 - $product['discount_percentage'] / 100), 0, ',', '.'); ?>
            <?php else: ?>
                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
            <?php endif; ?>
        </div>
        <div class="rating-display">
            <?php if ($product['review_count'] > 0): ?>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?php echo ($i <= round($product['average_rating'])) ? 'rated' : ''; ?>"></i>
                <?php endfor; ?>
                <span>(<?php echo $product['review_count']; ?> ulasan)</span>
            <?php else: ?>
                <span>Belum terdapat ulasan.</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="review-section">
        <div class="review-form-section">
            <h3>Tulis Ulasan Anda</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="detail_produk.php?id=<?php echo $product_id; ?>" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="form-group">
                        <label for="rating">Peringkat Bintang:</label>
                        <select name="rating" id="rating" required>
                            <option value="5">5 Bintang</option>
                            <option value="4">4 Bintang</option>
                            <option value="3">3 Bintang</option>
                            <option value="2">2 Bintang</option>
                            <option value="1">1 Bintang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="review_text">Komentar (Opsional):</label>
                        <textarea name="review_text" id="review_text" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit-review">Kirim Ulasan</button>
                </form>
            <?php else: ?>
                <p>Silakan <a href="login.php">login</a> untuk memberikan ulasan.</p>
            <?php endif; ?>
        </div>

        <div class="review-list">
            <h3>Ulasan Pelanggan</h3>
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <span class="review-author"><?php echo htmlspecialchars($review['username']); ?></span>
                        <span class="review-date"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo ($i <= $review['rating']) ? 'rated' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if (!empty($review['review_text'])): ?>
                            <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum terdapat ulasan untuk produk ini.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>