<!-- Header/Navigation -->
<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-laptop"></i>
                    <span><?php echo SITE_NAME; ?></span>
                </a>
                <span id="clock" style="font-size: 0.8rem; color: var(--primary-color); margin-left: 10px;"></span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php#products" class="nav-link">Produk</a></li>
                <li><a href="promo.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'promo.php' ? 'active' : ''; ?>">Promo</a></li>
                <li><a href="index.php#reviews" class="nav-link">Review</a></li>
                <li><a href="index.php#faq" class="nav-link">FAQ</a></li>
                <li><a href="contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Kontak</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="orders.php" class="nav-link">Pesanan</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/dashboard.php" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="nav-right">
                <!-- Cart Icon (for both member and guest) -->
                <a href="cart.php" class="cart-btn" style="position: relative; margin-right: 10px;">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $cart_count = 0;
                    if (isLoggedIn() && isset($_SESSION['user_id'])) {
                        $cart_result = fetchOne("SELECT COUNT(*) as count FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
                        $cart_count = $cart_result['count'] ?? 0;
                    } elseif (isset($_SESSION['cart'])) {
                        $cart_count = count($_SESSION['cart']);
                    }
                    ?>
                    <span id="cartBadge" style="position: absolute; top: -8px; right: -8px; background: #ff6b6b; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; <?php echo $cart_count === 0 ? 'display: none;' : ''; ?>"><?php echo $cart_count; ?></span>
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown" style="display: inline-block; position: relative;">
                        <button class="contact-btn" id="userMenuBtn" style="cursor: pointer;">
                            <i class="fas fa-user"></i> <?php echo clean($_SESSION['username'] ?? 'User'); ?>
                        </button>
                        <div id="userDropdown" class="dropdown-content" style="display: none; position: absolute; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; padding: 10px; min-width: 150px; z-index: 1000; right: 0; top: 100%;">
                            <a href="profile.php" style="display: block; padding: 8px; text-decoration: none; color: #333;"><i class="fas fa-user-circle"></i> Profil</a>
                            <a href="orders.php" style="display: block; padding: 8px; text-decoration: none; color: #333;"><i class="fas fa-box"></i> Pesanan</a>
                            <a href="logout.php" style="display: block; padding: 8px; text-decoration: none; color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="cart-btn" title="Login">
                        <i class="fas fa-sign-in-alt"></i>
                    </a>
                    <button class="contact-btn" onclick="window.location.href='contact.php'">CONTACT US</button>
                <?php endif; ?>
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>
</header>

<style>
/* Dropdown menu styling with dark mode support */
#userDropdown {
    background: white !important;
    color: #333 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
}

#userDropdown a {
    color: #333 !important;
}

#userDropdown a:hover {
    background-color: #f0f0f0 !important;
    border-radius: 4px;
}

#userDropdown a i {
    margin-right: 8px;
}

/* Dark mode dropdown styling */
body.dark-mode #userDropdown,
html.dark-mode #userDropdown {
    background: #2d2d2d !important;
    color: #ffffff !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5) !important;
}

body.dark-mode #userDropdown a,
html.dark-mode #userDropdown a {
    color: #e0e0e0 !important;
}

body.dark-mode #userDropdown a:hover,
html.dark-mode #userDropdown a:hover {
    background-color: #3d3d3d !important;
    border-radius: 4px;
}

body.dark-mode #userDropdown a[href="logout.php"],
html.dark-mode #userDropdown a[href="logout.php"] {
    color: #ff9999 !important;
}

body.dark-mode #userDropdown a[href="logout.php"]:hover,
html.dark-mode #userDropdown a[href="logout.php"]:hover {
    background-color: #3d3d3d !important;
}
</style>

<script>
// Dropdown user menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.style.display = userDropdown.style.display === 'none' ? 'block' : 'none';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.style.display = 'none';
            }
        });
    }
});
</script>