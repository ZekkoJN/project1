<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = clean($_POST['status']);
    $order_type = clean($_POST['order_type']); // 'member' or 'guest'
    
    if ($order_type === 'guest') {
        execute("UPDATE guest_orders SET status = ?, updated_at = NOW() WHERE id = ?", [$new_status, $order_id]);
    } else {
        execute("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?", [$new_status, $order_id]);
    }
    
    $_SESSION['success'] = 'Status pesanan berhasil diupdate!';
    header('Location: orders.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filter by status
$status_filter = isset($_GET['status']) ? clean($_GET['status']) : 'all';
$where_clause = $status_filter !== 'all' ? "WHERE status = '$status_filter'" : '';

// Fetch orders from both tables (member and guest)
// UNION query to combine member orders and guest orders
$orders_query = "
    SELECT 
        o.id,
        o.order_number,
        o.total_amount,
        o.discount_amount,
        o.final_amount,
        o.status,
        o.payment_method,
        o.payment_status,
        o.created_at,
        u.username,
        u.email,
        u.full_name,
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count,
        'member' as order_type
    FROM orders o
    JOIN users u ON o.user_id = u.id
    
    UNION ALL
    
    SELECT 
        go.id,
        go.order_number,
        go.total_amount,
        go.discount_amount,
        go.final_amount,
        go.status,
        go.payment_method,
        go.payment_status,
        go.created_at,
        go.guest_name as username,
        go.guest_email as email,
        go.guest_name as full_name,
        (SELECT COUNT(*) FROM guest_order_items WHERE guest_order_id = go.id) as item_count,
        'guest' as order_type
    FROM guest_orders go
";

// Add status filter if needed
if ($status_filter !== 'all') {
    $orders_query = "SELECT * FROM ($orders_query) as combined_orders WHERE status = '$status_filter' ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
} else {
    $orders_query .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
}

$orders = fetchAll($orders_query);

// Count total orders from both tables
if ($status_filter !== 'all') {
    $total_member = fetchOne("SELECT COUNT(*) as total FROM orders WHERE status = '$status_filter'")['total'];
    $total_guest = fetchOne("SELECT COUNT(*) as total FROM guest_orders WHERE status = '$status_filter'")['total'];
    $total_orders = $total_member + $total_guest;
} else {
    $total_member = fetchOne("SELECT COUNT(*) as total FROM orders")['total'];
    $total_guest = fetchOne("SELECT COUNT(*) as total FROM guest_orders")['total'];
    $total_orders = $total_member + $total_guest;
}
$total_pages = ceil($total_orders / $per_page);

// Order statistics (combine member and guest orders)
$stats = [
    'pending' => fetchOne("SELECT (
        (SELECT COUNT(*) FROM orders WHERE status = 'pending') + 
        (SELECT COUNT(*) FROM guest_orders WHERE status = 'pending')
    ) as count")['count'],
    'processing' => fetchOne("SELECT (
        (SELECT COUNT(*) FROM orders WHERE status = 'processing') + 
        (SELECT COUNT(*) FROM guest_orders WHERE status = 'processing')
    ) as count")['count'],
    'shipped' => fetchOne("SELECT (
        (SELECT COUNT(*) FROM orders WHERE status = 'shipped') + 
        (SELECT COUNT(*) FROM guest_orders WHERE status = 'shipped')
    ) as count")['count'],
    'completed' => fetchOne("SELECT (
        (SELECT COUNT(*) FROM orders WHERE status = 'completed') + 
        (SELECT COUNT(*) FROM guest_orders WHERE status = 'completed')
    ) as count")['count'],
    'cancelled' => fetchOne("SELECT (
        (SELECT COUNT(*) FROM orders WHERE status = 'cancelled') + 
        (SELECT COUNT(*) FROM guest_orders WHERE status = 'cancelled')
    ) as count")['count']
];

$page_title = "Manajemen Pesanan";
require_once '../includes/header.php';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body { 
        background: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }
    .admin-sidebar {
        width: 260px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 20px 0;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 999;
    }
    .admin-brand {
        padding: 0 20px 30px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        text-align: center;
    }
    .admin-brand h2 {
        font-size: 1.5rem;
    }
    .admin-menu {
        list-style: none;
        padding: 20px 0;
    }
    .admin-menu li {
        margin-bottom: 5px;
    }
    .admin-menu a {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }
    .admin-menu a:hover, .admin-menu a.active {
        background: rgba(255,255,255,0.2);
        padding-left: 30px;
    }
    .admin-menu i {
        margin-right: 15px;
        width: 20px;
    }
    .admin-content {
        flex: 1;
        margin-left: 260px;
        padding: 30px;
    }
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
    }
    .admin-header {
        background: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary-color);
    }
    .stat-label { color: #666; font-size: 0.9rem; }
    .stat-value { font-size: 2rem; font-weight: bold; color: #333; margin: 10px 0; }
    .filter-bar {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .filter-bar > strong {
        font-size: 1rem;
        color: #333;
        min-width: fit-content;
    }
    .filter-btn {
        padding: 12px 24px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        font-weight: 600;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        position: relative;
    }
    .filter-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    .filter-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }
    .filter-btn i {
        font-size: 1.1rem;
    }
    .filter-count {
        display: inline-block;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-left: 4px;
    }
    .filter-btn.active .filter-count {
        background: rgba(255,255,255,0.3);
        color: white;
    }
    .orders-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
    }
    td {
        padding: 15px;
        border-top: 1px solid #e0e0e0;
        vertical-align: middle;
    }
    td:last-child {
        text-align: center;
        white-space: normal;
        min-width: 250px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #d1ecf1; color: #0c5460; }
    .status-shipped { background: #d4edda; color: #155724; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .action-btn {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        margin: 0 4px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-view { 
        background: #17a2b8; 
        color: white; 
    }
    .btn-view:hover {
        background: #138496;
    }
    .btn-update { 
        background: #28a745; 
        color: white; 
    }
    .btn-update:hover {
        background: #218838;
    }
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }
    .page-btn {
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 5px;
        cursor: pointer;
    }
    .page-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
    }
    .modal.active { display: flex; align-items: center; justify-content: center; }
    .modal-content {
        background: white;
        padding: 40px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    .modal-content h2 {
        margin: 0 0 25px 0;
        font-size: 1.5rem;
        color: #333;
    }
        max-width: 500px;
        width: 90%;
    }
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-container">
            <div class="admin-header">
                <h1>Manajemen Pesanan</h1>
                <p>Kelola dan update status pesanan pelanggan</p>
            </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?php echo $stats['pending']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Processing</div>
            <div class="stat-value"><?php echo $stats['processing']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Shipped</div>
            <div class="stat-value"><?php echo $stats['shipped']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value"><?php echo $stats['completed']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Cancelled</div>
            <div class="stat-value"><?php echo $stats['cancelled']; ?></div>
        </div>
    </div>

    <div class="filter-bar">
        <strong><i class="fas fa-filter"></i> Filter Status:</strong>
        <button class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>" 
                onclick="window.location.href='orders.php?status=all'">
            <i class="fas fa-list"></i> Semua
            <span class="filter-count"><?php echo $stats['pending'] + $stats['processing'] + $stats['shipped'] + $stats['completed'] + $stats['cancelled']; ?></span>
        </button>
        <button class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>" 
                onclick="window.location.href='orders.php?status=pending'">
            <i class="fas fa-clock"></i> Pending
            <span class="filter-count"><?php echo $stats['pending']; ?></span>
        </button>
        <button class="filter-btn <?php echo $status_filter === 'processing' ? 'active' : ''; ?>" 
                onclick="window.location.href='orders.php?status=processing'">
            <i class="fas fa-hourglass-half"></i> Processing
            <span class="filter-count"><?php echo $stats['processing']; ?></span>
        </button>
        <button class="filter-btn <?php echo $status_filter === 'shipped' ? 'active' : ''; ?>" 
                onclick="window.location.href='orders.php?status=shipped'">
            <i class="fas fa-truck"></i> Shipped
            <span class="filter-count"><?php echo $stats['shipped']; ?></span>
        </button>
        <button class="filter-btn <?php echo $status_filter === 'completed' ? 'active' : ''; ?>" 
                onclick="window.location.href='orders.php?status=completed'">
            <i class="fas fa-check-circle"></i> Completed
            <span class="filter-count"><?php echo $stats['completed']; ?></span>
        </button>
    </div>

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tipe</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.3;"></i>
                            <p style="font-size: 1.1rem; margin: 0;">Tidak ada pesanan ditemukan</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo clean($order['order_number']); ?></strong></td>
                            <td>
                                <?php if ($order['order_type'] === 'guest'): ?>
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">GUEST</span>
                                <?php else: ?>
                                    <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">MEMBER</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo clean($order['full_name'] ?? $order['username']); ?><br>
                                <small style="color: #666;"><?php echo clean($order['email']); ?></small>
                            </td>
                            <td><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo $order['item_count']; ?> item</td>
                            <td><strong>Rp <?php echo number_format($order['final_amount'], 0, ',', '.'); ?></strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo strtoupper($order['status']); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <button class="action-btn btn-view" onclick="window.open('../pdf/invoice.php?<?php echo $order['order_type'] === 'guest' ? 'guest=' : 'order='; ?><?php echo $order['id']; ?>', '_blank')" style="display: inline-block;">
                                    <i class="fas fa-file-pdf"></i> Invoice
                                </button>
                                <button class="action-btn btn-update" onclick="openStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>', '<?php echo $order['order_type']; ?>')" style="display: inline-block;">
                                    <i class="fas fa-edit"></i> Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <button class="page-btn <?php echo $i === $page ? 'active' : ''; ?>" 
                        onclick="window.location.href='orders.php?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>'">
                    <?php echo $i; ?>
                </button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Status Update Modal -->
<div class="modal" id="statusModal">
    <div class="modal-content">
        <h2>Update Status Pesanan</h2>
        <form method="POST">
            <input type="hidden" name="order_id" id="modalOrderId">
            <input type="hidden" name="order_type" id="modalOrderType">
            <div style="margin: 20px 0;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">Status Baru:</label>
                <select name="status" id="modalStatus" style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 25px;">
                <button type="submit" name="update_status" style="flex: 1; padding: 14px 20px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s;">
                    <i class="fas fa-check"></i> Update
                </button>
                <button type="button" onclick="closeStatusModal()" style="flex: 1; padding: 14px 20px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s;">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
                </button>
            </div>
        </form>
    </div>
</div>
        </div><!-- end admin-container -->
    </main><!-- end admin-content -->
</div><!-- end admin-layout -->

<script>
function openStatusModal(orderId, currentStatus, orderType) {
    document.getElementById('modalOrderId').value = orderId;
    document.getElementById('modalOrderType').value = orderType;
    document.getElementById('modalStatus').value = currentStatus;
    document.getElementById('statusModal').classList.add('active');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}
</script>

</body>
</html>
