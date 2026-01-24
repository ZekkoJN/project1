<?php
$page_title = "Login";
require_once 'includes/header.php';
require_once 'includes/voucher_functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = 'Invalid request';
    } else {
        $email = clean($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $error = 'Semua field harus diisi';
        } else {
            // Fetch user without status check first
            $user = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if (!$user) {
                $error = 'Email atau password salah';
            } elseif ($user['role'] === 'admin') {
                $error = 'Admin harus login di <a href="login-admin.php">halaman admin</a>';
            } elseif ($user['status'] !== 'active') {
                $error = 'Akun Anda tidak aktif. Silakan hubungi administrator.';
            } elseif (!password_verify($password, $user['password_hash'])) {
                $error = 'Email atau password salah';
            } else {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Check if first login (welcome notification)
                $last_login = $user['last_login'];
                
                // Update last login
                execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
                
                // Set session flag if first login
                if ($last_login === null) {
                    $_SESSION['is_first_login'] = true;
                }
                
                // Redirect to customer index
                redirect('index.php');
            }
        }
    }
}
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.auth-container {
    min-height: 100vh;
    display: flex;
    position: relative;
    overflow: hidden;
}

/* Beautiful dark background with stars and moon */
.auth-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, 
        #0F2027 0%,
        #203A43 30%,
        #2C5364 60%,
        #1a1a2e 100%
    );
    z-index: 0;
}

/* Stars effect */
.auth-background::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, white, transparent),
        radial-gradient(2px 2px at 60px 70px, white, transparent),
        radial-gradient(1px 1px at 50px 50px, white, transparent),
        radial-gradient(1px 1px at 130px 80px, white, transparent),
        radial-gradient(2px 2px at 90px 10px, white, transparent),
        radial-gradient(1px 1px at 110px 120px, white, transparent);
    background-repeat: repeat;
    background-size: 200px 200px;
    animation: twinkle 5s ease-in-out infinite;
    opacity: 0.6;
    z-index: 0;
}

@keyframes twinkle {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 0.3; }
}

/* Moon */
.auth-background::after {
    content: '';
    position: absolute;
    top: 80px;
    right: 120px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle at 30% 30%, #fff 0%, #f4f4f4 50%, #d3d3d3 100%);
    border-radius: 50%;
    box-shadow: 0 0 40px rgba(255, 255, 255, 0.4);
    opacity: 0.9;
    z-index: 0;
}

/* Left side content */
.auth-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 80px;
    color: white;
    position: relative;
    z-index: 10;
}

