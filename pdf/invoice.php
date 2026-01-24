<?php
require_once '../config/config.php';
require_once '../config/database.php';
// require_once '../vendor/autoload.php'; // For TCPDF/mPDF if using Composer (not used in this project)

// Simple PDF generation without external library
class SimplePDF {
    public function generateInvoice($order_id, $is_guest = false) {
        if ($is_guest) {
            // Guest order
            $order = fetchOne("SELECT * FROM guest_orders WHERE id = ?", [$order_id]);
            
            if (!$order) {
                die("Order not found");
            }
            
            // Add customer info for consistency
            $order['username'] = $order['guest_name'];
            $order['full_name'] = $order['guest_name'];
            $order['email'] = $order['guest_email'];
            $order['phone'] = $order['guest_phone'];
            
            $items = fetchAll("SELECT * FROM guest_order_items WHERE guest_order_id = ?", [$order_id]);
        } else {
            // Member order
            $order = fetchOne("SELECT o.*, u.username, u.full_name, u.email 
                               FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               WHERE o.id = ?", [$order_id]);
            
            if (!$order) {
                die("Order not found");
            }
            
            $items = fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order_id]);
        }
        
        // Generate HTML for PDF
        $html = $this->getInvoiceHTML($order, $items, $is_guest);
        
        // Return HTML that can be printed as PDF
        return $html;
    }
    
    private function getInvoiceHTML($order, $items, $is_guest = false) {
        ob_start();
        
        // Determine back link based on user role
        $back_link = isAdmin() ? '../admin/orders.php' : '../orders.php';
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice #<?php echo $order['order_number']; ?></title>
            <style>
                * { box-sizing: border-box; }
                html, body {
                    height: 100%;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 5px;
                    color: #333;
                    background: white;
                    font-size: 10.5px;
                    display: flex;
                    flex-direction: column;
                }
                .invoice-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 10px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                }
                .invoice-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 5px;
                    border-bottom: 1.5px solid #667eea;
                    padding-bottom: 5px;
                    gap: 15px;
                }
                .company-info {
                    flex: 1;
                }
                .company-info h1 {
                    color: #667eea;
                    margin: 0 0 1px 0;
                    font-size: 1.1rem;
                }
                .company-info p {
                    margin: 0.5px 0;
                    font-size: 0.7rem;
                    line-height: 1.15;
                }
                .invoice-info {
                    text-align: right;
                    flex: 0 0 auto;
                }
                .invoice-info h2 {
                    color: #667eea;
                    margin: 0 0 1px 0;
                    font-size: 0.95rem;
                }
                .invoice-info p {
                    margin: 0.5px 0;
                    font-size: 0.7rem;
                    line-height: 1.15;
                }
                .info-section {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 8px;
                    gap: 10px;
                }
                .info-box {
                    flex: 1;
                }
                .info-box h3 {
                    color: #667eea;
                    margin: 0 0 2px 0;
                    font-size: 0.75rem;
                    font-weight: 600;
                }
                .info-box p {
                    margin: 0.5px 0;
                    line-height: 1.15;
                    font-size: 0.7rem;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 6px 0;
                    font-size: 0.75rem;
                }
                thead {
                    background: #667eea;
                }
                th {
                    color: white;
                    padding: 4px 5px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 0.7rem;
                }
                td {
                    padding: 3px 5px;
                    border-bottom: 0.5px solid #e0e0e0;
                    font-size: 0.75rem;
                }
                tbody tr:last-child td {
                    border-bottom: none;
                }
                .text-right {
                    text-align: right;
                }
                .totals {
                    text-align: right;
                    margin-top: 5px;
                    margin-bottom: 6px;
                }
                .totals table {
                    margin-left: auto;
                    width: 250px;
                    margin: 0;
                    font-size: 0.75rem;
                }
                .totals td {
                    border: none;
                    padding: 2px 5px;
                    font-size: 0.75rem;
                }
                .totals td:first-child {
                    text-align: left;
                }
                .totals td:last-child {
                    text-align: right;
                    font-weight: 600;
                }
                .total-row {
                    font-size: 0.8rem;
                    font-weight: bold;
                    color: #667eea;
                    border-top: 1px solid #667eea;
                    padding-top: 1px !important;
                }
                .footer {
                    margin-top: auto;
                    padding-top: 3px;
                    border-top: 0.5px solid #e0e0e0;
                    text-align: center;
                    color: #888;
                    font-size: 0.65rem;
                }
                .footer p {
                    margin: 0.5px 0;
                    line-height: 1.1;
                }
                .status-badge {
                    display: inline-block;
                    padding: 0.5px 6px;
                    border-radius: 10px;
                    font-size: 0.6rem;
                    font-weight: 600;
                }
                .status-pending { background: #fff3cd; color: #856404; }
                .status-paid { background: #d4edda; color: #155724; }
                @media print {
                    body { 
                        margin: 0; 
                        padding: 2px;
                        font-size: 10px;
                    }
                    .invoice-container {
                        padding: 6px;
                        max-width: 100%;
                    }
                    .no-print { display: none !important; }
                    table { page-break-inside: avoid; }
                    .invoice-header { page-break-inside: avoid; }
                    .info-section { page-break-inside: avoid; }
                    .totals { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                <div class="invoice-header">
                    <div class="company-info">
                        <h1><?php echo SITE_NAME; ?></h1>
                        <p>Toko Elektronik Komputer & Laptop</p>
                        <p>Semarang, Indonesia</p>
                        <p>Email: <?php echo ADMIN_EMAIL; ?></p>
                        <p>Telp: +62 812-3456-7890</p>
                    </div>
                    <div class="invoice-info">
                        <h2>INVOICE</h2>
                        <p><strong>#<?php echo $order['order_number']; ?></strong></p>
                        <p>Tanggal: <?php echo formatDate($order['created_at']); ?></p>
                        <p>
                            <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                <?php echo strtoupper($order['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="info-section">
                    <div class="info-box">
                        <h3>Informasi Pelanggan</h3>
                        <p><strong><?php echo clean($order['full_name'] ?? $order['username']); ?></strong></p>
                        <p><?php echo clean($order['email']); ?></p>
                    </div>
                    <div class="info-box">
                        <h3>Alamat Pengiriman</h3>
                        <p><?php echo nl2br(clean($order['shipping_address'])); ?></p>
                        <p>Telp: <?php echo clean($order['shipping_phone']); ?></p>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($items as $item): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo clean($item['product_name']); ?></td>
                                <td class="text-right"><?php echo formatRupiah($item['price']); ?></td>
                                <td class="text-right"><?php echo $item['quantity']; ?></td>
                                <td class="text-right"><?php echo formatRupiah($item['subtotal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="totals">
                    <table>
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right"><strong><?php echo formatRupiah($order['total_amount']); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Diskon:</td>
                            <td class="text-right"><strong><?php echo formatRupiah($order['discount_amount']); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Ongkir:</td>
                            <td class="text-right"><strong>Rp 0</strong></td>
                        </tr>
                        <tr class="total-row">
                            <td>TOTAL:</td>
                            <td class="text-right"><?php echo formatRupiah($order['final_amount']); ?></td>
                        </tr>
                    </table>
                </div>
                
                <?php if ($order['payment_method'] === 'transfer'): ?>
                    <div style="background: #f8f9fa; padding: 4px; border-radius: 3px; margin-top: 4px; font-size: 0.65rem;">
                        <p style="margin: 0; font-weight: 600; color: #667eea;">Bank BCA • Rek: 1234567890 • A/N: TechHub Indonesia</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($order['notes']): ?>
                    <div style="margin-top: 3px; font-size: 0.65rem;">
                        <p style="margin: 0;"><strong style="color: #667eea;">Catatan:</strong> <?php echo nl2br(clean($order['notes'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="footer">
                    <p><strong>Terima kasih atas pembelian Anda!</strong></p>
                    <p>Invoice dibuat oleh sistem.</p>
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
                </div>
                
                <div class="no-print" style="text-align: center; margin-top: 10px;">
                    <button onclick="window.print()" style="padding: 8px 20px; background: #667eea; color: white; border: none; border-radius: 4px; font-size: 0.85rem; cursor: pointer;">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="<?php echo $back_link; ?>" style="margin-left: 8px; padding: 8px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 0.85rem;">
                        Kembali
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}

// Check if order ID is provided
if (!isset($_GET['order']) && !isset($_GET['guest'])) {
    die("Order ID required");
}

$is_guest = isset($_GET['guest']);
$order_id = $is_guest ? (int)$_GET['guest'] : (int)$_GET['order'];

// Verify order belongs to user (if not admin)
if (!isAdmin()) {
    if ($is_guest) {
        // For guest orders, just verify it exists
        $order = fetchOne("SELECT * FROM guest_orders WHERE id = ?", [$order_id]);
    } else {
        // For member orders, verify ownership
        $order = fetchOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$order_id, $_SESSION['user_id']]);
    }
    
    if (!$order) {
        die("Unauthorized access or order not found");
    }
}

$pdf = new SimplePDF();
echo $pdf->generateInvoice($order_id, $is_guest);
