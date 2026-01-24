<?php
require_once 'config/config.php';
require_once 'config/database.php';

$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$is_guest = !$user_id;

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($is_guest) {
        // Store in session for guest
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            // Get product details
            $product = fetchOne("SELECT id, name, price, discount_price, brand, image, stock FROM products WHERE id = ?", [$product_id]);
            if ($product) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'discount_price' => $product['discount_price'],
                    'brand' => $product['brand'],
                    'image' => $product['image'],
                    'stock' => $product['stock']
                ];
            }
        }
    } else {
        // Store in database for member
        $existing = fetchOne("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        
        if ($existing) {
            query("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?", 
                  [$quantity, $user_id, $product_id]);
        } else {
            query("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", 
                  [$user_id, $product_id, $quantity]);
        }
    }
    
    $_SESSION['success'] = "Produk ditambahkan ke keranjang";
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    
    if ($is_guest) {
        if (isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
        }
    } else {
        $cart_id = $index;
        query("DELETE FROM cart WHERE id = ? AND user_id = ?", [$cart_id, $user_id]);
    }
    redirect('cart.php');
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if ($is_guest) {
        foreach ($_POST['quantity'] as $index => $quantity) {
            if ($quantity > 0 && isset($_SESSION['cart'][$index])) {
                $_SESSION['cart'][$index]['quantity'] = (int)$quantity;
            } elseif ($quantity <= 0 && isset($_SESSION['cart'][$index])) {
                array_splice($_SESSION['cart'], $index, 1);
            }
        }
    } else {
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            if ($quantity > 0) {
                query("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?", 
                      [(int)$quantity, (int)$cart_id, $user_id]);
            }
        }
    }
    redirect('cart.php');
}

// Fetch cart items
if ($is_guest) {
    $cart_items = $_SESSION['cart'] ?? [];
} else {
    $cart_items = fetchAll("SELECT c.*, p.name, p.brand, p.price, p.discount_price, p.image, p.stock 
                            FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?", [$user_id]);
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $item_price = ($item['discount_price'] ?? null) ?: ($item['price'] ?? 0);
    $subtotal += $item_price * ($item['quantity'] ?? 1);
}

$page_title = "Keranjang Belanja";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.cart-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    border: 1px solid #f0f0f0;
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.cart-table th {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 18px 15px;
    text-align: left;
    font-weight: 600;
    border: none;
}

.cart-table th:nth-child(2),
.cart-table th:nth-child(3),
.cart-table th:nth-child(4),
.cart-table th:nth-child(5) {
    text-align: center;
}

.cart-table td {
    padding: 20px 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.cart-table tbody tr:hover {
    background: #f0fdf4;
    transition: background 0.2s;
}

.cart-item-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #d1fae5;
}

.quantity-input {
    width: 70px;
    padding: 8px 10px;
    border: 2px solid #d1fae5;
    border-radius: 6px;
    text-align: center;
    font-weight: 500;
    transition: border-color 0.2s;
}

.quantity-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.btn-remove {
    color: #ff6b6b;
    cursor: pointer;
    font-size: 1.1rem;
    transition: transform 0.2s;
}

.btn-remove:hover {
    transform: scale(1.2);
}

.cart-summary {
    background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
    padding: 25px;
    border-radius: 15px;
    position: sticky;
    top: 100px;
    box-shadow: 0 2px 15px rgba(16, 185, 129, 0.15);
    border: 2px solid #a7f3d0;
    height: fit-content;
}

.cart-summary h3 {
    color: #047857;
    margin-bottom: 20px;
    font-size: 1.2rem;
    text-align: center;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #a7f3d0;
    font-size: 0.95rem;
    color: #374151;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-total {
    font-size: 1.3rem;
    font-weight: 700;
    color: #047857;
    padding-top: 15px !important;
    border-top: 2px solid #a7f3d0;
    margin-top: 10px !important;
}

.btn-checkout {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 25px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    display: block;
}
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
}

