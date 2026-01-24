<?php
// Tentukan halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
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
    margin: 0;
}

.admin-brand p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
}

.admin-menu {
    list-style: none;
    padding: 20px 0;
    margin: 0;
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

.admin-menu a:hover {
    background: rgba(255,255,255,0.2);
    padding-left: 30px;
}

.admin-menu a.active {
    background: rgba(255,255,255,0.2);
    padding-left: 30px;
    border-left: 4px solid white;
    padding-left: 26px;
}

.admin-menu i {
    margin-right: 15px;
    width: 20px;
    text-align: center;
}
</style>

<aside class="admin-sidebar">
    <div class="admin-brand">
        <h2><i class="fas fa-laptop"></i> <?php echo SITE_NAME; ?></h2>
        <p>Admin Panel</p>
    </div>
    <ul class="admin-menu">
        <li><a href="dashboard.php" <?php echo $current_page === 'dashboard.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="products.php" <?php echo ($current_page === 'products.php' || $current_page === 'product_add.php' || $current_page === 'product_edit.php') ? 'class="active"' : ''; ?>><i class="fas fa-box"></i> Produk</a></li>
        <li><a href="orders.php" <?php echo $current_page === 'orders.php' ? 'class="active"' : ''; ?>><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
        <li><a href="users.php" <?php echo $current_page === 'users.php' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> Pengguna</a></li>
        <li><a href="categories.php" <?php echo $current_page === 'categories.php' ? 'class="active"' : ''; ?>><i class="fas fa-tags"></i> Kategori</a></li>
        <li><a href="reviews.php" <?php echo $current_page === 'reviews.php' ? 'class="active"' : ''; ?>><i class="fas fa-star"></i> Review</a></li>
        <li><a href="reports.php" <?php echo $current_page === 'reports.php' ? 'class="active"' : ''; ?>><i class="fas fa-chart-bar"></i> Laporan</a></li>
        <li><a href="../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
