<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/voucher_functions.php';

$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$is_guest = !$user_id;
$error = '';
$success = '';
$discount_amount = 0;
$user_vouchers = [];
$cashback_balance = 0;

// If guest, get cart from session/cookie
// If member, get cart from database
if ($is_guest) {
    // Guest checkout - get items dari session dan fetch product details
    $session_cart = $_SESSION['cart'] ?? [];
    if (empty($session_cart)) {
        redirect('cart.php');
    }
    
    // Fetch product details for each item
    $cart_items = [];
    foreach ($session_cart as $item) {
        $product = fetchOne("SELECT id, name, brand, price, discount_price, image, stock FROM products WHERE id = ?", 
                           [$item['product_id']]);
        if ($product) {
            $cart_items[] = array_merge($item, $product, ['quantity' => $item['quantity']]);
        }
    }
    
    if (empty($cart_items)) {
        redirect('cart.php');
    }
} else {
    // Member checkout
    $cart_items = fetchAll("SELECT c.*, p.name, p.brand, p.price, p.discount_price, p.image, p.stock 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = ?", [$user_id]);
    
    if (count($cart_items) === 0) {
        redirect('cart.php');
    }
    
    $user_vouchers = getUserVouchers($user_id);
    $cashback_balance = getCashbackBalance($user_id);
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $item_price = $item['discount_price'] ?: $item['price'];
    $subtotal += $item_price * $item['quantity'];
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_email = $is_guest ? clean($_POST['guest_email'] ?? '') : null;
    $guest_name = $is_guest ? clean($_POST['guest_name'] ?? '') : null;
    $shipping_address = clean($_POST['shipping_address']);
    $shipping_phone = clean($_POST['shipping_phone']);
    $payment_method = $_POST['payment_method'];
    $notes = clean($_POST['notes'] ?? '');
    $voucher_code = clean($_POST['voucher_code'] ?? '');
    $use_cashback = isset($_POST['use_cashback']) ? floatval($_POST['use_cashback']) : 0;
    
    $validation_errors = [];
    
    if (empty($shipping_address) || empty($shipping_phone) || empty($payment_method)) {
        $validation_errors[] = 'Alamat dan telepon wajib diisi';
    }
    
    if ($is_guest && (empty($guest_email) || empty($guest_name))) {
        $validation_errors[] = 'Email dan nama wajib diisi untuk guest checkout';
    }
    
    // Validate email
    $email_to_validate = $is_guest ? $guest_email : ($_SESSION['email'] ?? '');
    if (empty($email_to_validate)) {
        $validation_errors[] = 'Email tidak ditemukan';
    } elseif (!filter_var($email_to_validate, FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = 'Format email tidak valid';
    }
    
    if (!empty($validation_errors)) {
        $error = implode('<br>', $validation_errors);
    } else {
        try {
            $pdo->beginTransaction();
            
            // Generate order number
            $order_number = $is_guest ? generateGuestOrderNumber() : 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Apply voucher if provided
            if (!empty($voucher_code) && !$is_guest) {
                $voucher_result = applyVoucher($user_id, $voucher_code, $subtotal);
                if ($voucher_result['success']) {
                    $discount_amount += $voucher_result['discount'];
                } else {
                    $error = $voucher_result['message'];
                }
            }
            
            // Apply cashback if member wants to use it
            if ($use_cashback > 0 && !$is_guest && $use_cashback <= $cashback_balance) {
                $discount_amount += $use_cashback;
                
                // Deduct from cashback balance
                query("UPDATE cashback_balance SET balance = balance - ? WHERE user_id = ?", 
                      [$use_cashback, $user_id]);
            }
            
            $final_amount = max(0, $subtotal - $discount_amount);
            
            if ($is_guest) {
                // Create guest order
                $sql = "INSERT INTO guest_orders (order_number, guest_email, guest_name, guest_phone, 
                        total_amount, discount_amount, final_amount, payment_method, payment_status, shipping_address, notes) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, ?)";
                query($sql, [$order_number, $guest_email, $guest_name, $shipping_phone, 
                            $subtotal, $discount_amount, $final_amount, $payment_method, $shipping_address, $notes]);
                
                $order_id = lastInsertId();
                
                // Insert guest order items
                foreach ($cart_items as $item) {
                    $item_price = $item['discount_price'] ?: $item['price'];
                    $item_subtotal = $item_price * $item['quantity'];
                    
                    $sql = "INSERT INTO guest_order_items (guest_order_id, product_id, product_name, price, quantity, subtotal) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    query($sql, [$order_id, $item['product_id'], $item['name'], $item_price, 
                                $item['quantity'], $item_subtotal]);
                    
                    // Update stock
                    query("UPDATE products SET stock = stock - ? WHERE id = ?", 
                          [$item['quantity'], $item['product_id']]);
                }
                
                // Clear guest cart from session
                unset($_SESSION['cart']);
                $_SESSION['cart'] = [];
                
                $table = 'guest_orders';
                $redirect_param = "guest=" . $order_id;
                
            } else {
                // Create member order
                $sql = "INSERT INTO orders (user_id, order_number, total_amount, discount_amount, final_amount, 
                        payment_method, payment_status, shipping_address, shipping_phone, notes) 
                        VALUES (?, ?, ?, ?, ?, ?, 'paid', ?, ?, ?)";
                query($sql, [$user_id, $order_number, $subtotal, $discount_amount, $final_amount, 
                            $payment_method, $shipping_address, $shipping_phone, $notes]);
                
                $order_id = lastInsertId();
                
                // Insert order items
                foreach ($cart_items as $item) {
                    $item_price = $item['discount_price'] ?: $item['price'];
                    $item_subtotal = $item_price * $item['quantity'];
                    
                    $sql = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    query($sql, [$order_id, $item['product_id'], $item['name'], $item_price, 
                                $item['quantity'], $item_subtotal]);
                    
                    // Update stock
                    query("UPDATE products SET stock = stock - ? WHERE id = ?", 
                          [$item['quantity'], $item['product_id']]);
                }
                
                // Clear cart
                query("DELETE FROM cart WHERE user_id = ?", [$user_id]);
                
                // Add cashback earned (2% dari final_amount)
                $cashback_earned = $final_amount * 0.02;
                if ($cashback_earned > 0) {
                    applyCashback($user_id, $discount_amount);
                }
                
                $table = 'orders';
                $redirect_param = "order=" . $order_id;
            }
            
            $pdo->commit();
            
            $_SESSION['success'] = "Pesanan berhasil dibuat! Nomor order: $order_number";
            redirect('order_success.php?' . $redirect_param);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

