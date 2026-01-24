-- TechHub Database Schema
-- Created: January 2026

CREATE DATABASE IF NOT EXISTS techhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techhub_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    brand VARCHAR(100),
    description TEXT,
    specifications JSON,
    price DECIMAL(12,2) NOT NULL,
    discount_price DECIMAL(12,2) NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    images JSON,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_price (price)
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('transfer', 'cod', 'ewallet') NOT NULL,
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    shipping_address TEXT NOT NULL,
    shipping_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_user (user_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- Promos table
CREATE TABLE IF NOT EXISTS promos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    discount_percent INT NOT NULL,
    promo_code VARCHAR(50) UNIQUE,
    banner_image VARCHAR(255),
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (promo_code),
    INDEX idx_dates (valid_from, valid_until)
) ENGINE=InnoDB;

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB;

-- Member Vouchers table (untuk welcome bonus & cashback)
CREATE TABLE IF NOT EXISTS member_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    voucher_code VARCHAR(50) UNIQUE NOT NULL,
    voucher_type ENUM('discount_percent', 'discount_fixed', 'cashback') DEFAULT 'discount_percent',
    amount INT NOT NULL COMMENT 'Percentage (%) atau fixed amount (Rp)',
    min_purchase DECIMAL(12,2) DEFAULT 0 COMMENT 'Minimum purchase amount',
    description VARCHAR(255),
    is_used BOOLEAN DEFAULT FALSE,
    used_date TIMESTAMP NULL,
    valid_until DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_code (voucher_code),
    INDEX idx_used (is_used)
) ENGINE=InnoDB;

-- Cashback Balance table (untuk tracking cashback member)
CREATE TABLE IF NOT EXISTS cashback_balance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    balance DECIMAL(12,2) DEFAULT 0 COMMENT 'Saldo cashback dalam Rp',
    total_earned DECIMAL(12,2) DEFAULT 0 COMMENT 'Total cashback yang pernah diterima',
    total_used DECIMAL(12,2) DEFAULT 0 COMMENT 'Total cashback yang sudah dipakai',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Guest Orders table (untuk order tanpa akun)
CREATE TABLE IF NOT EXISTS guest_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('transfer', 'cod', 'ewallet') NOT NULL,
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    shipping_address TEXT NOT NULL,
    shipping_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (guest_email),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Guest Order Items table
CREATE TABLE IF NOT EXISTS guest_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (guest_order_id) REFERENCES guest_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (guest_order_id)
) ENGINE=InnoDB;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@techhub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('Laptop', 'laptop', 'Laptop gaming, bisnis, dan multimedia'),
('PC Desktop', 'pc-desktop', 'Komputer desktop rakitan'),
('GPU', 'gpu', 'Graphics Card untuk gaming dan rendering'),
('Monitor', 'monitor', 'Monitor gaming dan profesional'),
('Keyboard', 'keyboard', 'Keyboard mechanical dan wireless'),
('Mouse', 'mouse', 'Mouse gaming dan office'),
('Aksesori', 'aksesoris', 'Aksesoris komputer dan laptop');

