# TechHub - E-Commerce Elektronik Komputer & Laptop

Website e-commerce lengkap dengan PHP, MySQL, sistem login, CRUD, keranjang belanja, checkout, dan generate PDF invoice.

## 🚀 Fitur Utama

### Customer Features
- ✅ Registrasi & Login dengan session management
- ✅ Browse & search produk dengan filter kategori (35 produk)
- ✅ Product detail page dengan reviews & ratings
- ✅ Shopping cart (keranjang belanja) dengan AJAX
- ✅ Checkout & order management
- ✅ Profile management
- ✅ Order history dengan status tracking
- ✅ Download invoice PDF
- ✅ Product reviews & ratings display
- ✅ Related products recommendation
- ✅ Quick buy functionality

### Admin Features
- ✅ Admin dashboard dengan statistik lengkap
- ✅ CRUD Produk (Create, Read, Update, Delete)
- ✅ Product image upload & management
- ✅ Manajemen kategori
- ✅ Manajemen pesanan (view & update status)
- ✅ Order filtering by status
- ✅ Manajemen user (role management & delete)
- ✅ User statistics & search
- ✅ Generate sales report PDF
- ✅ Generate invoice PDF per order
- ✅ Low stock alerts
- ✅ Order statistics dashboard

### Security Features
- ✅ Password hashing (bcrypt)
- ✅ CSRF token protection
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input validation & sanitization
- ✅ Role-based access control

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (recommended)

## 📊 Database Content

**Total Produk:** 35 items (dari file HTML lama)

| Kategori | Jumlah | Contoh |
|----------|--------|--------|
| 💻 Laptop | 9 | ASUS TUF Gaming, Acer Nitro, Gaming Pro |
| 🖥️ PC Desktop | 9 | Gaming PC Beast, Workstation Pro, Office PC |
| 🎮 GPU | 6 | RTX 4090, RTX 4080, RTX 4070 Ti |
| 🖥️ Monitor | 3 | Gaming 144Hz, Professional 4K, IPS 60Hz |
| ⌨️ Keyboard | 3 | Mechanical RGB, Wireless Pro, Compact |
| 🖱️ Mouse | 3 | Gaming Pro, Wireless, Ultra Precision |
| 🎧 Aksesori | 2 | RGB Headset, Casing Premium |

**Total:** 35 produk lengkap dengan harga, stok, dan deskripsi detail.

**📘 Detail lengkap:** Lihat file `ANALISIS_DAN_PERUBAHAN.md`

## 📦 Installation

### 1. Setup Database

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru: `techhub_db`
3. Import file database dengan urutan:
   - **STEP 1**: Import `database/schema.sql` (struktur database)
     - Klik tab "Import" → Pilih file `schema.sql` → Klik "Go"
   - **STEP 2**: Import `database/insert_all_products.sql` (data 35 produk)
     - Klik tab "Import" → Pilih file `insert_all_products.sql` → Klik "Go"

4. Verifikasi: Total 35 produk dan 7 kategori harus ter-import

**📘 Panduan lengkap:** Lihat file `database/README_IMPORT.md`

### 2. Konfigurasi Database

Edit file `config/database.php` sesuaikan dengan setting MySQL Anda:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'techhub_db');
define('DB_USER', 'root');        // Ganti jika berbeda
define('DB_PASS', '');            // Ganti jika ada password
```

### 3. Setup Folder Upload

Buat folder untuk upload gambar produk:

```
Project7/
└── uploads/
    └── products/
