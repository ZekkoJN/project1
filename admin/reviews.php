<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Kelola Review";

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    query("DELETE FROM reviews WHERE id = ?", [$id]);
    $_SESSION['success'] = "Review berhasil dihapus!";
    redirect('reviews.php');
}

// Handle status update
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    query("UPDATE reviews SET status = 'approved' WHERE id = ?", [$id]);
    $_SESSION['success'] = "Review berhasil disetujui!";
    redirect('reviews.php');
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    query("UPDATE reviews SET status = 'rejected' WHERE id = ?", [$id]);
    $_SESSION['success'] = "Review ditolak!";
    redirect('reviews.php');
}

// Get all reviews
$reviews = fetchAll("SELECT r.*, u.username, u.full_name, p.name as product_name 
                     FROM reviews r 
                     LEFT JOIN users u ON r.user_id = u.id 
                     LEFT JOIN products p ON r.product_id = p.id 
                     ORDER BY r.created_at DESC");

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

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-success {
    background: #48bb78;
    color: white;
}

.btn-danger {
    background: #f56565;
    color: white;
}

.btn-warning {
    background: #ed8936;
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

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.badge-success {
    background: #c6f6d5;
    color: #22543d;
}

.badge-warning {
    background: #feebc8;
    color: #744210;
}

.badge-danger {
    background: #fed7d7;
    color: #742a2a;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border: 1px solid #9ae6b4;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.rating {
    color: #f59e0b;
}
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <div class="admin-content">
        <div class="page-header">
            <h1><i class="fas fa-star"></i> Kelola Review</h1>
            <p>Moderasi dan kelola review produk dari pelanggan</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Daftar Review</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Pengguna</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e0; margin-bottom: 10px;"></i>
                                <p style="color: #718096;">Belum ada review</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td><?php echo $review['product_name'] ?? 'Produk dihapus'; ?></td>
                                <td>
                                    <strong><?php echo $review['full_name'] ?? $review['username'] ?? 'Unknown'; ?></strong><br>
                                    <small><?php echo $review['username'] ?? 'N/A'; ?></small>
                                </td>
                                <td>
                                    <span class="rating">
                                        <?php for($i = 0; $i < $review['rating']; $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                        <?php for($i = $review['rating']; $i < 5; $i++): ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                    <br>
                                    <small><?php echo $review['rating']; ?>/5</small>
                                </td>
                                <td><?php echo substr($review['comment'], 0, 100); ?><?php echo strlen($review['comment']) > 100 ? '...' : ''; ?></td>
                                <td>
                                    <?php if ($review['status'] == 'approved'): ?>
                                        <span class="badge badge-success">Disetujui</span>
                                    <?php elseif ($review['status'] == 'pending'): ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($review['status'] != 'approved'): ?>
                                            <a href="?approve=<?php echo $review['id']; ?>" 
                                               class="btn btn-success btn-sm"
                                               onclick="return confirm('Setujui review ini?')">
                                                <i class="fas fa-check"></i> Setuju
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($review['status'] != 'rejected'): ?>
                                            <a href="?reject=<?php echo $review['id']; ?>" 
                                               class="btn btn-warning btn-sm"
                                               onclick="return confirm('Tolak review ini?')">
                                                <i class="fas fa-times"></i> Tolak
                                            </a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $review['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus review ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
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
