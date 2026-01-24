<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get date range
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch report data
$orders = fetchAll("SELECT o.*, u.username 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    WHERE DATE(o.created_at) BETWEEN ? AND ? 
                    ORDER BY o.created_at DESC", [$start_date, $end_date]);

$total_revenue = fetchOne("SELECT SUM(final_amount) as total 
                           FROM orders 
                           WHERE payment_status = 'paid' 
                           AND DATE(created_at) BETWEEN ? AND ?", 
                           [$start_date, $end_date])['total'] ?? 0;

$total_orders = count($orders);

// Top products
$top_products = fetchAll("SELECT oi.product_name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue 
                          FROM order_items oi 
                          JOIN orders o ON oi.order_id = o.id 
                          WHERE DATE(o.created_at) BETWEEN ? AND ? 
                          GROUP BY oi.product_id 
                          ORDER BY total_sold DESC 
                          LIMIT 10", [$start_date, $end_date]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - <?php echo formatDate($start_date); ?> s/d <?php echo formatDate($end_date); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .report-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 40px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .report-header h1 {
            color: #667eea;
            margin: 0 0 10px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            color: #667eea;
            font-size: 2rem;
        }
        .stat-box p {
            margin: 10px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        h2 {
            color: #667eea;
            margin: 40px 0 20px 0;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>LAPORAN PENJUALAN</h1>
            <p><?php echo SITE_NAME; ?></p>
            <p>Periode: <?php echo formatDate($start_date); ?> s/d <?php echo formatDate($end_date); ?></p>
            <p>Dibuat: <?php echo date('d M Y H:i'); ?></p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <h3><?php echo $total_orders; ?></h3>
                <p>Total Pesanan</p>
            </div>
            <div class="stat-box">
                <h3><?php echo formatRupiah($total_revenue); ?></h3>
                <p>Total Pendapatan</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $total_orders > 0 ? formatRupiah($total_revenue / $total_orders) : 'Rp 0'; ?></h3>
                <p>Rata-rata per Order</p>
            </div>
        </div>
        
        <h2>Produk Terlaris</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th class="text-right">Terjual</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($top_products as $product): 
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo clean($product['product_name']); ?></td>
                        <td class="text-right"><?php echo $product['total_sold']; ?> unit</td>
                        <td class="text-right"><?php echo formatRupiah($product['revenue']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Daftar Pesanan</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['order_number']; ?></td>
                        <td><?php echo clean($order['username']); ?></td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                        <td class="text-right"><?php echo formatRupiah($order['final_amount']); ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><?php echo ucfirst($order['payment_status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="no-print" style="text-align: center; margin-top: 40px;">
            <button onclick="window.print()" style="padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Print Laporan
            </button>
            <a href="../admin/reports.php" style="margin-left: 10px; padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>
