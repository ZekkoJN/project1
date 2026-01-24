<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/voucher_functions.php';

$page_title = "Order Success";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$order_id = $_GET['order'] ?? null;
$guest_order_id = $_GET['guest'] ?? null;
$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

$order = null;
$order_items = null;

if ($order_id && $user_id) {
    $order = fetchOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$order_id, $user_id]);
    if ($order) {
        $order_items = fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order_id]);
    }
} elseif ($guest_order_id) {
    $order = fetchOne("SELECT * FROM guest_orders WHERE id = ?", [$guest_order_id]);
    if ($order) {
        $order_items = fetchAll("SELECT * FROM guest_order_items WHERE guest_order_id = ?", [$guest_order_id]);
    }
}

if (!$order) {
    redirect('index.php');
}
?>

<style>
.success-container {
    max-width: 600px;
    margin: 60px auto;
    padding: 20px;
}

.success-box {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    padding: 40px;
    text-align: center;
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 20px;
}

.success-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.order-number {
    font-size: 1.3rem;
    color: #667eea;
    font-weight: 700;
    background: #f0f4ff;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.order-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #666;
}

.detail-value {
    font-weight: 600;
    color: #333;
}

.order-items {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}

.item-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.item-name {
    flex: 1;
}

.item-qty {
    text-align: center;
    width: 50px;
}

.item-price {
    text-align: right;
    width: 120px;
}

.member-info {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-left: 4px solid #667eea;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}

.btn-group {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    flex: 1;
    padding: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-secondary {
    background: #e9ecef;
    color: #333;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.status-badge {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-paid {
    background: #d4edda;
    color: #155724;
}

.total-box {
    background: white;
    border: 2px solid #667eea;
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.total-final {
    font-size: 1.3rem;
    font-weight: 700;
    color: #667eea;
    border-top: 2px solid #ddd;
    padding-top: 10px;
    margin-top: 10px;
}
</style>

<div class="success-container">
    <div class="success-box">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Pesanan Berhasil Dibuat!</h1>
        
        <p style="color: #666; margin-bottom: 20px;">
            Terima kasih telah berbelanja di TechHub. Pesanan Anda telah kami terima dan <strong style="color: #28a745;">pembayaran sudah diterima</strong>. Pesanan akan segera kami proses.
        </p>
        
        <div class="order-number">
            <i class="fas fa-receipt"></i> <?php echo $order['order_number']; ?>
        </div>
        
        <!-- Member Info -->
        <?php if ($user_id && !$guest_order_id): ?>
            <div class="member-info">
                <strong><i class="fas fa-crown"></i> Member Benefits</strong>
                <p style="margin: 10px 0 0 0; font-size: 0.95rem;">
                    Anda mendapat <strong>2% cashback</strong> dari total pembelian ini. Cashback akan ditambahkan ke akun Anda segera.
                </p>
            </div>
        <?php elseif ($guest_order_id): ?>
            <div class="member-info" style="background: linear-gradient(135deg, #51cf6615 0%, #37b24d15 100%); border-left-color: #37b24d;">
                <strong><i class="fas fa-gift"></i> Dapatkan Benefit Member!</strong>
                <p style="margin: 10px 0 0 0; font-size: 0.95rem;">
                    Terima kasih sebagai guest customer. Untuk pembelian berikutnya, 
                    <a href="register.php" style="color: #37b24d; text-decoration: underline; font-weight: 600;">daftar sekarang</a> 
                    dan dapatkan <strong>voucher diskon 10%</strong> + <strong>program cashback eksklusif</strong>!
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Order Details -->
        <div class="order-details">
            <h3 style="margin-top: 0; text-align: left;">Detail Pesanan</h3>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Order:</span>
                <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-pending">
                        <?php 
                        $status_map = [
                            'pending' => 'Menunggu Pembayaran',
                            'processing' => 'Sedang Diproses',
                            'shipped' => 'Sedang Dikirim',
                            'delivered' => 'Terkirim',
                            'cancelled' => 'Dibatalkan'
                        ];
                        echo $status_map[$order['status']] ?? $order['status'];
                        ?>
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status Pembayaran:</span>
                <span class="detail-value">
                    <span class="status-badge <?php echo $order['payment_status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                        <?php 
                        $payment_status_map = [
                            'unpaid' => 'Belum Dibayar',
                            'paid' => '✓ Sudah Dibayar',
                            'refunded' => 'Dikembalikan'
                        ];
                        echo $payment_status_map[$order['payment_status']] ?? $order['payment_status'];
                        ?>
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Metode Pembayaran:</span>
                <span class="detail-value">
                    <?php 
                    $method_map = [
                        'transfer' => 'Transfer Bank',
                        'cod' => 'COD (Cash on Delivery)',
                        'ewallet' => 'E-Wallet'
                    ];
                    echo $method_map[$order['payment_method']] ?? $order['payment_method'];
                    ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Alamat Pengiriman:</span>
                <span class="detail-value"><?php echo clean($order['shipping_address']); ?></span>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-items">
            <h3 style="margin-top: 0; text-align: left; margin-bottom: 15px;">Barang yang Dipesan</h3>
            
            <?php foreach ($order_items as $item): ?>
                <div class="item-row">
                    <div class="item-name">
                        <strong><?php echo clean($item['product_name']); ?></strong>
                    </div>
                    <div class="item-qty">
                        <?php echo $item['quantity']; ?>x
                    </div>
                    <div class="item-price">
                        <?php echo formatRupiah($item['price']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Total -->
            <div class="total-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <strong><?php echo formatRupiah($order['total_amount']); ?></strong>
                </div>
                
                <?php if ($order['discount_amount'] > 0): ?>
                    <div class="total-row">
                        <span>Diskon/Cashback</span>
                        <strong style="color: #28a745;">-<?php echo formatRupiah($order['discount_amount']); ?></strong>
                    </div>
                <?php endif; ?>
                
                <div class="total-row total-final">
                    <span>Total Bayar</span>
                    <span><?php echo formatRupiah($order['final_amount']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="btn-group">
            <?php if ($user_id && !$guest_order_id): ?>
                <!-- Tombol untuk Member -->
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Pesanan Saya
                </a>
            <?php else: ?>
                <!-- Tombol untuk Guest -->
                <a href="register.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar Member
                </a>
            <?php endif; ?>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Kembali Berbelanja
            </a>
        </div>
        
        <?php if ($guest_order_id): ?>
            <p style="color: #666; margin-top: 30px; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> <strong>Nomor order Anda: <?php echo $order['order_number']; ?></strong><br>
                Simpan nomor order ini untuk tracking pesanan Anda. Invoice telah dikirim ke <strong><?php echo clean($order['guest_email']); ?></strong>
            </p>
            <p style="color: #10b981; margin-top: 15px; font-size: 0.95rem; background: #f0fdf4; padding: 15px; border-radius: 8px; border-left: 4px solid #10b981;">
                <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Daftar sebagai member untuk mendapatkan voucher diskon 10%, cashback 2%, dan akses mudah ke riwayat pesanan Anda!
            </p>
        <?php else: ?>
            <p style="color: #666; margin-top: 30px; font-size: 0.9rem;">
                <i class="fas fa-check-circle"></i> Pembayaran telah diterima (Demo Mode). 
                Invoice telah dikirim ke email Anda.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
