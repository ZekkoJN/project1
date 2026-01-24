<?php
require_once 'config/config.php';
require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$page_title = "Pesanan Saya";

// Fetch user orders
$orders = fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
.orders-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.order-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

body.dark-mode .order-card,
html.dark-mode .order-card {
    background: #2d2d2d;
    box-shadow: 0 2px 15px rgba(0,0,0,0.5);
}

.order-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-header h3,
.order-header p {
    color: white !important;
}

.order-body {
    padding: 25px 30px;
    color: #333;
}

body.dark-mode .order-body,
html.dark-mode .order-body {
    color: #e0e0e0;
}

.order-items {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
    margin-top: 20px;
}

body.dark-mode .order-items,
html.dark-mode .order-items {
    border-top: 1px solid #444;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

body.dark-mode .order-item,
html.dark-mode .order-item {
    border-bottom: 1px solid #444;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-pending { background: #fff3cd; color: #856404; }
.badge-processing { background: #cce5ff; color: #004085; }
.badge-shipped { background: #d1ecf1; color: #0c5460; }
.badge-delivered { background: #d4edda; color: #155724; }
.badge-cancelled { background: #f8d7da; color: #721c24; }

.badge-unpaid { background: #f8d7da; color: #721c24; }
.badge-paid { background: #d4edda; color: #155724; }

/* Dark mode badges */
body.dark-mode .badge-pending,
html.dark-mode .badge-pending {
    background: #664d00 !important;
    color: #ffd700 !important;
}

body.dark-mode .badge-processing,
html.dark-mode .badge-processing {
    background: #003d82 !important;
    color: #66c2ff !important;
}

body.dark-mode .badge-shipped,
html.dark-mode .badge-shipped {
    background: #004d66 !important;
    color: #66d9ff !important;
}

body.dark-mode .badge-delivered,
html.dark-mode .badge-delivered {
    background: #1a4d2e !important;
    color: #66ff99 !important;
}

body.dark-mode .badge-cancelled,
html.dark-mode .badge-cancelled {
    background: #661a1a !important;
    color: #ff9999 !important;
}

body.dark-mode .badge-unpaid,
html.dark-mode .badge-unpaid {
    background: #661a1a !important;
    color: #ff9999 !important;
}

body.dark-mode .badge-paid,
html.dark-mode .badge-paid {
    background: #1a4d2e !important;
    color: #66ff99 !important;
}

.btn {
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.85rem;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 15px;
}

body.dark-mode .empty-state,
html.dark-mode .empty-state {
    background: #2d2d2d;
    color: #e0e0e0;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

body.dark-mode .empty-state i,
html.dark-mode .empty-state i {
    color: #555;
}

h1 {
    color: #333;
}

body.dark-mode h1,
html.dark-mode h1 {
    color: #ffffff;
}

h3 {
    color: #333;
}

body.dark-mode h3,
html.dark-mode h3 {
    color: #ffffff;
}

p {
    color: #666;
}

body.dark-mode p,
html.dark-mode p {
    color: #ccc;
}

h4 {
    color: #667eea !important;
}

body.dark-mode h4,
html.dark-mode h4 {
    color: #667eea !important;
}

strong {
    color: #333;
}

body.dark-mode strong,
html.dark-mode strong {
    color: #e0e0e0;
}
</style>

<div class="orders-container">
    <h1 style="margin-bottom: 30px;"><i class="fas fa-box"></i> Pesanan Saya</h1>
    
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $items = fetchAll("SELECT oi.*, p.image FROM order_items oi 
                              LEFT JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?", [$order['id']]);
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h3 style="margin: 0 0 5px 0;">Order #<?php echo $order['order_number']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">
                            <?php echo formatDate($order['created_at']); ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge badge-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                        <span class="badge badge-<?php echo $order['payment_status']; ?>" style="margin-left: 10px;">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="order-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                        <div>
                            <h4 style="color: #667eea; margin: 0 0 10px 0;">
                                <i class="fas fa-credit-card"></i> Pembayaran
                            </h4>
                            <p style="margin: 0;"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                        </div>
                        <div>
                            <h4 style="color: #667eea; margin: 0 0 10px 0;">
                                <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                            </h4>
                            <p style="margin: 0;"><?php echo clean(substr($order['shipping_address'], 0, 50)) . '...'; ?></p>
                        </div>
                        <div>
                            <h4 style="color: #667eea; margin: 0 0 10px 0;">
                                <i class="fas fa-dollar-sign"></i> Total
                            </h4>
                            <p style="margin: 0; font-size: 1.3rem; font-weight: 600; color: #667eea;">
                                <?php echo formatRupiah($order['final_amount']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <h4 style="margin: 0 0 15px 0;">Item Pesanan (<?php echo count($items); ?>)</h4>
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <?php if ($item['image']): ?>
                                    <img src="img/<?php echo clean($item['image']); ?>" alt="">
                                <?php else: ?>
                                    <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: #ccc; font-size: 2rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 5px 0;"><?php echo clean($item['product_name']); ?></h4>
                                    <p style="margin: 0; color: #888;">
                                        <?php echo formatRupiah($item['price']); ?> x <?php echo $item['quantity']; ?>
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <strong><?php echo formatRupiah($item['subtotal']); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                        <a href="pdf/invoice.php?order=<?php echo $order['id']; ?>" 
                           target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-pdf"></i> Lihat Invoice
                        </a>
                        <?php if ($order['payment_status'] === 'unpaid' && $order['status'] !== 'cancelled'): ?>
                            <button class="btn btn-sm" style="background: #28a745; color: white; margin-left: 10px;">
                                <i class="fas fa-upload"></i> Upload Bukti Transfer
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h2>Belum Ada Pesanan</h2>
            <p style="color: #888; margin: 20px 0;">Anda belum melakukan pemesanan apapun</p>
            <a href="products.php" class="cta-btn">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
