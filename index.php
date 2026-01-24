<?php
$page_title = "Home";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/voucher_functions.php';

// Fetch ALL products from database (like HTML version with 35 products)
$featured_products = fetchAll("SELECT * FROM products WHERE status = 'active' ORDER BY id ASC");

// Fetch categories
$categories = fetchAll("SELECT * FROM categories ORDER BY id");

// Fetch reviews
$reviews = fetchAll("SELECT r.*, u.username, u.full_name, p.name as product_name 
                     FROM reviews r 
                     JOIN users u ON r.user_id = u.id 
                     JOIN products p ON r.product_id = p.id 
                     WHERE r.status = 'approved' 
                     ORDER BY r.created_at DESC LIMIT 6");

// Check first login
$show_welcome_banner = isset($_SESSION['is_first_login']) && $_SESSION['is_first_login'];
if ($show_welcome_banner) {
    unset($_SESSION['is_first_login']);
}
?>

<!-- Welcome Banner for First Login -->
<?php if ($show_welcome_banner && isLoggedIn()): ?>
<div class="welcome-banner">
    <div class="welcome-content">
        <div class="welcome-icon">🎉</div>
        <div class="welcome-text">
            <h2>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h2>
            <p>Anda telah mendapatkan benefit eksklusif sebagai member baru TechHub</p>
            <div class="welcome-benefits">
                <?php
                $member_benefits = getMemberBenefits($_SESSION['user_id']);
                $vouchers = getUserVouchers($_SESSION['user_id']);
                $cashback = getCashbackBalance($_SESSION['user_id']);
                ?>
                <div class="benefit-card">
                    <i class="fas fa-ticket-alt"></i>
                    <div>
                        <strong><?php echo count($vouchers); ?></strong>
                        <span>Voucher Aktif</span>
                    </div>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-coins"></i>
                    <div>
                        <strong>Rp <?php echo number_format($cashback, 0); ?></strong>
                        <span>Cashback Tersedia</span>
                    </div>
                </div>
            </div>
        </div>
        <button class="welcome-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
.welcome-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    margin: 0 0 30px 0;
    border-radius: 12px;
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.welcome-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
}

.welcome-icon {
    font-size: 2.5rem;
    min-width: 60px;
}

.welcome-text {
    flex: 1;
}

.welcome-text h2 {
    margin: 0 0 5px 0;
    font-size: 1.5rem;
}

.welcome-text p {
    margin: 0 0 15px 0;
    opacity: 0.95;
}

.welcome-benefits {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.benefit-card {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.15);
    padding: 12px 15px;
    border-radius: 8px;
}

.benefit-card i {
    font-size: 1.5rem;
}

.benefit-card div strong {
    display: block;
    font-size: 1.1rem;
}

.benefit-card div span {
    font-size: 0.85rem;
    opacity: 0.9;
}

.welcome-close {
    position: absolute;
    right: 0;
    top: 0;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s;
}

.welcome-close:hover {
    background: rgba(255,255,255,0.3);
}

@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-benefits {
        justify-content: center;
    }
    
    .welcome-close {
        position: static;
    }
}
</style>
<?php endif; ?>

