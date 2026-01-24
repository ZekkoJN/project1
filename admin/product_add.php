<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$page_title = "Tambah Produk";
$error = '';
$success = '';

$categories = fetchAll("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $brand = clean($_POST['brand']);
    $description = clean($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = uploadImage($_FILES['image'], PRODUCT_IMG_PATH);
        if ($upload_result['success']) {
            $image = $upload_result['filename'];
        } else {
            $error = $upload_result['message'];
        }
    }
    
    if (empty($error)) {
        $slug = strtolower(str_replace(' ', '-', $name));
        
        $sql = "INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        query($sql, [$category_id, $name, $slug, $brand, $description, $price, $discount_price, $stock, $image, $status]);
        
        $_SESSION['success'] = "Produk berhasil ditambahkan";
        redirect('products.php');
    }
}

require_once '../includes/header.php';
?>

<link rel="stylesheet" href="dashboard.php">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
.admin-layout { display: flex; min-height: 100vh; }
.admin-content { flex: 1; margin-left: 260px; padding: 30px; }
.admin-header { background: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
.content-section { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
.form-control { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
.form-control:focus { outline: none; border-color: #10b981; }
textarea.form-control { min-height: 100px; resize: vertical; }
.btn { padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #10b981; color: white; }
.btn-secondary { background: #6c757d; color: white; margin-left: 10px; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="admin-header">
            <h1>Tambah Produk Baru</h1>
        </div>

        <div class="content-section">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Produk *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo clean($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Brand *</label>
                    <input type="text" name="brand" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label>Harga *</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Harga Diskon</label>
                    <input type="number" name="discount_price" class="form-control" step="0.01">
                </div>

                <div class="form-group">
                    <label>Stok *</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Gambar Produk</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                    <a href="products.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
