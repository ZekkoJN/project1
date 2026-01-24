<?php
$page_title = "Detail Produk";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header('Location: index.php');
    exit;
}

// Fetch product details
$product = fetchOne("SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = ? AND p.status = 'active'", [$product_id]);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Update views
execute("UPDATE products SET views = views + 1 WHERE id = ?", [$product_id]);

// Fetch reviews
$reviews = fetchAll("SELECT r.*, u.username, u.full_name 
                     FROM reviews r 
                     JOIN users u ON r.user_id = u.id 
                     WHERE r.product_id = ? AND r.status = 'approved' 
                     ORDER BY r.created_at DESC", [$product_id]);

// Calculate average rating
$avg_rating = fetchOne("SELECT AVG(rating) as avg, COUNT(*) as count FROM reviews WHERE product_id = ? AND status = 'approved'", [$product_id]);
$rating_avg = round($avg_rating['avg'] ?? 0, 1);
$review_count = $avg_rating['count'] ?? 0;

// Fetch related products
$related = fetchAll("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'active' ORDER BY RAND() LIMIT 4", [$product['category_id'], $product_id]);
?>

<style>
    .product-detail-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }

    .product-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-bottom: 60px;
    }

    .product-images {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .main-image {
        width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .product-info-detail {
        padding: 20px 0;
    }

    .product-badge-large {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: bold;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .product-badge-large.hot { background: #ff6b6b; color: white; }
    .product-badge-large.new { background: #51cf66; color: white; }
    .product-badge-large.recommended { background: #ffd43b; color: #000; }

    .product-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: #333;
    }

    .product-rating-large {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .stars-large {
        color: #ffd700;
        font-size: 1.5rem;
    }

    .rating-text {
        color: #666;
        font-size: 1.1rem;
    }

    .product-price-large {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-color);
        margin: 20px 0;
    }

    .old-price {
        font-size: 1.5rem;
        color: #999;
        text-decoration: line-through;
        margin-right: 15px;
    }

    .product-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .meta-item i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    .product-description {
        margin: 30px 0;
        line-height: 1.8;
        color: #555;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin: 30px 0;
    }

    .btn-large {
        flex: 1;
        padding: 18px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add-cart {
        background: var(--primary-color);
        color: white;
    }

    .btn-add-cart:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .btn-buy-now {
        background: #ff6b6b;
        color: white;
    }

    .btn-buy-now:hover {
        background: #ee5a6f;
        transform: translateY(-2px);
    }

    .reviews-section {
        margin-top: 60px;
        padding: 40px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .review-item {
        padding: 25px;
        border-bottom: 1px solid #e0e0e0;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .reviewer-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .reviewer-avatar-large {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .related-products {
        margin-top: 60px;
    }

    body.dark-mode .product-title { color: #fff; }
    body.dark-mode .product-description { color: #ccc; }
    body.dark-mode .product-meta { background: #2d2d2d; }
    body.dark-mode .reviews-section { background: #2d2d2d; }

    @media (max-width: 768px) {
        .product-main { grid-template-columns: 1fr; gap: 30px; }
        .product-title { font-size: 1.8rem; }
        .action-buttons { flex-direction: column; }
    }
</style>

<div class="product-detail-container">
    <div class="product-main">
        <div class="product-images">
            <img src="img/<?php echo clean($product['image']); ?>" alt="<?php echo clean($product['name']); ?>" class="main-image">
        </div>

        <div class="product-info-detail">
            <?php if ($product['views'] > 250): ?>
                <span class="product-badge-large hot">🔥 HOT</span>
            <?php elseif (strtotime($product['created_at']) > strtotime('-30 days')): ?>
                <span class="product-badge-large new">✨ NEW</span>
            <?php elseif ($product['views'] > 200): ?>
                <span class="product-badge-large recommended">⭐ RECOMMENDED</span>
            <?php endif; ?>

            <h1 class="product-title"><?php echo clean($product['name']); ?></h1>

            <div class="product-rating-large">
                <div class="stars-large">
                    <?php for ($i = 1; $i <= 5; $i++) echo $i <= round($rating_avg) ? '★' : '☆'; ?>
                </div>
                <span class="rating-text"><?php echo $rating_avg; ?> / 5.0 (<?php echo $review_count; ?> ulasan)</span>
            </div>

            <div class="product-price-large">
                <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                    <span class="old-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                <?php endif; ?>
                Rp <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span><strong>Kategori:</strong> <?php echo clean($product['category_name']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-building"></i>
                    <span><strong>Brand:</strong> <?php echo clean($product['brand']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-box"></i>
                    <span><strong>Stok:</strong> <?php echo $product['stock']; ?> unit</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span><strong>Dilihat:</strong> <?php echo $product['views']; ?>x</span>
                </div>
            </div>

            <div class="product-description">
                <h3>Deskripsi Produk</h3>
                <p><?php echo nl2br(clean($product['description'] ?? 'Produk berkualitas tinggi dengan spesifikasi terbaik di kelasnya.')); ?></p>
            </div>

            <div class="action-buttons">
                <button class="btn-large btn-add-cart" onclick="addToCartDetail(<?php echo $product['id']; ?>)">
                    <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                </button>
                <button class="btn-large btn-buy-now" onclick="buyNow(<?php echo $product['id']; ?>)">
                    <i class="fas fa-bolt"></i> Beli Sekarang
                </button>
                
                <?php if (!isLoggedIn()): ?>
                <div style="margin-top: 15px; padding: 15px; background: #f0f8ff; border-radius: 8px; border-left: 4px solid #667eea;">
                    <p style="margin: 0 0 10px 0; color: #333; font-weight: 600;">
                        💡 <strong>Tip:</strong> Beli sebagai guest atau <a href="login.php" style="color: #667eea; text-decoration: none;"><strong>login untuk dapat benefit member!</strong></a>
                    </p>
                    <small style="color: #666;">
                        Member baru mendapat voucher diskon 10% + program cashback eksklusif
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <div class="reviews-header">
            <h2>Ulasan Produk (<?php echo $review_count; ?>)</h2>
        </div>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="reviewer-header">
                        <div class="reviewer-avatar-large">
                            <?php echo strtoupper(substr($review['full_name'] ?? $review['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong><?php echo clean($review['full_name'] ?? $review['username']); ?></strong>
                            <div class="stars" style="color: #ffd700;">
                                <?php for ($i = 1; $i <= 5; $i++) echo $i <= $review['rating'] ? '★' : '☆'; ?>
                            </div>
                            <small style="color: #999;"><?php echo date('d M Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    </div>
                    <p><?php echo clean($review['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px;">Belum ada ulasan untuk produk ini.</p>
        <?php endif; ?>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
        <div class="related-products">
            <h2 class="section-title">Produk Terkait</h2>
            <div class="products-grid">
                <?php foreach ($related as $rel): ?>
                    <div class="product-card">
                        <img src="img/<?php echo clean($rel['image']); ?>" alt="<?php echo clean($rel['name']); ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo clean($rel['name']); ?></h3>
                            <div class="product-price">Rp <?php echo number_format($rel['discount_price'] ?? $rel['price'], 0, ',', '.'); ?></div>
                            <button class="add-btn" onclick="window.location.href='product_detail.php?id=<?php echo $rel['id']; ?>'">Lihat Detail</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function addToCartDetail(productId) {
    fetch('api/cart_add.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produk berhasil ditambahkan ke keranjang!');
            location.reload();
        } else {
            alert('Gagal menambahkan produk: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function buyNow(productId) {
    addToCartDetail(productId);
    setTimeout(() => {
        window.location.href = 'checkout.php';
    }, 1000);
}
</script>

<?php require_once 'includes/footer.php'; ?>
