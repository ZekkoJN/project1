<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Admin Dashboard";

// Get statistics
$total_products = fetchOne("SELECT COUNT(*) as count FROM products")['count'];
$total_orders = fetchOne("SELECT COUNT(*) as count FROM orders")['count'];
$total_users = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")['count'];
$total_revenue = fetchOne("SELECT SUM(final_amount) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0;

// Recent orders
$recent_orders = fetchAll("SELECT o.*, u.username FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           ORDER BY o.created_at DESC LIMIT 10");

// Low stock products
$low_stock = fetchAll("SELECT * FROM products WHERE stock < 5 AND status = 'active' ORDER BY stock ASC");

require_once '../includes/header.php';
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f7fa;
}

.admin-layout {
    display: flex;
    min-height: 100vh;
}

.admin-content {
    flex: 1;
    margin-left: 260px;
    padding: 30px;
}

.admin-header {
    background: white;
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-info h3 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 5px;
}

.stat-info p {
    color: #888;
    font-size: 0.9rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.blue { background: #e3f2fd; color: #2196f3; }
.stat-icon.green { background: #e8f5e9; color: #4caf50; }
.stat-icon.orange { background: #fff3e0; color: #ff9800; }
.stat-icon.purple { background: #f3e5f5; color: #9c27b0; }

.content-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-header h2 {
    font-size: 1.3rem;
    color: #333;
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
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

th {
    background: #f8f9fa;
    font-weight: 600;
    color: #555;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-pending { background: #fff3cd; color: #856404; }
.badge-processing { background: #cce5ff; color: #004085; }
.badge-shipped { background: #d1ecf1; color: #0c5460; }
.badge-delivered { background: #d4edda; color: #155724; }
.badge-cancelled { background: #f8d7da; color: #721c24; }
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <div>
                <h1>Dashboard</h1>
                <p>Selamat datang, <?php echo clean($_SESSION['username']); ?>!</p>
            </div>
            <div>
                <span><?php echo date('d M Y, H:i'); ?></span>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Produk</p>
                </div>
                <div class="stat-icon blue">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="stat-icon green">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="stat-icon orange">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo formatRupiah($total_revenue); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
                <div class="stat-icon purple">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-shopping-cart"></i> Pesanan Terbaru</h2>
                <a href="orders.php" class="btn btn-primary">Lihat Semua</a>
            </div>
            <?php if (count($recent_orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_number']; ?></td>
                                <td><?php echo clean($order['username']); ?></td>
                                <td><?php echo formatRupiah($order['final_amount']); ?></td>
                                <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo formatDate($order['created_at']); ?></td>
                                <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">Detail</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">Belum ada pesanan</p>
            <?php endif; ?>
        </div>

        <!-- Low Stock Alert -->
        <?php if (count($low_stock) > 0): ?>
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> Stok Rendah</h2>
                    <a href="products.php" class="btn btn-primary">Kelola Produk</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Brand</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock as $product): ?>
                            <tr>
                                <td><?php echo clean($product['name']); ?></td>
                                <td><?php echo clean($product['brand']); ?></td>
                                <td style="color: #f44336; font-weight: 600;"><?php echo $product['stock']; ?> unit</td>
                                <td><?php echo formatRupiah($product['price']); ?></td>
                                <td><a href="product_edit.php?id=<?php echo $product['id']; ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
