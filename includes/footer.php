    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Tentang Kami</h4>
                    <p>TechHub - Toko elektronik online berkualitas.</p>
                </div>
                <div class="footer-section">
                    <h4>Kontak Kami</h4>
                    <p>Email: info@techhub.com</p>
                    <p>Telepon: 0812-3456-7890</p>
                    <p>Alamat: Jl. Teknologi No. 123, Jakarta, Indonesia</p>
                </div>
                <div class="footer-section">
                    <h4>Ikuti Kami</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 TechHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modal untuk detail produk -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <span class="close" onclick="closeProductModal()">&times;</span>
            <div class="modal-body">
                <img id="modalImage" src="" alt="">
                <div class="modal-info">
                    <h2 id="modalTitle"></h2>
                    <p id="modalCategory" class="modal-category"></p>
                    <div class="modal-rating">
                        <div class="stars" id="modalStars"></div>
                        <span id="modalReviewCount"></span>
                    </div>
                    <p id="modalDescription" class="modal-description"></p>
                    <h3 class="modal-price" id="modalPrice"></h3>
                    <?php if (isLoggedIn()): ?>
                        <button class="add-to-cart-btn" id="modalAddToCart">Tambah ke Keranjang</button>
                    <?php else: ?>
                        <button class="add-to-cart-btn" onclick="window.location.href='login.php'">Login untuk Membeli</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Product data from PHP for modal (override script.js products array)
    <?php
    // Fetch all products if on index page
    if (basename($_SERVER['PHP_SELF']) == 'index.php') {
        $all_prods = fetchAll("SELECT * FROM products WHERE status = 'active'");
        $category_map = [
            1 => 'laptop', 2 => 'pc', 3 => 'gpu', 4 => 'monitor',
            5 => 'keyboard', 6 => 'mouse', 7 => 'accessories'
        ];
        echo "const productsFromPHP = " . json_encode(array_map(function($p) use ($category_map) {
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'category' => $category_map[$p['category_id']] ?? 'all',
                'price' => $p['discount_price'] ?? $p['price'],
                'image' => 'img/' . $p['image'],
                'rating' => 4.5,
                'reviews' => $p['views'],
                'description' => $p['description'] ?? 'Produk berkualitas tinggi'
            ];
        }, $all_prods)) . ";\n";
    }
    ?>
    
    // Override openProductModal to use PHP data
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
    function openProductModal(productId) {
        const product = productsFromPHP.find(p => p.id === productId);
        if (!product) return;

        document.getElementById('modalImage').src = product.image;
        document.getElementById('modalTitle').textContent = product.name;
        document.getElementById('modalCategory').textContent = product.category.charAt(0).toUpperCase() + product.category.slice(1);
        document.getElementById('modalStars').innerHTML = '★★★★★';
        document.getElementById('modalReviewCount').textContent = `${product.reviews} ulasan`;
        document.getElementById('modalDescription').textContent = product.description;
        document.getElementById('modalPrice').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(product.price)}`;
        
        <?php if (isLoggedIn()): ?>
        const addBtn = document.getElementById('modalAddToCart');
        if (addBtn) {
            addBtn.onclick = () => addToCartFromModal(productId);
        }
        <?php endif; ?>
        
        document.getElementById('productModal').classList.add('active');
    }

    // Add to cart from modal
    function addToCartFromModal(productId) {
        fetch('api/cart_add.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produk berhasil ditambahkan ke keranjang!');
                location.reload();
            } else {
                alert('Gagal menambahkan produk: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
    <?php endif; ?>
    </script>
    <script src="script.js"></script>
    <script>
        // Initialize theme immediately after script.js loads
        // This ensures checkTheme is available
        window.addEventListener('load', function() {
            console.log('Window load event - calling checkTheme()');
            if (typeof checkTheme === 'function') {
                checkTheme();
            } else {
                console.warn('checkTheme function not found');
            }
        });
        
        // Also try on DOMContentLoaded as backup
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event - calling checkTheme()');
            if (typeof checkTheme === 'function') {
                checkTheme();
            }
        });
    </script>
    <script src="atur1.js"></script>
</body>
</html>