-- Insert all products dari file HTML lama
-- LAPTOP PRODUCTS (9 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(1, 'ASUS TUF Gaming A16', 'asus-tuf-gaming-a16', 'ASUS', 'Laptop gaming powerful dengan prosesor terbaru dan GPU RTX 4060', 15999000, NULL, 15, 'Asus-A16-FA608.png', 'active', 245),
(1, 'Acer Nitro V 16S', 'acer-nitro-v-16s', 'Acer', 'Performa tinggi untuk gaming dan workstation', 14999000, NULL, 12, 'Nitro-V-16S-AN16S-61.png', 'active', 189),
(1, 'Gaming Laptop Pro', 'gaming-laptop-pro', 'Gaming Pro', 'Laptop gaming dengan display 165Hz dan kolaborasi brand ternama', 16999000, NULL, 20, 'Gambarlaptop1.jpeg', 'active', 312),
(1, 'Professional Laptop', 'professional-laptop', 'Professional', 'Ideal untuk profesional dan content creator', 12999000, NULL, 18, 'Gambarlaptop2.jpeg', 'active', 156),
(1, 'Ultra Gaming Beast', 'ultra-gaming-beast', 'Ultra Gaming', 'Monster gaming dengan spesifikasi tertinggi', 17999000, NULL, 10, 'Gambarlaptop3.jfif', 'active', 298),
(1, 'Gaming Master', 'gaming-master', 'Gaming Master', 'Performa maksimal untuk gamer sejati', 15499000, NULL, 14, 'Gambarlaptop4.jfif', 'active', 267),
(1, 'High Performance Gaming', 'high-performance-gaming', 'High Performance', 'Display 4K dengan performa gaming ekstrem', 16499000, NULL, 11, 'Gambarlaptop5.jfif', 'active', 234),
(1, 'Elite Gaming Laptop', 'elite-gaming-laptop', 'Elite Gaming', 'Laptop gaming elite dengan teknologi terdepan', 18999000, NULL, 8, 'Laptop1.jpeg', 'active', 345),
(1, 'Professional Creator', 'professional-creator', 'Professional', 'Untuk creator dan profesional multimedia', 13999000, NULL, 16, 'Laptop2.jpeg', 'active', 198),

-- PC DESKTOP PRODUCTS (9 items)
(2, 'Gaming PC Beast', 'gaming-pc-beast', 'Gaming PC', 'PC desktop gaming ultimate dengan performa maksimal', 24999000, NULL, 8, 'Pc1.jpeg', 'active', 276),
(2, 'Workstation Pro', 'workstation-pro', 'Workstation', 'PC workstation untuk profesional dan rendering', 21999000, NULL, 10, 'pc2.jpeg', 'active', 189),
(2, 'Gaming Server Beast', 'gaming-server-beast', 'Gaming Server', 'Server gaming dengan cooling system advanced', 27999000, NULL, 5, 'Pc3.jpeg', 'active', 312),
(2, 'Office PC', 'office-pc', 'Office PC', 'PC office dengan harga terjangkau', 8999000, NULL, 25, 'Pc4.jpeg', 'active', 145),
(2, 'Creator Workstation', 'creator-workstation', 'Creator', 'Workstation untuk video editing dan 3D rendering', 22999000, NULL, 7, 'Pc5.jpeg', 'active', 267),
(2, 'Streaming PC Pro', 'streaming-pc-pro', 'Streaming PC', 'PC streaming dengan performa stabil', 19999000, NULL, 12, 'Pc6.jpeg', 'active', 234),
(2, 'High-End Gaming PC', 'high-end-gaming-pc', 'High-End Gaming', 'PC gaming high-end dengan RTX 4090', 28999000, NULL, 6, 'Pc7.jpeg', 'active', 345),
(2, 'Budget Gaming PC', 'budget-gaming-pc', 'Budget Gaming', 'PC gaming budget dengan performa baik', 12999000, NULL, 20, 'Pc8.jpeg', 'active', 198),
(2, 'Professional Setup', 'professional-setup', 'Professional', 'Setup profesional untuk workstation premium', 23999000, NULL, 9, 'Pc9.jpeg', 'active', 287),

-- GPU PRODUCTS (6 items)
(3, 'RTX 4090 Beast', 'rtx-4090-beast', 'NVIDIA', 'GPU paling powerful untuk gaming ekstrem', 19999000, NULL, 10, 'Gpu1.jpeg', 'active', 289),
(3, 'RTX 4080 Pro', 'rtx-4080-pro', 'NVIDIA', 'GPU professional untuk workstation', 14999000, NULL, 15, 'Gpu2.jpeg', 'active', 267),
(3, 'RTX 4070 Gaming', 'rtx-4070-gaming', 'NVIDIA', 'GPU gaming mid-range dengan harga kompetitif', 10999000, NULL, 20, 'Gpu3.jpeg', 'active', 245),
(3, 'RTX 4060 Budget', 'rtx-4060-budget', 'NVIDIA', 'GPU budget untuk 1080p gaming smooth', 6999000, NULL, 30, 'Gpu4.jpeg', 'active', 198),
(3, 'RTX 4070 Ti', 'rtx-4070-ti', 'NVIDIA', 'GPU high-end untuk gaming 1440p', 13999000, NULL, 12, 'GPu5.jpeg', 'active', 276),
(3, 'RTX 4060 Ti', 'rtx-4060-ti', 'NVIDIA', 'GPU entry-level gaming 1080p', 8999000, NULL, 25, 'Gpu6.jpeg', 'active', 223),

-- MONITOR PRODUCTS (3 items)
(4, 'Gaming Monitor 144Hz', 'gaming-monitor-144hz', 'Gaming Monitor', 'Monitor 27" 144Hz untuk gaming kompetitif', 3999000, NULL, 30, 'Monitor1.jpeg', 'active', 234),
(4, 'Professional Monitor 4K', 'professional-monitor-4k', 'Professional', 'Monitor 4K untuk color grading dan editing', 8999000, NULL, 15, 'Monitor2.jpeg', 'active', 289),
(4, 'IPS Monitor 60Hz', 'ips-monitor-60hz', 'IPS Monitor', 'Monitor IPS 24" untuk office dan desain', 2499000, NULL, 40, 'Monitor3.jpeg', 'active', 167),

-- KEYBOARD PRODUCTS (3 items)
(5, 'Mechanical Gaming Keyboard', 'mechanical-gaming-keyboard', 'Mechanical', 'Keyboard mechanical RGB dengan switch gaming', 1299000, NULL, 50, 'Keyboard1.jpeg', 'active', 312),
(5, 'Wireless Keyboard Pro', 'wireless-keyboard-pro', 'Wireless', 'Keyboard wireless dengan baterai tahan lama', 899000, NULL, 60, 'Keyboard2.jpeg', 'active', 245),
(5, 'Compact Gaming Keyboard', 'compact-gaming-keyboard', 'Compact', 'Keyboard compact untuk gaming mobile', 799000, NULL, 55, 'Keyboard3.jpeg', 'active', 189),

-- MOUSE PRODUCTS (3 items)
(6, 'Gaming Mouse Pro', 'gaming-mouse-pro', 'Gaming Mouse', 'Mouse gaming dengan DPI tinggi dan presisi sempurna', 799000, NULL, 70, 'Mouse1.jpeg', 'active', 345),
(6, 'Wireless Mouse', 'wireless-mouse', 'Wireless', 'Mouse wireless ergonomis untuk office', 499000, NULL, 80, 'Mouse2.jpeg', 'active', 267),
(6, 'Ultra Precision Gaming', 'ultra-precision-gaming', 'Ultra Precision', 'Mouse dengan sensor optik presisi tinggi', 899000, NULL, 65, 'Mouse3.jpeg', 'active', 298),

-- ACCESSORIES PRODUCTS (2 items)
(7, 'RGB Gaming Headset', 'rgb-gaming-headset', 'RGB Gaming', 'Headset gaming dengan surround sound 7.1', 1499000, NULL, 45, 'accessories.jpg', 'active', 276),
(7, 'Casing PC Premium', 'casing-pc-premium', 'Premium Casing', 'Casing PC dengan airflow optimal dan desain modern', 2999000, NULL, 35, 'casing.png', 'active', 198);
