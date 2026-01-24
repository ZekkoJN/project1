<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Laporan";

// Date range filter
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Sales statistics
$sales_data = fetchOne("SELECT 
    COUNT(*) as total_orders,
    SUM(final_amount) as total_revenue,
    AVG(final_amount) as avg_order_value,
    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_orders
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?", [$start_date, $end_date]);

// Top products
$top_products = fetchAll("SELECT p.name, p.image, 
    COUNT(oi.id) as total_sold,
    SUM(oi.quantity) as quantity_sold,
    SUM(oi.subtotal) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY quantity_sold DESC
    LIMIT 10", [$start_date, $end_date]);

// Daily sales
$daily_sales = fetchAll("SELECT 
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(final_amount) as revenue
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date DESC", [$start_date, $end_date]);

// Customer statistics
$customer_stats = fetchOne("SELECT 
    COUNT(DISTINCT user_id) as total_customers,
    COUNT(DISTINCT CASE WHEN DATE(orders.created_at) BETWEEN ? AND ? THEN user_id END) as active_customers
    FROM orders", [$start_date, $end_date]);

// Product statistics
$product_stats = fetchOne("SELECT 
    COUNT(*) as total_products,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
    SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as low_stock_products
    FROM products");

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

.page-header {
    background: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.page-header h1 {
    color: #2d3748;
    margin-bottom: 5px;
}

.page-header p {
    color: #718096;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}

.card h3 {
    margin-bottom: 20px;
    color: #2d3748;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.stat-card h4 {
    color: #718096;
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-card .value {
    font-size: 28px;
    font-weight: bold;
    color: #2d3748;
}

.stat-card.revenue {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.revenue h4,
.stat-card.revenue .value {
    color: white;
}

.filter-form {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #2d3748;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 5px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

table th {
    background: #f7fafc;
    color: #2d3748;
    font-weight: 600;
}

table tr:hover {
    background: #f7fafc;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-info img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <div class="admin-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Laporan Penjualan</h1>
            <p>Analisis dan statistik bisnis</p>
        </div>

        <div class="card">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card revenue">
                <h4>Total Pendapatan</h4>
                <div class="value">Rp <?php echo number_format($sales_data['total_revenue'] ?? 0, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <h4>Total Pesanan</h4>
                <div class="value"><?php echo $sales_data['total_orders'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h4>Rata-rata Nilai Order</h4>
                <div class="value">Rp <?php echo number_format($sales_data['avg_order_value'] ?? 0, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card">
                <h4>Pesanan Dibayar</h4>
                <div class="value"><?php echo $sales_data['paid_orders'] ?? 0; ?></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Pelanggan</h4>
                <div class="value"><?php echo $customer_stats['total_customers'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h4>Pelanggan Aktif</h4>
                <div class="value"><?php echo $customer_stats['active_customers'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h4>Total Produk</h4>
                <div class="value"><?php echo $product_stats['total_products'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h4>Stok Rendah</h4>
                <div class="value"><?php echo $product_stats['low_stock_products'] ?? 0; ?></div>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-trophy"></i> Top 10 Produk Terlaris</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Terjual</th>
                            <th>Quantity</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_products)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">
                                <p style="color: #718096;">Belum ada data penjualan dalam periode ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($top_products as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="<?php echo BASE_URL; ?>/img/<?php echo $product['image']; ?>" 
                                             alt="<?php echo $product['name']; ?>">
                                        <span><?php echo $product['name']; ?></span>
                                    </div>
                                </td>
                                <td><?php echo $product['total_sold']; ?>x</td>
                                <td><?php echo $product['quantity_sold']; ?> unit</td>
                                <td>Rp <?php echo number_format($product['revenue'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-calendar"></i> Penjualan Harian</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Pesanan</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daily_sales)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px;">
                                <p style="color: #718096;">Belum ada data penjualan dalam periode ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($daily_sales as $day): ?>
                            <tr>
                                <td><?php echo date('d F Y', strtotime($day['date'])); ?></td>
                                <td><?php echo $day['orders']; ?> pesanan</td>
                                <td>Rp <?php echo number_format($day['revenue'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
