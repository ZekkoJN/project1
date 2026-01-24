<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Handle product edit
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header('Location: products.php');
    exit;
}

// Fetch product
$product = fetchOne("SELECT * FROM products WHERE id = ?", [$product_id]);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Fetch categories
$categories = fetchAll("SELECT * FROM categories ORDER BY name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $brand = clean($_POST['brand']);
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];
    $description = clean($_POST['description']);
    $status = clean($_POST['status']);
    
    // Handle image upload
    $image = $product['image']; // Keep old image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../img/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
                // Delete old image if exists
                if ($product['image'] && file_exists('../img/' . $product['image'])) {
                    @unlink('../img/' . $product['image']);
                }
            }
        }
    }
    
    // Update product
    $result = execute("UPDATE products SET 
                        name = ?, 
                        category_id = ?, 
                        brand = ?, 
                        price = ?, 
                        discount_price = ?, 
                        stock = ?, 
                        description = ?, 
                        image = ?, 
                        status = ?,
                        updated_at = NOW()
                      WHERE id = ?",
                      [$name, $category_id, $brand, $price, $discount_price, $stock, $description, $image, $status, $product_id]);
    
    if ($result) {
        $_SESSION['success'] = 'Produk berhasil diupdate!';
        header('Location: products.php');
        exit;
    } else {
        $error = 'Gagal mengupdate produk';
    }
}

$page_title = "Edit Produk";
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
    .admin-content {
        flex: 1;
        margin-left: 260px;
        padding: 30px;
    }
    .admin-container {
        max-width: 900px;
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
    .admin-header h1 {
        margin: 0;
        color: #333;
    }
    .form-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .form-group {
        margin-bottom: 25px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
    }
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }
    .current-image {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 10px;
    }
    .btn-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    .btn {
        padding: 14px 35px;
        border: none;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 50px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex: 1;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }
    .btn-primary i {
        font-size: 1.2rem;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
        flex: 0.7;
    }
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-secondary i {
        font-size: 1.2rem;
    }
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #f5c6cb;
    }
</style>
</style>

<div class="admin-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-container">
            <div class="admin-header">
                <h1>Edit Produk</h1>
                <p>Update informasi produk</p>
            </div>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="name" required value="<?php echo clean($product['name']); ?>">
            </div>

            <div class="form-group">
                <label>Kategori *</label>
                <select name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo clean($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Brand *</label>
                <input type="text" name="brand" required value="<?php echo clean($product['brand']); ?>">
            </div>

            <div class="form-group">
                <label>Harga Normal (Rp) *</label>
                <input type="number" name="price" required step="0.01" value="<?php echo $product['price']; ?>">
            </div>

            <div class="form-group">
                <label>Harga Diskon (Rp)</label>
                <input type="number" name="discount_price" step="0.01" value="<?php echo $product['discount_price']; ?>">
            </div>

            <div class="form-group">
                <label>Stok *</label>
                <input type="number" name="stock" required value="<?php echo $product['stock']; ?>">
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"><?php echo clean($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Produk</label>
                <?php if ($product['image']): ?>
                    <img src="../img/<?php echo clean($product['image']); ?>" alt="Current" class="current-image">
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
                <small style="color: #666;">Kosongkan jika tidak ingin mengubah gambar</small>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Produk
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='products.php'">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
        </div><!-- end admin-container -->
    </main><!-- end admin-content -->
</div><!-- end admin-layout -->

</body>
</html>
