<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/voucher_functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$page_title = "Profil Saya";
$error = '';
$success = '';

// Fetch user data
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);

// Fetch member benefits
$vouchers = getUserVouchers($user_id);
$cashback = getCashbackBalance($user_id);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = clean($_POST['full_name']);
        $phone = clean($_POST['phone']);
        $address = clean($_POST['address']);
        
        query("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?", 
              [$full_name, $phone, $address, $user_id]);
        
        $success = "Profil berhasil diperbarui";
        $user = fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password_hash'])) {
            $error = "Password saat ini salah";
        } elseif (strlen($new_password) < 6) {
            $error = "Password baru minimal 6 karakter";
        } elseif ($new_password !== $confirm_password) {
            $error = "Konfirmasi password tidak cocok";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            query("UPDATE users SET password_hash = ? WHERE id = ?", [$new_hash, $user_id]);
            $success = "Password berhasil diubah";
        }
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
.profile-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.profile-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-header h2 {
    margin: 0;
    color: #667eea;
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

.btn {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-block;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.user-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    margin: 0 auto 20px;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.benefit-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #f0f4f8 100%);
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    display: flex;
    gap: 15px;
}

.benefit-card-icon {
    font-size: 2.5rem;
    min-width: 60px;
}

.benefit-card-content {
    flex: 1;
}

.benefit-card-content h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 0.95rem;
}

.benefit-value {
    margin: 0 0 5px 0;
    font-size: 1.8rem;
    font-weight: bold;
    color: #667eea;
}

.benefit-desc {
    margin: 0;
    color: #666;
    font-size: 0.85rem;
}

.voucher-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.voucher-list li {
    padding: 8px 0;
    font-size: 0.85rem;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.voucher-code {
    background: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    color: #667eea;
    font-family: monospace;
}

.voucher-type {
    color: #666;
}

</style>

<div class="profile-container">
    <div class="profile-section">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="margin: 0 0 5px 0;"><?php echo clean($user['full_name'] ?? $user['username']); ?></h1>
            <p style="color: #888; margin: 0;"><?php echo clean($user['email']); ?></p>
            <?php if ($user['role'] === 'admin'): ?>
                <span style="display: inline-block; margin-top: 10px; padding: 5px 15px; background: #667eea; color: white; border-radius: 20px; font-size: 0.9rem;">
                    <i class="fas fa-crown"></i> Admin
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-section">
        <div class="section-header">
            <i class="fas fa-crown" style="color: #667eea; font-size: 1.5rem;"></i>
            <h2>Benefit Member</h2>
        </div>
        
        <!-- Member Benefits Cards -->
        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-card-icon">🎟️</div>
                <div class="benefit-card-content">
                    <h3>Voucher Aktif</h3>
                    <p class="benefit-value"><?php echo count($vouchers); ?></p>
                    <ul class="voucher-list">
                        <?php if (count($vouchers) > 0):
                            foreach (array_slice($vouchers, 0, 3) as $voucher): ?>
                            <li>
                                <span class="voucher-code"><?php echo $voucher['voucher_code']; ?></span>
                                <span class="voucher-type">
                                    <?php if ($voucher['voucher_type'] === 'discount_percent'): ?>
                                        Diskon <?php echo $voucher['amount']; ?>%
                                    <?php else: ?>
                                        Diskon Rp <?php echo number_format($voucher['amount'], 0); ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <?php endforeach;
                            if (count($vouchers) > 3): ?>
                                <li style="text-align: center; font-size: 0.85rem; color: #666;">
                                    +<?php echo count($vouchers) - 3; ?> voucher lainnya
                                </li>
                            <?php endif;
                        else: ?>
                            <li style="text-align: center; color: #999;">Belum ada voucher</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-card-icon">💰</div>
                <div class="benefit-card-content">
                    <h3>Cashback Tersedia</h3>
                    <p class="benefit-value">Rp <?php echo number_format($cashback, 0); ?></p>
                    <p class="benefit-desc">Gunakan cashback di pembelian berikutnya</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profile-section">
        <div class="section-header">
            <i class="fas fa-user-edit" style="color: #667eea; font-size: 1.5rem;"></i>
            <h2>Edit Profil</h2>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" 
                       value="<?php echo clean($user['full_name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" 
                       value="<?php echo clean($user['username']); ?>" disabled>
                <small style="color: #888;">Username tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" 
                       value="<?php echo clean($user['email']); ?>" disabled>
                <small style="color: #888;">Email tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label>Nomor Telepon</label>
                <input type="tel" name="phone" class="form-control" 
                       value="<?php echo clean($user['phone'] ?? ''); ?>" 
                       placeholder="08xxxxxxxxxx">
            </div>
            
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" class="form-control" 
                          placeholder="Alamat lengkap Anda"><?php echo clean($user['address'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
    
    <div class="profile-section">
        <div class="section-header">
            <i class="fas fa-lock" style="color: #667eea; font-size: 1.5rem;"></i>
            <h2>Ubah Password</h2>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label>Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" class="form-control" 
                       placeholder="Minimal 6 karakter" required>
            </div>
            
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" name="change_password" class="btn btn-primary">
                <i class="fas fa-key"></i> Ubah Password
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
