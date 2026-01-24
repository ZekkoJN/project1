<?php
$page_title = "Login Admin";
require_once 'includes/header.php';

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
            // Fetch user
            $user = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if (!$user) {
                $error = 'Email atau password salah';
            } elseif ($user['role'] !== 'admin') {
                $error = 'Hanya admin yang bisa login di sini';
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
                
                // Update last login
                execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
                
                redirect('admin/dashboard.php');
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
    font-size: 0.9rem;
}

.auth-header .admin-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    margin-top: 10px;
    font-size: 0.85rem;
}

.auth-body {
    padding: 40px 30px;
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
    border-top: 1px solid #eee;
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

.info-box {
    background: #d1fae5;
    border-left: 4px solid #10b981;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    color: #065f46;
    font-size: 0.9rem;
}
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1><i class="fas fa-shield-alt"></i> <?php echo SITE_NAME; ?></h1>
            <p>Admin Portal</p>
            <div class="admin-badge"><i class="fas fa-lock"></i> Akses Terbatas</div>
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

            <div class="info-box">
                <i class="fas fa-info-circle"></i> <strong>Admin Only</strong> - Hanya administrator yang dapat login di halaman ini
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="nama@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login Admin
                </button>
            </form>
        </div>
        
        <div class="auth-footer">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali ke Login Customer</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
