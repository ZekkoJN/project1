<?php
$page_title = "Promo & Diskon";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
    /* Promo-specific styles */
    .promo-hero {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 50%, #c92a2a 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
    }

    .promo-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .promo-hero p {
        font-size: 1.3rem;
        margin-bottom: 30px;
    }

    .promo-badge {
        background: #ffd700;
        color: #000;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .promo-section {
        padding: 60px 20px;
    }

    .promo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .promo-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .promo-card:hover {
        transform: translateY(-10px);
    }

    .promo-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .promo-content {
        padding: 20px;
    }

    .promo-discount {
        background: #ff6b6b;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 1.5rem;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 15px;
    }

    .promo-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .promo-desc {
        color: #666;
        margin-bottom: 15px;
    }

    .promo-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        width: 100%;
        transition: background 0.3s;
    }

    .promo-btn:hover {
        background: var(--secondary-color);
    }

    .promo-card,
    .promo-title,
    .promo-desc {
        transition: all 0.3s ease;
    }

    body.dark-mode .promo-card {
        background: #2d2d2d;
    }

    body.dark-mode .promo-title {
        color: #fff;
    }

    body.dark-mode .promo-desc {
        color: #ccc;
    }
</style>

<section class="promo-hero">
    <div class="container">
        <span class="promo-badge">🔥 PROMO SPESIAL</span>
        <h1>Diskon Hingga 50%!</h1>
        <p>Dapatkan penawaran terbaik untuk produk elektronik pilihan</p>
    </div>
</section>

<section class="promo-section">
    <div class="container">
        <h2 class="section-title">Promo Bulan Ini</h2>
        <div class="promo-grid">
            <?php
            // Fetch products with discount
            $promo_products = fetchAll("SELECT * FROM products WHERE discount_price IS NOT NULL AND discount_price < price AND status = 'active' ORDER BY RAND() LIMIT 6");
            
            if (empty($promo_products)) {
                // Sample promos if no discount products
                $promo_products = fetchAll("SELECT * FROM products WHERE status = 'active' ORDER BY views DESC LIMIT 6");
            }
            
            foreach ($promo_products as $product):
                $discount_percent = 0;
                if ($product['discount_price'] && $product['price'] > 0) {
                    $discount_percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100);
                } else {
                    $discount_percent = rand(10, 50); // Random discount for demo
                }
            ?>
                <div class="promo-card">
                    <img src="img/<?php echo clean($product['image']); ?>" alt="<?php echo clean($product['name']); ?>">
                    <div class="promo-content">
                        <div class="promo-discount">-<?php echo $discount_percent; ?>%</div>
                        <h3 class="promo-title"><?php echo clean($product['name']); ?></h3>
                        <p class="promo-desc"><?php echo clean(substr($product['description'] ?? 'Produk berkualitas dengan harga spesial', 0, 80)); ?>...</p>
                        <div style="margin-bottom: 15px;">
                            <?php if ($product['discount_price']): ?>
                                <span style="text-decoration: line-through; color: #999;">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span><br>
                            <?php endif; ?>
                            <span style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">
                                Rp <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?>
                            </span>
                        </div>
                        <?php if (isLoggedIn()): ?>
                            <button class="promo-btn" onclick="window.location.href='index.php#products'">Lihat Produk</button>
                        <?php else: ?>
                            <button class="promo-btn" onclick="window.location.href='login.php'">Login untuk Beli</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="key-features" style="background: #f8f9fa;">
    <div class="container">
        <h2 class="section-title">Keuntungan Berbelanja di Promo</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <h3>Harga Spesial</h3>
                <p>Dapatkan harga terbaik dengan diskon hingga 50% untuk produk pilihan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Promo Terbatas</h3>
                <p>Penawaran terbatas, buruan ambil sebelum kehabisan stok</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h3>Bonus Menarik</h3>
                <p>Dapatkan bonus dan hadiah menarik untuk pembelian tertentu</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Garansi Resmi</h3>
                <p>Semua produk promo tetap bergaransi resmi dari distributor</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