<!-- Hero Section / Vespa Style -->
<section class="hero" id="home">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Feel True Comfort With <span class="highlight"><?php echo SITE_NAME; ?></span></h1>
                <p class="hero-subtitle">Koleksi lengkap elektronik komputer dan laptop terbaik dengan harga kompetitif</p>
                <div class="hero-features">
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Produk Original Bergaransi</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>Pengiriman Cepat Ke Seluruh Indonesia</span>
                    </div>
                </div>
                <button class="cta-btn" onclick="document.getElementById('products').scrollIntoView({behavior: 'smooth'})">Jelajahi Produk</button>
            </div>
            <div class="hero-carousel">
                <div class="carousel-wrapper">
                    <div class="carousel-main">
                        <img id="carouselMainImage" src="img/Gambarlaptop1.jpeg" alt="Featured Product">
                    </div>
                    <button class="carousel-btn prev" onclick="prevSlide()"><i class="fas fa-chevron-left"></i></button>
                    <button class="carousel-btn next" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></button>
                </div>

                <!-- Floating Info Boxes -->
                <div class="floating-box box-1">
                    <div class="box-icon"><i class="fas fa-laptop"></i></div>
                    <div class="box-info">
                        <h4>Laptop Gaming</h4>
                        <p>Performa Tinggi</p>
                    </div>
                </div>

                <div class="floating-box box-2">
                    <div class="box-icon"><i class="fas fa-star"></i></div>
                    <div class="box-info">
                        <h4>Best Seller</h4>
                        <p>Pilihan Terbaik</p>
                    </div>
                </div>

                <div class="floating-box box-3">
                    <div class="box-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div class="box-info">
                        <h4>Fast Delivery</h4>
                        <p>2-5 Hari Kerja</p>
                    </div>
                </div>

                <!-- Slide Counter -->
                <div class="slide-counter">
                    <span id="slideNumber">1</span>/<span>5</span>
                </div>
            </div>
        </div>

        <!-- Product Variants Showcase -->
        <div class="variants-showcase">
            <div class="variants-grid" id="variantsGrid">
                <!-- Variants will be loaded by JavaScript -->
            </div>
        </div>
    </div>
</section>

<!-- Search & Filter Section -->
<section class="search-filter">
    <div class="container">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari produk...">
        </div>
        <div class="filter-section">
            <h3>Kategori</h3>
            <div class="category-buttons">
                <button class="category-btn active" data-category="all">Semua</button>
                <button class="category-btn" data-category="laptop">Laptop</button>
                <button class="category-btn" data-category="pc">PC Desktop</button>
                <button class="category-btn" data-category="gpu">GPU</button>
                <button class="category-btn" data-category="monitor">Monitor</button>
                <button class="category-btn" data-category="keyboard">Keyboard</button>
                <button class="category-btn" data-category="mouse">Mouse</button>
                <button class="category-btn" data-category="accessories">Aksesori</button>
            </div>
        </div>
    </div>
</section>

<!-- Key Features Section -->
<section class="key-features">
    <div class="container">
        <h2 class="section-title">Mengapa Pilih <?php echo SITE_NAME; ?>?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>100% Original</h3>
                <p>Semua produk dijamin original dari distributor resmi dengan garansi resmi</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Pengiriman Cepat</h3>
                <p>Pengiriman ke seluruh Indonesia dalam 2-5 hari kerja</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Customer Service 24/7</h3>
                <p>Tim support siap membantu Anda kapan saja tanpa henti</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Kebijakan Pengembalian</h3>
                <p>Pengembalian barang mudah dalam 30 hari jika tidak puas</p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products" id="products">
    <div class="container">
        <h2 class="section-title">Our Top Selling</h2>
        <div class="products-grid" id="productsGrid">
            <?php 
            // Category mapping
            $category_map = [
                1 => 'laptop',
                2 => 'pc', 
                3 => 'gpu',
                4 => 'monitor',
                5 => 'keyboard',
                6 => 'mouse',
                7 => 'accessories'
            ];
            
            foreach ($featured_products as $product): 
                $cat_slug = $category_map[$product['category_id']] ?? 'all';
            ?>
                <div class="product-card" data-category="<?php echo $cat_slug; ?>" data-product-id="<?php echo $product['id']; ?>">
                    <?php 
                    // Badge logic
                    $badge = '';
                    if ($product['views'] > 250) {
                        $badge = '<div class="product-badge hot">HOT</div>';
                    } elseif (strtotime($product['created_at']) > strtotime('-30 days')) {
                        $badge = '<div class="product-badge new">NEW</div>';
                    } elseif ($product['views'] > 200) {
                        $badge = '<div class="product-badge recommended">RECOMMENDED</div>';
                    }
                    echo $badge;
                    ?>
                    <img src="img/<?php echo clean($product['image']); ?>" alt="<?php echo clean($product['name']); ?>" class="product-image">
                    <div class="product-info">
                        <div class="product-category"><?php echo clean($product['brand']); ?></div>
                        <h3 class="product-name"><?php echo clean($product['name']); ?></h3>
                        <div class="product-rating">
                            <div class="stars">
                                <?php
                                $avg_rating = fetchOne("SELECT AVG(rating) as avg FROM reviews WHERE product_id = ? AND status = 'approved'", [$product['id']]);
                                $rating = round($avg_rating['avg'] ?? 4.5);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '★' : '☆';
                                }
                                ?>
                            </div>
                            <span class="rating-number">(<?php echo $product['views']; ?>)</span>
                        </div>
                        <div class="product-price">Rp <?php echo number_format($product['discount_price'] ?? $product['price'], 0, ',', '.'); ?></div>
                        <button class="add-btn" onclick="window.location.href='product_detail.php?id=<?php echo $product['id']; ?>'">Detail Produk</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Customer Review Section -->
