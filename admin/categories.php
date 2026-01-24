<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Kelola Kategori";

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    query("DELETE FROM categories WHERE id = ?", [$id]);
    $_SESSION['success'] = "Kategori berhasil dihapus!";
    redirect('categories.php');
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $description = clean($_POST['description']);
    $status = isset($_POST['status']) ? $_POST['status'] : 'active';
    
    if (isset($_POST['id']) && $_POST['id']) {
        // Update
        $id = (int)$_POST['id'];
        query("UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?", 
              [$name, $description, $status, $id]);
        $_SESSION['success'] = "Kategori berhasil diupdate!";
    } else {
        // Insert
        query("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)", 
              [$name, $description, $status]);
        $_SESSION['success'] = "Kategori berhasil ditambahkan!";
    }
    redirect('categories.php');
}

// Get edit data
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_category = fetchOne("SELECT * FROM categories WHERE id = ?", [(int)$_GET['edit']]);
}

// Get all categories
$categories = fetchAll("SELECT c.*, COUNT(p.id) as product_count 
                        FROM categories c 
                        LEFT JOIN products p ON c.id = p.category_id 
                        GROUP BY c.id 
                        ORDER BY c.name");

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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2d3748;
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 5px;
    font-size: 14px;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
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

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
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
}
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <div class="admin-content">
        <div class="page-header">
            <h1><i class="fas fa-tags"></i> Kelola Kategori</h1>
            <p>Tambah, edit, dan hapus kategori produk</p>
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
            <h3><?php echo $edit_category ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h3>
            <form method="POST">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nama Kategori *</label>
                    <input type="text" name="name" required 
                           value="<?php echo $edit_category ? $edit_category['name'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description"><?php echo $edit_category ? $edit_category['description'] : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="active" <?php echo ($edit_category && isset($edit_category['status']) && $edit_category['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="inactive" <?php echo ($edit_category && isset($edit_category['status']) && $edit_category['status'] == 'inactive') ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $edit_category ? 'Update Kategori' : 'Tambah Kategori'; ?>
                </button>
                <?php if ($edit_category): ?>
                    <a href="categories.php" class="btn btn-warning">
                        <i class="fas fa-times"></i> Batal
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3>Daftar Kategori</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo $cat['name']; ?></td>
                            <td><?php echo substr($cat['description'], 0, 50); ?><?php echo strlen($cat['description']) > 50 ? '...' : ''; ?></td>
                            <td><?php echo $cat['product_count']; ?> produk</td>
                            <td>
                                <?php if (isset($cat['status']) && $cat['status'] == 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php elseif (isset($cat['status']) && $cat['status'] == 'inactive'): ?>
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit=<?php echo $cat['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $cat['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