$page_title = "Checkout";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
.checkout-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 30px;
}

.checkout-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

.payment-options {
    display: grid;
    gap: 15px;
}

.payment-option {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
}

.payment-option input[type="radio"] {
    width: 20px;
    height: 20px;
}

.payment-option:hover {
    border-color: #667eea;
}

.payment-option.selected {
    border-color: #667eea;
    background: #f0f4ff;
}

.order-summary {
    position: sticky;
    top: 100px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.summary-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.summary-totals {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
}

.total-final {
    font-size: 1.3rem;
    font-weight: 700;
    color: #667eea;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
}

.badge-success {
    background: #28a745;
    color: white;
}

.member-benefits {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-left: 4px solid #667eea;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.voucher-item {
    padding: 12px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.voucher-item:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
}

.voucher-item input[type="radio"] {
    margin-right: 10px;
}

.voucher-item.selected {
    border-color: #667eea;
    background: #f0f4ff;
}

.cashback-input {
    display: grid;
    gap: 10px;
}

.input-group {
    display: flex;
    gap: 10px;
}

.input-group input {
    flex: 1;
}

.input-group button {
    padding: 10px 15px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
</style>

<div class="checkout-container">
    <h1 style="margin-bottom: 30px;"><i class="fas fa-credit-card"></i> Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="checkout-grid">
            <div>
                <!-- Guest Info Section -->
                <?php if ($is_guest): ?>
                    <div class="checkout-section">
                        <h3 style="margin-bottom: 20px;"><i class="fas fa-user"></i> Informasi Pemesan</h3>
                        
                        <div class="form-group">
                            <label>Nama Lengkap *</label>
                            <input type="text" name="guest_name" class="form-control" 
                                   placeholder="Nama Anda" required 
                                   value="<?php echo $_POST['guest_name'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="guest_email" class="form-control" 
                                   placeholder="contoh@email.com" required 
                                   value="<?php echo $_POST['guest_email'] ?? ''; ?>">
                        </div>
                        
                        <div style="background: #e8f4f8; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <p style="margin: 0; color: #0c5460;">
                                <i class="fas fa-info-circle"></i> 
                                Anda order sebagai tamu. <a href="login.php" style="color: #0c5460; text-decoration: underline;">Login</a> atau <a href="register.php" style="color: #0c5460; text-decoration: underline;">daftar</a> untuk mendapat voucher welcome 10% diskon!
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Member Benefits Section -->
                    <div class="checkout-section">
                        <h3 style="margin-bottom: 15px;"><i class="fas fa-crown"></i> Member Benefits</h3>
                        
                        <div class="member-benefits">
                            <div style="margin-bottom: 10px;">
                                <strong>Voucher Aktif:</strong> <span class="badge badge-success"><?php echo count($user_vouchers); ?></span>
                            </div>
                            <div>
                                <strong>Cashback Balance:</strong> <span style="color: #28a745; font-size: 1.2rem; font-weight: 700;">Rp <?php echo formatRupiah($cashback_balance); ?></span>
                            </div>
                        </div>
                        
                        <!-- Voucher Section -->
                        <?php if (!empty($user_vouchers)): ?>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 10px; font-weight: 600;">Pilih Voucher:</label>
                                <?php foreach ($user_vouchers as $voucher): ?>
                                    <label class="voucher-item">
                                        <input type="radio" name="voucher_code" value="<?php echo $voucher['voucher_code']; ?>">
                                        <div style="display: inline-block;">
                                            <strong><?php echo $voucher['voucher_code']; ?></strong> - <?php echo $voucher['description']; ?>
                                            <br>
                                            <small style="color: #888;">
                                                Valid hingga <?php echo date('d/m/Y', strtotime($voucher['valid_until'])); ?>
                                            </small>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Cashback Usage -->
                        <?php if ($cashback_balance > 0): ?>
                            <div>
                                <label style="display: block; margin-bottom: 10px; font-weight: 600;">Gunakan Cashback:</label>
                                <div class="input-group">
                                    <input type="number" name="use_cashback" class="form-control" 
                                           placeholder="Jumlah cashback (Rp)" 
                                           max="<?php echo $cashback_balance; ?>" 
                                           min="0" step="1000">
                                    <button type="button" onclick="setMaxCashback()" style="background: #28a745;">Gunakan Semua</button>
                                </div>
                                <small style="color: #888; display: block; margin-top: 5px;">
                                    Saldo cashback Anda: Rp <?php echo formatRupiah($cashback_balance); ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Shipping Info Section -->
                <div class="checkout-section">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-shipping-fast"></i> Informasi Pengiriman</h3>
                    
                    <div class="form-group">
                        <label>Alamat Lengkap *</label>
                        <textarea name="shipping_address" class="form-control" 
                                  placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos" 
                                  required><?php echo $_POST['shipping_address'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Nomor Telepon *</label>
                        <input type="tel" name="shipping_phone" class="form-control" 
                               placeholder="08xxxxxxxxxx" required 
                               value="<?php echo $_POST['shipping_phone'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" 
                                  placeholder="Catatan untuk penjual..."><?php echo $_POST['notes'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="checkout-section">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-credit-card"></i> Metode Pembayaran</h3>
                    
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="transfer" required>
                            <div>
                                <strong>Transfer Bank</strong>
                                <p style="color: #888; font-size: 0.9rem; margin: 5px 0 0 0;">Transfer ke rekening tujuan</p>
                            </div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" required>
                            <div>
                                <strong>COD (Cash on Delivery)</strong>
                                <p style="color: #888; font-size: 0.9rem; margin: 5px 0 0 0;">Bayar saat barang diterima</p>
                            </div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="ewallet" required>
                            <div>
                                <strong>E-Wallet</strong>
                                <p style="color: #888; font-size: 0.9rem; margin: 5px 0 0 0;">GoPay, OVO, Dana, dll</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="order-summary">
                <div class="checkout-section">
                    <h3 style="margin-bottom: 20px;">Ringkasan Pesanan</h3>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <?php $item_price = $item['discount_price'] ?: $item['price']; ?>
                        <div class="summary-item">
                            <img src="img/<?php echo clean($item['image']); ?>" alt="">
                            <div style="flex: 1;">
                                <h5 style="margin: 0 0 5px 0;"><?php echo clean($item['name']); ?></h5>
                                <p style="color: #888; font-size: 0.9rem; margin: 0;">
                                    <?php echo $item['quantity']; ?> x <?php echo formatRupiah($item_price); ?>
                                </p>
                            </div>
                            <strong><?php echo formatRupiah($item_price * $item['quantity']); ?></strong>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="summary-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <strong><?php echo formatRupiah($subtotal); ?></strong>
                        </div>
                        
                        <div id="discount-row" style="display: none;" class="total-row">
                            <span>Diskon</span>
                            <strong id="discount-amount" style="color: #28a745;">-Rp 0</strong>
                        </div>
                        
                        <div class="total-row">
                            <span>Ongkir</span>
                            <strong>Rp 0</strong>
                        </div>
                        
                        <div class="total-row total-final">
                            <span>Total</span>
                            <span id="total-amount"><?php echo formatRupiah($subtotal); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i> Buat Pesanan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Add selected class to payment option
document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
        this.closest('.payment-option').classList.add('selected');
    });
});