.empty-cart i {
    font-size: 5rem;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-cart h2 {
    color: #666;
    margin-bottom: 10px;
}

.product-name-cell {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.product-brand-cell {
    color: #888;
    font-size: 0.9rem;
}

.product-id-cell {
    color: #999;
    font-size: 0.85rem;
    margin-top: 4px;
}

.price-cell {
    text-align: right;
}

.price-original {
    text-decoration: line-through;
    color: #999;
    font-size: 0.9rem;
}

.price-discount {
    color: #059669;
    font-weight: 600;
}

.price-normal {
    color: #047857;
    font-weight: 600;
}

@media (max-width: 768px) {
    .cart-container {
        grid-template-columns: 1fr !important;
    }
    
    .cart-summary {
        position: relative;
        top: 0;
    }
}
</style>

<div class="cart-container">
    <h1 style="margin-bottom: 30px;"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($cart_items) > 0): ?>
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px;">
            <div class="cart-section">
                <form method="POST">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $index => $item): ?>
                                <?php
                                $item_price = ($item['discount_price'] ?? null) ?: ($item['price'] ?? 0);
                                $item_subtotal = $item_price * ($item['quantity'] ?? 1);
                                $item_key = $is_guest ? $index : $item['id'];
                                ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: flex-start; gap: 18px;">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="img/<?php echo clean($item['image']); ?>" 
                                                     alt="<?php echo clean($item['name'] ?? 'Product'); ?>" 
                                                     class="cart-item-img" style="display: block;">
                                            <?php else: ?>
                                                <div class="cart-item-img" style="background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%); display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image" style="color: #ccc; font-size: 2.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div style="flex: 1;">
                                                <div class="product-name-cell">
                                                    <?php echo clean($item['name'] ?? 'Unnamed Product'); ?>
                                                </div>
                                                <div class="product-brand-cell">
                                                    <?php echo clean($item['brand'] ?? '-'); ?>
                                                </div>
                                                <div class="product-id-cell">
                                                    SKU: <?php echo $item['product_id'] ?? 'N/A'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price-cell">
                                        <?php if ($item['discount_price'] ?? 0): ?>
                                            <div class="price-original">
                                                <?php echo formatRupiah($item['price'] ?? 0); ?>
                                            </div>
                                            <div class="price-discount">
                                                <?php echo formatRupiah($item['discount_price']); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="price-normal">
                                                <?php echo formatRupiah($item['price'] ?? 0); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="number" name="quantity[<?php echo $item_key; ?>]" 
                                               class="quantity-input" value="<?php echo $item['quantity'] ?? 1; ?>" 
                                               min="1" max="<?php echo $item['stock'] ?? 999; ?>">
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: #333;">
                                        <?php echo formatRupiah($item_subtotal); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="?remove=<?php echo $item_key; ?>" 
                                           class="btn-remove" 
                                           title="Hapus dari keranjang"
                                           onclick="return confirm('Hapus produk ini dari keranjang?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 20px;">
                        <button type="submit" name="update_cart" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Update Keranjang
                        </button>
                    </div>
                </form>
            </div>
            
            <div>
                <div class="cart-summary">
                    <h3 style="margin-bottom: 20px; text-align: center; font-size: 1.2rem;">Ringkasan Belanja</h3>
                    
                    <div class="summary-row" style="color: #666;">
                        <span>Subtotal</span>
                        <strong style="color: #047857;"><?php echo formatRupiah($subtotal); ?></strong>
                    </div>
                    <div class="summary-row" style="color: #666;">
                        <span>Ongkir</span>
                        <strong style="color: #047857;">Rp 0</strong>
                    </div>
                    <div class="summary-row summary-total">
                        <span style="font-size: 1.15rem; color: #047857;">Total</span>
                        <span style="font-size: 1.25rem; color: #047857;"><?php echo formatRupiah($subtotal); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn-checkout">
                        <i class="fas fa-credit-card"></i> Checkout Sekarang
                    </a>
                    <a href="index.php" style="display: block; text-align: center; margin-top: 12px; color: #10b981; text-decoration: none; font-weight: 500; transition: color 0.2s; padding: 10px 0;">
                        <i class="fas fa-arrow-left"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="cart-section empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Keranjang Belanja Kosong</h2>
            <p style="color: #888; margin: 20px 0;">Belum ada produk di keranjang Anda</p>
            <a href="index.php#products" class="cta-btn">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