.auth-left h1 {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 30px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.auth-left h1 .highlight {
    font-style: italic;
    font-weight: 300;
}

.auth-left .tagline {
    font-size: 1.2rem;
    opacity: 0.95;
    max-width: 500px;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Right side - Login form panel */
.auth-right {
    width: 480px;
    background: white;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 20;
    box-shadow: -10px 0 40px rgba(0, 0, 0, 0.2);
}

.auth-box {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 50px 45px;
    overflow-y: auto;
}

.auth-header {
    margin-bottom: 30px;
}

.auth-header h2 {
    font-size: 2rem;
    color: #1f2937;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.auth-header p {
    color: #6b7280;
    font-size: 1rem;
}

.auth-body {
    flex: 1;
}

.divider {
    text-align: center;
    color: #9ca3af;
    margin: 25px 0;
    font-size: 0.9rem;
    position: relative;
}

.divider::before,
.divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 45%;
    height: 1px;
    background: #e5e7eb;
}

.divider::before {
    left: 0;
}

.divider::after {
    right: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s;
    background: #f9fafb;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    background: white;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 16px;
    background: #1f2937;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-primary:hover {
    background: #111827;
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(31, 41, 55, 0.2);
}

.alert {
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.alert-danger {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.auth-footer {
    text-align: center;
    padding: 20px 0 0;
    color: #6b7280;
    border-top: 1px solid #e5e7eb;
    margin-top: 30px;
}

.auth-footer a {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
}

.auth-footer a:hover {
    color: #059669;
    text-decoration: underline;
}

.member-benefits {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 1px solid #a7f3d0;
}

.member-benefits h3 {
    margin: 0 0 15px 0;
    color: #065f46;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.member-benefits ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.member-benefits li {
    padding: 8px 0;
    color: #047857;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.member-benefits li:before {
    content: "✓";
    color: #10b981;
    font-weight: bold;
    font-size: 1.1rem;
    flex-shrink: 0;
}

/* Social links */
.social-links {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.3s;
}

.social-link:hover {
    background: #10b981;
    color: white;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1024px) {
    .auth-left {
        padding: 40px 50px;
    }
    
    .auth-left h1 {
        font-size: 2.5rem;
    }
    
    .auth-right {
        width: 420px;
    }
}

@media (max-width: 768px) {
    .auth-container {
        flex-direction: column;
    }
    
    .auth-left {
        padding: 40px 30px;
        min-height: 300px;
    }
    
    .auth-left h1 {
        font-size: 2rem;
    }
    
    .auth-left .tagline {
        font-size: 1rem;
    }
    
    .auth-right {
        width: 100%;
    }
    
    .auth-box {
        padding: 30px 25px;
    }
}

/* Dark mode styles */
body.dark-mode .auth-right {
    background: #1f2937;
}

body.dark-mode .auth-header h2 {
    color: #f9fafb;
}

body.dark-mode .auth-header p {
    color: #9ca3af;
}

body.dark-mode .form-group label {
    color: #e5e7eb;
}

body.dark-mode .form-control {
    background: #374151;
    border-color: #4b5563;
    color: #f9fafb;
}

body.dark-mode .form-control:focus {
    background: #374151;
    border-color: #10b981;
}

body.dark-mode .btn-primary {
    background: #10b981;
}

body.dark-mode .btn-primary:hover {
    background: #059669;
}

body.dark-mode .auth-footer {
    color: #9ca3af;
    border-top-color: #374151;
}

body.dark-mode .member-benefits {
    background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    border-color: #059669;
}

body.dark-mode .member-benefits h3 {
    color: #d1fae5;
}

body.dark-mode .member-benefits li {
    color: #a7f3d0;
}
</style>

<div class="auth-container">
    <!-- Beautiful background -->
    <div class="auth-background"></div>
    
    <!-- Left side - Hero content -->
    <div class="auth-left">
        <h1>
            Wujudkan belanja impian<br>
            menjadi <span class="highlight">kenyataan</span>
        </h1>
        <p class="tagline">
            Bergabunglah dengan ribuan pelanggan yang telah mempercayai kami untuk mendapatkan Produk Teknologi berkualitas dengan harga terbaik.
        </p>
    </div>
    
    <!-- Right side - Login form -->
    <div class="auth-right">
        <div class="auth-box">
            <div class="auth-header">
                <h2>
                    <i class="fas fa-hand-paper"></i> Selamat datang kembali!
                </h2>
                <p>Silakan login ke akun Anda</p>
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

                <!-- Member Benefits Info -->
                <div class="member-benefits">
                    <h3>
                        <i class="fas fa-crown"></i> Keuntungan Member
                    </h3>
                    <ul>
                        <li><strong>Voucher Diskon 10%</strong> - Dapatkan saat mendaftar</li>
                        <li><strong>Program Cashback</strong> - Kumpulkan cashback dari transaksi</li>
                        <li><strong>Promo Eksklusif</strong> - Penawaran spesial hanya untuk member</li>
                    </ul>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label for="email">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" class="form-control" 
                               placeholder="nama@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            Password
                        </label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Masukkan password Anda" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        Login ke Akun
                    </button>
                </form>
                
                <div class="auth-footer">
                    Belum punya akun? <a href="register.php">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require_once 'includes/footer.php'; ?>