// Add selected class to voucher
document.querySelectorAll('.voucher-item input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.voucher-item').forEach(item => item.classList.remove('selected'));
        this.closest('.voucher-item').classList.add('selected');
        updateTotal();
    });
});

// Update total when cashback changes
document.querySelector('input[name="use_cashback"]')?.addEventListener('change', updateTotal);

function updateTotal() {
    const subtotal = <?php echo $subtotal; ?>;
    let discount = 0;
    
    // Check voucher
    const selectedVoucher = document.querySelector('input[name="voucher_code"]:checked');
    if (selectedVoucher) {
        // This would require AJAX to calculate - for now just show message
    }
    
    // Check cashback
    const cashbackInput = document.querySelector('input[name="use_cashback"]');
    if (cashbackInput && cashbackInput.value) {
        discount += parseInt(cashbackInput.value);
    }
    
    const final = Math.max(0, subtotal - discount);
    
    if (discount > 0) {
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('discount-amount').textContent = '-' + formatRupiah(discount);
    } else {
        document.getElementById('discount-row').style.display = 'none';
    }
    
    document.getElementById('total-amount').textContent = formatRupiah(final);
}

function setMaxCashback() {
    const max = <?php echo $cashback_balance; ?>;
    document.querySelector('input[name="use_cashback"]').value = max;
    updateTotal();
}

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num);
}
</script>

<?php require_once 'includes/footer.php'; ?>
