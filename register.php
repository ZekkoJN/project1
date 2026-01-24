<?php
$page_title = "Register";
require_once 'includes/header.php';
require_once 'includes/voucher_functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = 'Invalid request';
    } else {
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = clean($_POST['full_name']);
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            $error = 'Semua field harus diisi';
        } elseif (strlen($password) < 8) {
            $error = 'Password minimal 8 karakter';
        } elseif ($password !== $confirm_password) {
            $error = 'Konfirmasi password tidak cocok';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid';
        } else {
            // Check if username or email exists
            $existing = fetchOne("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
            
            if ($existing) {
                $error = 'Username atau email sudah terdaftar';
            } else {
                // Insert new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, 'customer')";
                query($sql, [$username, $email, $password_hash, $full_name]);
                
                // Get user ID
                $user = fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
                
                // Generate welcome voucher & initialize cashback
                generateWelcomeVoucher($user['id'], $email);
                
                $success = 'Registrasi berhasil! Anda mendapat voucher welcome 10% diskon. Silakan login.';
                
                // Auto redirect after 2 seconds
                header("refresh:2;url=login.php");
            }
        }
    }
}
?>

<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.auth-box {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
    max-width: 450px;
    width: 100%;
}

.auth-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 40px 30px;
    text-align: center;
}

.auth-header h1 {
    font-size: 2rem;
    margin: 0 0 10px 0;
}

.auth-header p {
    margin: 0;
    opacity: 0.9;
}

.auth-body {
    padding: 40px 30px;
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
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.auth-footer {
    text-align: center;
    padding: 20px 30px 30px;
    color: #666;
}

.auth-footer a {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
}

.auth-footer a:hover {
    text-decoration: underline;
    color: #059669;
}

.benefits-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #f0f4f8 100%);
    padding: 20px;
    border-radius: 12px;
    margin-top: 25px;
}

.benefits-section h3 {
    margin: 0 0 15px 0;
    color: #1f2937;
    font-size: 1.1rem;
    font-weight: 700;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 12px;
}

.benefit-icon {
    font-size: 1.8rem;
    min-width: 40px;
    text-align: center;
}

.benefit-text {
    flex: 1;
}

.benefit-text strong {
    display: block;
    color: #059669;
    margin-bottom: 3px;
    font-size: 1rem;
    font-weight: 700;
}

.benefit-text p {
    margin: 0;
    font-size: 0.85rem;
    color: #374151;
    font-weight: 500;
}

/* Dark mode styles */
body.dark-mode .benefits-section {
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
}

body.dark-mode .benefits-section h3 {
    color: #f9fafb;
}

body.dark-mode .benefit-text strong {
    color: #34d399;
}

body.dark-mode .benefit-text p {
    color: #e5e7eb;
}
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1><i class="fas fa-laptop"></i> <?php echo SITE_NAME; ?></h1>
            <p>Buat akun baru</p>
        </div>
        
        <div class="auth-body">
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
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-user"></i> Nama Lengkap
                    </label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           placeholder="Nama lengkap" required value="<?php echo $_POST['full_name'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user-circle"></i> Username
                    </label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Username" required value="<?php echo $_POST['username'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="nama@email.com" required value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Minimal 8 karakter" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Konfirmasi Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Ketik ulang password" required>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>

            <!-- Benefits Section -->
            <div class="benefits-section">
                <h3><i class="fas fa-gift"></i> Benefit Member Baru</h3>
                <div class="benefit-item">
                    <div class="benefit-icon">🎟️</div>
                    <div class="benefit-text">
                        <strong>Voucher Diskon 10%</strong>
                        <p>Dapatkan voucher welcome senilai 10% untuk pembelian pertama Anda</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <div class="benefit-text">
                        <strong>Program Cashback</strong>
                        <p>Kumpulkan cashback dari setiap transaksi untuk digunakan di pembelian berikutnya</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">⭐</div>
                    <div class="benefit-text">
                        <strong>Member Exclusive</strong>
                        <p>Akses ke penawaran eksklusif dan promo spesial member</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