```

Set permission (Linux/Mac):
```bash
chmod -R 755 uploads/
```

### 4. Konfigurasi Site URL

Edit file `config/config.php` line 8:

```php
define('SITE_URL', 'http://localhost/Project7');
```

Sesuaikan dengan URL lokal Anda.

### 5. Copy Gambar Produk

Copy gambar dari folder `img/` ke `uploads/products/` untuk produk sample

### 6. Akses Website

- **Customer Area**: http://localhost/Project7/index.php
- **Admin Panel**: http://localhost/Project7/admin/dashboard.php

## 👤 Default Login

### Admin Account
- Email: `admin@techhub.com`
- Password: `admin123`

### Test Customer (Create via Register)
- Buat akun baru di: http://localhost/Project7/register.php

## 📂 Struktur File

```
Project7/
├── config/
│   ├── database.php          # Database connection
│   └── config.php            # General configuration
├── includes/
│   ├── header.php            # HTML head & config
│   ├── footer.php            # Footer section
│   └── navbar.php            # Navigation bar
├── admin/
│   ├── dashboard.php         # ✅ Admin dashboard
│   ├── products.php          # ✅ Product list (CRUD)
│   ├── product_add.php       # ✅ Add product
│   ├── product_edit.php      # ✅ Edit product
│   ├── orders.php            # ✅ Order management
│   └── users.php             # ✅ User management
├── pdf/
│   ├── invoice.php           # Generate invoice PDF
│   └── sales_report.php      # Generate sales report
├── api/
│   └── cart_add.php          # AJAX add to cart
├── database/
│   └── schema.sql            # Database schema
├── uploads/
│   └── products/             # Product images
├── img/                      # Static images
├── index.php                 # ✅ Homepage (35 products)
├── login.php                 # ✅ Login page
├── register.php              # ✅ Registration page
├── logout.php                # ✅ Logout handler
├── cart.php                  # ✅ Shopping cart
├── checkout.php              # ✅ Checkout page
├── orders.php                # ✅ User order history
├── profile.php               # ✅ User profile
├── promo.php                 # ✅ Promo page
├── contact.php               # ✅ Contact page
├── product_detail.php        # ✅ Product detail page
├── styles.css                # Main stylesheet
├── script.js                 # Main JavaScript
└── README.md                 # This file
```

## 🔧 Troubleshooting

### Error: Connection failed
- Pastikan MySQL sudah running
- Cek username/password di `config/database.php`
- Pastikan database `techhub_db` sudah dibuat

### Error: Cannot write to uploads/
- Buat folder `uploads/products/` manual
- Set permission 755 atau 777

### Error: Page not found
- Pastikan `SITE_URL` di `config/config.php` sudah benar
- Gunakan `http://localhost/Project7` bukan `http://localhost/`

### Error: Session not working
- Pastikan PHP session enabled
- Clear browser cache & cookies

## 📚 Cara Penggunaan

### Untuk Customer:

1. **Register**: Buat akun baru di halaman register
2. **Login**: Masuk dengan email & password
3. **Browse**: Lihat produk di halaman home atau products
4. **Add to Cart**: Klik tombol cart pada produk
5. **Checkout**: Klik checkout, isi alamat & pilih payment
6. **Order History**: Lihat pesanan di menu "Pesanan"
7. **Download Invoice**: Klik "Lihat Invoice" di detail pesanan

### Untuk Admin:

1. **Login**: Login dengan akun admin
2. **Dashboard**: Lihat statistik & overview
3. **Add Product**: Admin > Produk > Tambah Produk
4. **Edit Product**: Klik tombol Edit pada produk
5. **Delete Product**: Klik tombol Hapus (konfirmasi)
6. **Manage Orders**: Lihat & update status pesanan
7. **Generate Report**: Pilih range tanggal & download PDF

## 🎨 Customization

### Ubah Logo/Brand Name
Edit di `config/config.php`:
```php
define('SITE_NAME', 'TechHub');
```

### Ubah Warna Theme
Edit di `styles.css`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### Ubah Email Admin
Edit di `config/config.php`:
```php
define('ADMIN_EMAIL', 'admin@techhub.com');
```

## 📄 License

© 2025 TechHub. All rights reserved.

Dikembangkan oleh: **Zekko Jotty Nugroho** | NIM: **A12.2022.06860**

## 🆘 Support

Jika ada pertanyaan atau issue, silakan buat issue di repository ini.

---

**Happy Coding! 🚀**
