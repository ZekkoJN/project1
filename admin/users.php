<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_role'])) {
        $user_id = (int)$_POST['user_id'];
        $current_role = $_POST['current_role'];
        $new_role = $current_role === 'admin' ? 'customer' : 'admin';
        
        execute("UPDATE users SET role = ? WHERE id = ?", [$new_role, $user_id]);
        $_SESSION['success'] = 'Role user berhasil diupdate!';
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        // Don't allow deleting own account
        if ($user_id !== $_SESSION['user_id']) {
            execute("DELETE FROM users WHERE id = ?", [$user_id]);
            $_SESSION['success'] = 'User berhasil dihapus!';
        }
    }
    
    header('Location: users.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$where_clause = $search ? "WHERE username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%'" : '';

// Fetch users
$users = fetchAll("SELECT u.*, 
                   (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                   (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_spent
                   FROM users u
                   $where_clause
                   ORDER BY u.created_at DESC
                   LIMIT $per_page OFFSET $offset");

// Count total users
$total_result = fetchOne("SELECT COUNT(*) as total FROM users $where_clause");
$total_users = $total_result['total'];
$total_pages = ceil($total_users / $per_page);

// User statistics
$total_customers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")['count'];
$total_admins = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")['count'];

$page_title = "Manajemen User";
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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stat-label { color: #666; font-size: 0.9rem; }
    .stat-value { font-size: 2rem; font-weight: bold; color: #333; margin: 10px 0; }
    .search-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .search-bar input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
    }
    .users-table {
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
    }
    .role-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .role-admin { background: #10b981; color: white; }
    .role-customer { background: #51cf66; color: white; }
    .action-btn {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.85rem;
        margin: 0 3px;
    }
    .btn-toggle { background: #ffc107; color: #000; }
    .btn-delete { background: #dc3545; color: white; }
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
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-container">
            <div class="admin-header">
                <div>
                    <h1>Manajemen User</h1>
                    <p>Kelola user dan permission</p>
        </div>
        <button onclick="window.location.href='dashboard.php'" style="padding: 12px 24px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?php echo $total_users; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Customers</div>
            <div class="stat-value"><?php echo $total_customers; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Admins</div>
            <div class="stat-value"><?php echo $total_admins; ?></div>
        </div>
    </div>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Cari user (username, email, nama)..." value="<?php echo $search; ?>">
        </form>
    </div>

    <div class="users-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Total Orders</th>
                    <th>Total Spent</th>
                    <th>Registered</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <strong><?php echo clean($user['username']); ?></strong><br>
                            <small style="color: #666;"><?php echo clean($user['full_name'] ?? '-'); ?></small>
                        </td>
                        <td><?php echo clean($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['order_count']; ?></td>
                        <td>Rp <?php echo number_format($user['total_spent'] ?? 0, 0, ',', '.'); ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="current_role" value="<?php echo $user['role']; ?>">
                                    <button type="submit" name="toggle_role" class="action-btn btn-toggle" 
                                            onclick="return confirm('Yakin ingin mengubah role user ini?')">
                                        <i class="fas fa-exchange-alt"></i> Toggle Role
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="action-btn btn-delete" 
                                            onclick="return confirm('Yakin ingin menghapus user ini? Aksi tidak dapat dibatalkan!')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <button class="page-btn <?php echo $i === $page ? 'active' : ''; ?>" 
                        onclick="window.location.href='users.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>'">
                    <?php echo $i; ?>
                </button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
        </div><!-- end admin-container -->
    </main><!-- end admin-content -->
</div><!-- end admin-layout -->

</body>
</html>
