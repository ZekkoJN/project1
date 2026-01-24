<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Kelola Produk";

// Handle delete
if (isset($_GET['delete']) && $_GET['delete']) {
    $id = (int)$_GET['delete'];
    query("DELETE FROM products WHERE id = ?", [$id]);
    $_SESSION['success'] = "Produk berhasil dihapus";
    redirect('products.php');
}

// Fetch products with categories
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.brand LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY p.created_at DESC";
$products = fetchAll($sql, $params);

$categories = fetchAll("SELECT * FROM categories ORDER BY name");

require_once '../includes/header.php';
?>

<link rel="stylesheet" href="dashboard.php">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
.admin-layout { display: flex; min-height: 100vh; }
.admin-content { flex: 1; margin-left: 260px; padding: 30px; }
.admin-header { background: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
.content-section { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
.btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #10b981; color: white; }
.btn-primary:hover { background: #059669; }
.btn-success { background: #4caf50; color: white; }
.btn-danger { background: #f44336; color: white; }
.btn-sm { padding: 5px 10px; font-size: 0.85rem; }
.filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.filter-bar input, .filter-bar select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.95rem; }
.filter-bar input { flex: 1; min-width: 250px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
th { background: #f8f9fa; font-weight: 600; color: #555; }
.product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.badge-active { background: #d4edda; color: #155724; }
.badge-inactive { background: #f8d7da; color: #721c24; }
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1>Kelola Produk</h1>
        </div>

        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-box"></i> Daftar Produk (<?php echo count($products); ?>)</h2>
                <a href="product_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="GET" class="filter-bar">
                <input type="text" name="search" placeholder="Cari produk atau brand..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo clean($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="../img/<?php echo clean($product['image']); ?>" alt="" class="product-img">
                            </td>
                            <td><?php echo clean($product['name']); ?></td>
                            <td><?php echo clean($product['category_name'] ?? '-'); ?></td>
                            <td><?php echo clean($product['brand']); ?></td>
                            <td><?php echo formatRupiah($product['price']); ?></td>
                            <td style="<?php echo $product['stock'] < 5 ? 'color: #f44336; font-weight: 600;' : ''; ?>">
                                <?php echo $product['stock']; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $product['status']; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