<section class="reviews" id="reviews">
    <div class="container">
        <h2 class="section-title">Customer Review</h2>
        <div class="reviews-grid" id="reviewsGrid">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar"><?php echo strtoupper(substr($review['full_name'] ?? $review['username'], 0, 1)); ?></div>
                            <div class="reviewer-details">
                                <h4><?php echo clean($review['full_name'] ?? $review['username']); ?></h4>
                                <p><?php echo clean($review['product_name']); ?></p>
                            </div>
                        </div>
                        <div class="review-stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $review['rating'] ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <p class="review-text">"<?php echo clean($review['comment']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default reviews jika database kosong -->
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">B</div>
                        <div class="reviewer-details">
                            <h4>Budi Santoso</h4>
                            <p>Gamer Profesional</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Produk berkualitas tinggi dengan pengiriman cepat. Sangat puas dengan pelayanannya!"</p>
                </div>
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">S</div>
                        <div class="reviewer-details">
                            <h4>Siti Nurhaliza</h4>
                            <p>Content Creator</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Harga sangat kompetitif dan produk original. Rekomendasi untuk semua orang!"</p>
                </div>
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">A</div>
                        <div class="reviewer-details">
                            <h4>Ahmad Wijaya</h4>
                            <p>Profesional IT</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★☆</div>
                    <p class="review-text">"Pelayanan customer service responsif dan membantu. Puas dengan pembelian saya."</p>
                </div>
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">R</div>
                        <div class="reviewer-details">
                            <h4>Rina Hermawan</h4>
                            <p>Graphic Designer</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Kualitas packaging sangat baik, produk sampai dengan selamat dan sempurna!"</p>
                </div>
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">D</div>
                        <div class="reviewer-details">
                            <h4>Doni Pratama</h4>
                            <p>Tech Enthusiast</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Koleksi produk lengkap dan update. Akan membeli lagi di sini!"</p>
                </div>
                <div class="review-card">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">L</div>
                        <div class="reviewer-details">
                            <h4>Lisa Maulida</h4>
                            <p>Streamer</p>
                        </div>
                    </div>
                    <div class="review-stars">★★★★☆</div>
                    <p class="review-text">"Produk sesuai dengan deskripsi, garansi resmi terjamin. Mantap!"</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq" id="faq">
    <div class="container">
        <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Apakah produk dijamin original?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Ya, semua produk kami dijamin 100% original dari distributor resmi dengan garansi resmi dari brand.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Berapa lama pengiriman?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Pengiriman standar 2-5 hari kerja ke seluruh Indonesia. Ada juga opsi pengiriman express 1 hari.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Apa kebijakan pengembalian barang?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Kami menerima pengembalian barang dalam 30 hari jika barang tidak sesuai atau ada kerusakan. Syarat dan ketentuan berlaku.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Apakah ada garansi resmi?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Ya, semua produk dilengkapi dengan garansi resmi dari manufacturer sesuai dengan terms yang berlaku.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Bagaimana jika produk rusak saat pengiriman?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Hubungi customer service kami segera dengan bukti foto/video. Kami akan ganti atau refund tanpa biaya tambahan.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Berapa minimal pembelian?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Tidak ada minimal pembelian. Anda bisa membeli 1 produk atau lebih sesuai kebutuhan Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>



<?php require_once 'includes/footer.php'; ?>
