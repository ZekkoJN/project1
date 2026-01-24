-- ========================================
-- INSERT DATA LENGKAP UNTUK TECHHUB
-- Total: 35 Produk dari file HTML lama
-- ========================================

USE techhub_db;

-- Hapus data lama jika ada
DELETE FROM order_items;
DELETE FROM orders;
DELETE FROM reviews;
DELETE FROM cart;
DELETE FROM wishlist;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM users WHERE username != 'admin';

-- Reset auto increment
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;

-- ========================================
-- INSERT CATEGORIES (7 kategori)
-- ========================================
INSERT INTO categories (name, slug, description) VALUES
('Laptop', 'laptop', 'Laptop gaming, bisnis, dan multimedia'),
('PC Desktop', 'pc-desktop', 'Komputer desktop rakitan'),
('GPU', 'gpu', 'Graphics Card untuk gaming dan rendering'),
('Monitor', 'monitor', 'Monitor gaming dan profesional'),
('Keyboard', 'keyboard', 'Keyboard mechanical dan wireless'),
('Mouse', 'mouse', 'Mouse gaming dan office'),
('Aksesori', 'aksesoris', 'Aksesoris komputer dan laptop');

-- ========================================
-- INSERT PRODUCTS (35 produk)
-- ========================================

-- LAPTOP PRODUCTS (9 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(1, 'ASUS TUF Gaming A16', 'asus-tuf-gaming-a16', 'ASUS', 'Laptop gaming powerful dengan prosesor terbaru dan GPU RTX 4060. Dilengkapi dengan layar 16 inch refresh rate tinggi, RAM 16GB DDR5, dan SSD NVMe 512GB. Sistem pendingin advanced untuk gaming marathon.', 15999000, 14999000, 15, 'Asus-A16-FA608.png', 'active', 245),

(1, 'Acer Nitro V 16S', 'acer-nitro-v-16s', 'Acer', 'Performa tinggi untuk gaming dan workstation. Processor Intel Core i7 Gen 13, NVIDIA GeForce RTX 4050, RAM 16GB, SSD 512GB. Layar IPS 16 inch dengan teknologi ComfyView untuk mengurangi eye strain.', 14999000, 13999000, 12, 'Nitro-V-16S-AN16S-61.png', 'active', 189),

(1, 'Gaming Laptop Pro', 'gaming-laptop-pro', 'Gaming Pro', 'Laptop gaming dengan display 165Hz dan kolaborasi brand ternama. Prosesor AMD Ryzen 9, RTX 4070, RAM 32GB DDR5. Audio premium dengan Dolby Atmos. RGB keyboard mechanical switches.', 16999000, 15999000, 20, 'Gambarlaptop1.jpeg', 'active', 312),

(1, 'Professional Laptop', 'professional-laptop', 'Professional', 'Ideal untuk profesional dan content creator. Intel Core i7 vPro, NVIDIA RTX 3060, RAM 32GB, SSD 1TB. Layar 4K OLED dengan color accuracy 100% sRGB. Baterai tahan hingga 12 jam.', 12999000, 11999000, 18, 'Gambarlaptop2.jpeg', 'active', 156),

(1, 'Ultra Gaming Beast', 'ultra-gaming-beast', 'Ultra Gaming', 'Monster gaming dengan spesifikasi tertinggi. Intel Core i9 Gen 14, RTX 4080, RAM 64GB DDR5, SSD 2TB NVMe Gen 4. Liquid cooling system, RGB premium, dan layar QHD 240Hz.', 17999000, NULL, 10, 'Gambarlaptop3.jfif', 'active', 298),

(1, 'Gaming Master', 'gaming-master', 'Gaming Master', 'Performa maksimal untuk gamer sejati. AMD Ryzen 7, RTX 4060 Ti, RAM 16GB, SSD 1TB. Desain aggressive dengan RGB multizone, audio THX Spatial, dan cooling system triple fan.', 15499000, 14499000, 14, 'Gambarlaptop4.jfif', 'active', 267),

(1, 'High Performance Gaming', 'high-performance-gaming', 'High Performance', 'Display 4K dengan performa gaming ekstrem. Intel Core i9, RTX 4070 Ti, RAM 32GB, SSD 1TB. Mini LED display dengan HDR 1000, mechanical keyboard, dan Thunderbolt 4.', 16499000, NULL, 11, 'Gambarlaptop5.jfif', 'active', 234),

(1, 'Elite Gaming Laptop', 'elite-gaming-laptop', 'Elite Gaming', 'Laptop gaming elite dengan teknologi terdepan. AMD Ryzen 9, RTX 4090, RAM 64GB, SSD 2TB. Layar QHD+ 300Hz, vapor chamber cooling, dan design premium dengan magnesium alloy.', 18999000, 17999000, 8, 'Laptop1.jpeg', 'active', 345),

(1, 'Professional Creator', 'professional-creator', 'Professional', 'Untuk creator dan profesional multimedia. Intel Core i7, NVIDIA RTX A3000, RAM 32GB ECC, SSD 2TB. Layar 4K IPS DCI-P3 100%, port lengkap, dan sertifikasi ISV untuk aplikasi profesional.', 13999000, NULL, 16, 'Laptop2.jpeg', 'active', 198);

-- PC DESKTOP PRODUCTS (9 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(2, 'Gaming PC Beast', 'gaming-pc-beast', 'Gaming PC', 'PC desktop gaming ultimate dengan performa maksimal. Intel Core i9-14900K, RTX 4090 24GB, RAM 64GB DDR5, SSD 2TB Gen 4. Custom water cooling RGB, casing premium dengan tempered glass.', 24999000, 22999000, 8, 'Pc1.jpeg', 'active', 276),

(2, 'Workstation Pro', 'workstation-pro', 'Workstation', 'PC workstation untuk profesional dan rendering. AMD Ryzen Threadripper PRO, RTX A6000 48GB, RAM 128GB ECC, SSD 4TB NVMe RAID. PSU Platinum 1200W, cooling system advanced.', 21999000, NULL, 10, 'pc2.jpeg', 'active', 189),

(2, 'Gaming Server Beast', 'gaming-server-beast', 'Gaming Server', 'Server gaming dengan cooling system advanced. Dual Intel Xeon, RTX 4080 x2 SLI, RAM 256GB DDR5 ECC, SSD 8TB Enterprise. Redundant PSU, liquid cooling custom loop.', 27999000, 25999000, 5, 'Pc3.jpeg', 'active', 312),

(2, 'Office PC', 'office-pc', 'Office PC', 'PC office dengan harga terjangkau. Intel Core i5-13400, Intel UHD Graphics 730, RAM 16GB DDR4, SSD 512GB. Compact case, WiFi 6, dan efisiensi power tinggi untuk penggunaan kantor.', 8999000, 7999000, 25, 'Pc4.jpeg', 'active', 145),

(2, 'Creator Workstation', 'creator-workstation', 'Creator', 'Workstation untuk video editing dan 3D rendering. AMD Ryzen 9 7950X, RTX 4080 16GB, RAM 64GB DDR5, SSD 2TB + HDD 8TB. Color calibrated monitor support, thunderbolt 4.', 22999000, 21499000, 7, 'Pc5.jpeg', 'active', 267),

(2, 'Streaming PC Pro', 'streaming-pc-pro', 'Streaming PC', 'PC streaming dengan performa stabil. Intel Core i7-14700K, RTX 4070 Ti 12GB, RAM 32GB DDR5, SSD 1TB Gen 4. Dual PC streaming setup ready, capture card support, low latency.', 19999000, 18999000, 12, 'Pc6.jpeg', 'active', 234),

(2, 'High-End Gaming PC', 'high-end-gaming-pc', 'High-End Gaming', 'PC gaming high-end dengan RTX 4090. Intel Core i9-14900KS, RTX 4090 24GB OC, RAM 64GB DDR5-6400, SSD 4TB Gen 5. Custom water cooling RGB, PSU Titanium 1600W, casing Lian Li.', 28999000, NULL, 6, 'Pc7.jpeg', 'active', 345),

(2, 'Budget Gaming PC', 'budget-gaming-pc', 'Budget Gaming', 'PC gaming budget dengan performa baik. Intel Core i5-13600KF, RTX 4060 8GB, RAM 16GB DDR5, SSD 512GB. Air cooling RGB, PSU Bronze 650W, casing mesh untuk airflow optimal.', 12999000, 11999000, 20, 'Pc8.jpeg', 'active', 198),

(2, 'Professional Setup', 'professional-setup', 'Professional', 'Setup profesional untuk workstation premium. AMD Ryzen 9 7950X3D, RTX 4070 Ti 12GB, RAM 96GB DDR5, SSD 2TB + NAS 16TB. Multi monitor support, UPS backup, dan certified components.', 23999000, NULL, 9, 'Pc9.jpeg', 'active', 287);

-- GPU PRODUCTS (6 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(3, 'RTX 4090 Beast', 'rtx-4090-beast', 'NVIDIA', 'GPU paling powerful untuk gaming ekstrem. NVIDIA GeForce RTX 4090 24GB GDDR6X, boost clock 2.52GHz, 16384 CUDA cores. Triple fan cooling, RGB lighting, ray tracing gen 3, DLSS 3.0.', 19999000, 18999000, 10, 'Gpu1.jpeg', 'active', 289),

(3, 'RTX 4080 Pro', 'rtx-4080-pro', 'NVIDIA', 'GPU professional untuk workstation. RTX 4080 16GB GDDR6X, boost clock 2.51GHz, 9728 CUDA cores. Advanced cooling, support ray tracing, DLSS 3.0, dan AV1 encoding.', 14999000, NULL, 15, 'Gpu2.jpeg', 'active', 267),

(3, 'RTX 4070 Gaming', 'rtx-4070-gaming', 'NVIDIA', 'GPU gaming mid-range dengan harga kompetitif. RTX 4070 12GB GDDR6X, boost clock 2.48GHz, 5888 CUDA cores. Dual fan cooling, power efficient, perfect untuk 1440p gaming.', 10999000, 9999000, 20, 'Gpu3.jpeg', 'active', 245),

(3, 'RTX 4060 Budget', 'rtx-4060-budget', 'NVIDIA', 'GPU budget untuk 1080p gaming smooth. RTX 4060 8GB GDDR6, boost clock 2.46GHz, 3072 CUDA cores. Compact design, low power consumption, ray tracing support, DLSS 3.0.', 6999000, 5999000, 30, 'Gpu4.jpeg', 'active', 198),

(3, 'RTX 4070 Ti', 'rtx-4070-ti', 'NVIDIA', 'GPU high-end untuk gaming 1440p. RTX 4070 Ti 12GB GDDR6X, boost clock 2.61GHz, 7680 CUDA cores. Triple fan advanced cooling, RGB lighting, overclock ready, ray tracing gen 3.', 13999000, NULL, 12, 'GPu5.jpeg', 'active', 276),

(3, 'RTX 4060 Ti', 'rtx-4060-ti', 'NVIDIA', 'GPU entry-level gaming 1080p. RTX 4060 Ti 8GB GDDR6, boost clock 2.54GHz, 4352 CUDA cores. Dual fan cooling, energy efficient, DLSS 3.0 support, perfect untuk competitive gaming.', 8999000, 7999000, 25, 'Gpu6.jpeg', 'active', 223);

-- MONITOR PRODUCTS (3 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(4, 'Gaming Monitor 144Hz', 'gaming-monitor-144hz', 'Gaming Monitor', 'Monitor 27" 144Hz untuk gaming kompetitif. Panel IPS QHD (2560x1440), response time 1ms, refresh rate 144Hz, FreeSync Premium. HDR400, 99% sRGB, VESA mount, height adjustable.', 3999000, 3499000, 30, 'Monitor1.jpeg', 'active', 234),

(4, 'Professional Monitor 4K', 'professional-monitor-4k', 'Professional', 'Monitor 4K untuk color grading dan editing. 32" IPS 4K UHD (3840x2160), 60Hz, Delta E < 2, 100% sRGB, 99% Adobe RGB. Hardware calibration, USB-C 90W PD, KVM switch built-in.', 8999000, NULL, 15, 'Monitor2.jpeg', 'active', 289),

(4, 'IPS Monitor 60Hz', 'ips-monitor-60hz', 'IPS Monitor', 'Monitor IPS 24" untuk office dan desain. Full HD (1920x1080), 60Hz, 99% sRGB, flicker-free, low blue light. VESA mount, pivot support, height adjustable, multiple input ports.', 2499000, 1999000, 40, 'Monitor3.jpeg', 'active', 167);

-- KEYBOARD PRODUCTS (3 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(5, 'Mechanical Gaming Keyboard', 'mechanical-gaming-keyboard', 'Mechanical', 'Keyboard mechanical RGB dengan switch gaming. Hot-swappable mechanical switches, RGB per-key lighting, aluminum frame. N-key rollover, programmable macros, dedicated media keys, USB passthrough.', 1299000, 1099000, 50, 'Keyboard1.jpeg', 'active', 312),

(5, 'Wireless Keyboard Pro', 'wireless-keyboard-pro', 'Wireless', 'Keyboard wireless dengan baterai tahan lama. Tri-mode connection (Bluetooth 5.1, 2.4GHz, USB-C), battery life 200 jam. Low profile switches, compact 75% layout, white backlight.', 899000, NULL, 60, 'Keyboard2.jpeg', 'active', 245),

(5, 'Compact Gaming Keyboard', 'compact-gaming-keyboard', 'Compact', 'Keyboard compact untuk gaming mobile. 60% layout, mechanical switches, RGB backlight. Portable design, detachable USB-C cable, programmable keys, compatible dengan software customization.', 799000, 699000, 55, 'Keyboard3.jpeg', 'active', 189);

-- MOUSE PRODUCTS (3 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(6, 'Gaming Mouse Pro', 'gaming-mouse-pro', 'Gaming Mouse', 'Mouse gaming dengan DPI tinggi dan presisi sempurna. Sensor optik 26000 DPI, polling rate 1000Hz, 8 programmable buttons. RGB lighting, adjustable weight, PTFE feet, braided cable.', 799000, NULL, 70, 'Mouse1.jpeg', 'active', 345),

(6, 'Wireless Mouse', 'wireless-mouse', 'Wireless', 'Mouse wireless ergonomis untuk office. Silent click technology, 2.4GHz wireless, DPI adjustable 800-1600. Battery life 18 bulan, ergonomic design, 5 button, nano receiver.', 499000, 399000, 80, 'Mouse2.jpeg', 'active', 267),

(6, 'Ultra Precision Gaming', 'ultra-precision-gaming', 'Ultra Precision', 'Mouse dengan sensor optik presisi tinggi. PAW3395 sensor 30000 DPI, polling rate 8000Hz, ultra-lightweight 59g. Honeycomb shell design, RGB lighting, 6 programmable buttons, paracord cable.', 899000, 799000, 65, 'Mouse3.jpeg', 'active', 298);

-- ACCESSORIES PRODUCTS (2 items)
INSERT INTO products (category_id, name, slug, brand, description, price, discount_price, stock, image, status, views) VALUES
(7, 'RGB Gaming Headset', 'rgb-gaming-headset', 'RGB Gaming', 'Headset gaming dengan surround sound 7.1. 50mm drivers, virtual 7.1 surround, detachable noise-canceling mic. RGB lighting, memory foam earcups, multi-platform compatible, USB + 3.5mm.', 1499000, 1299000, 45, 'accessories.jpg', 'active', 276),

(7, 'Casing PC Premium', 'casing-pc-premium', 'Premium Casing', 'Casing PC dengan airflow optimal dan desain modern. Mid-tower ATX, tempered glass panel, 6 RGB fans included. Cable management, dust filters, support 360mm radiator, USB-C front panel.', 2999000, NULL, 35, 'casing.png', 'active', 198);

-- ========================================
-- VERIFICATION QUERIES
-- ========================================
-- Cek jumlah data yang ter-insert
SELECT 'Categories' as table_name, COUNT(*) as total FROM categories
UNION ALL
SELECT 'Products', COUNT(*) FROM products
UNION ALL
SELECT 'Laptop', COUNT(*) FROM products WHERE category_id = 1
UNION ALL
SELECT 'PC Desktop', COUNT(*) FROM products WHERE category_id = 2
UNION ALL
SELECT 'GPU', COUNT(*) FROM products WHERE category_id = 3
UNION ALL
SELECT 'Monitor', COUNT(*) FROM products WHERE category_id = 4
UNION ALL
SELECT 'Keyboard', COUNT(*) FROM products WHERE category_id = 5
UNION ALL
SELECT 'Mouse', COUNT(*) FROM products WHERE category_id = 6
UNION ALL
SELECT 'Aksesori', COUNT(*) FROM products WHERE category_id = 7;

-- Lihat semua produk
SELECT 
    p.id,
    c.name as category,
    p.name,
    p.brand,
    FORMAT(p.price, 0) as price,
    FORMAT(p.discount_price, 0) as discount,
    p.stock,
    p.views
FROM products p
JOIN categories c ON p.category_id = c.id
ORDER BY c.id, p.id;

SELECT '✅ INSERT DATA LENGKAP BERHASIL!' as status;
SELECT '📦 Total 35 Produk telah ditambahkan ke database' as info;
