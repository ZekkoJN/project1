// Data Produk dengan badges
const products = [
    // Laptop
    { id: 1, name: 'ASUS TUF Gaming A16', category: 'laptop', price: 15999000, image: 'img/Asus-A16-FA608.png', rating: 4.8, reviews: 245, description: 'Laptop gaming powerful dengan prosesor terbaru dan GPU RTX 4060', badge: 'hot' },
    { id: 2, name: 'Acer Nitro V 16S', category: 'laptop', price: 14999000, image: 'img/Nitro-V-16S-AN16S-61.png', rating: 4.7, reviews: 189, description: 'Performa tinggi untuk gaming dan workstation', badge: 'new' },
    { id: 3, name: 'Gaming Laptop Pro', category: 'laptop', price: 16999000, image: 'img/Gambarlaptop1.jpeg', rating: 4.9, reviews: 312, description: 'Laptop gaming dengan display 165Hz dan kolaborasi brand ternama', badge: 'recommended' },
    { id: 4, name: 'Professional Laptop', category: 'laptop', price: 12999000, image: 'img/Gambarlaptop2.jpeg', rating: 4.6, reviews: 156, description: 'Ideal untuk profesional dan content creator' },
    { id: 5, name: 'Ultra Gaming Beast', category: 'laptop', price: 17999000, image: 'img/Gambarlaptop3.jfif', rating: 4.9, reviews: 298, description: 'Monster gaming dengan spesifikasi tertinggi', badge: 'hot' },
    { id: 6, name: 'Gaming Master', category: 'laptop', price: 15499000, image: 'img/Gambarlaptop4.jfif', rating: 4.8, reviews: 267, description: 'Performa maksimal untuk gamer sejati' },
    { id: 7, name: 'High Performance Gaming', category: 'laptop', price: 16499000, image: 'img/Gambarlaptop5.jfif', rating: 4.7, reviews: 234, description: 'Display 4K dengan performa gaming ekstrem' },
    { id: 8, name: 'Elite Gaming Laptop', category: 'laptop', price: 18999000, image: 'img/Laptop1.jpeg', rating: 4.9, reviews: 345, description: 'Laptop gaming elite dengan teknologi terdepan', badge: 'recommended' },
    { id: 9, name: 'Professional Creator', category: 'laptop', price: 13999000, image: 'img/Laptop2.jpeg', rating: 4.7, reviews: 198, description: 'Untuk creator dan profesional multimedia' },

    // PC Desktop
    { id: 10, name: 'Gaming PC Beast', category: 'pc', price: 24999000, image: 'img/Pc1.jpeg', rating: 4.8, reviews: 276, description: 'PC desktop gaming ultimate dengan performa maksimal', badge: 'hot' },
    { id: 11, name: 'Workstation Pro', category: 'pc', price: 21999000, image: 'img/pc2.jpeg', rating: 4.7, reviews: 189, description: 'PC workstation untuk profesional dan rendering' },
    { id: 12, name: 'Gaming Server Beast', category: 'pc', price: 27999000, image: 'img/Pc3.jpeg', rating: 4.9, reviews: 312, description: 'Server gaming dengan cooling system advanced', badge: 'new' },
    { id: 13, name: 'Office PC', category: 'pc', price: 8999000, image: 'img/Pc4.jpeg', rating: 4.5, reviews: 145, description: 'PC office dengan harga terjangkau' },
    { id: 14, name: 'Creator Workstation', category: 'pc', price: 22999000, image: 'img/Pc5.jpeg', rating: 4.8, reviews: 267, description: 'Workstation untuk video editing dan 3D rendering' },
    { id: 15, name: 'Streaming PC Pro', category: 'pc', price: 19999000, image: 'img/Pc6.jpeg', rating: 4.7, reviews: 234, description: 'PC streaming dengan performa stabil' },
    { id: 16, name: 'High-End Gaming PC', category: 'pc', price: 28999000, image: 'img/Pc7.jpeg', rating: 4.9, reviews: 345, description: 'PC gaming high-end dengan RTX 4090', badge: 'recommended' },
    { id: 17, name: 'Budget Gaming PC', category: 'pc', price: 12999000, image: 'img/Pc8.jpeg', rating: 4.6, reviews: 198, description: 'PC gaming budget dengan performa baik' },
    { id: 18, name: 'Professional Setup', category: 'pc', price: 23999000, image: 'img/Pc9.jpeg', rating: 4.8, reviews: 287, description: 'Setup profesional untuk workstation premium' },

    // GPU/Graphics Card
    { id: 19, name: 'RTX 4090 Beast', category: 'gpu', price: 19999000, image: 'img/Gpu1.jpeg', rating: 4.9, reviews: 289, description: 'GPU paling powerful untuk gaming ekstrem', badge: 'hot' },
    { id: 20, name: 'RTX 4080 Pro', category: 'gpu', price: 14999000, image: 'img/Gpu2.jpeg', rating: 4.8, reviews: 267, description: 'GPU professional untuk workstation' },
    { id: 21, name: 'RTX 4070 Gaming', category: 'gpu', price: 10999000, image: 'img/Gpu3.jpeg', rating: 4.7, reviews: 245, description: 'GPU gaming mid-range dengan harga kompetitif' },
    { id: 22, name: 'RTX 4060 Budget', category: 'gpu', price: 6999000, image: 'img/Gpu4.jpeg', rating: 4.6, reviews: 198, description: 'GPU budget untuk 1080p gaming smooth' },
    { id: 23, name: 'RTX 4070 Ti', category: 'gpu', price: 13999000, image: 'img/GPu5.jpeg', rating: 4.8, reviews: 276, description: 'GPU high-end untuk gaming 1440p' },
    { id: 24, name: 'RTX 4060 Ti', category: 'gpu', price: 8999000, image: 'img/Gpu6.jpeg', rating: 4.7, reviews: 223, description: 'GPU entry-level gaming 1080p', badge: 'new' },

    // Monitor
    { id: 25, name: 'Gaming Monitor 144Hz', category: 'monitor', price: 3999000, image: 'img/Monitor1.jpeg', rating: 4.8, reviews: 234, description: 'Monitor 27" 144Hz untuk gaming kompetitif' },
    { id: 26, name: 'Professional Monitor 4K', category: 'monitor', price: 8999000, image: 'img/Monitor2.jpeg', rating: 4.9, reviews: 289, description: 'Monitor 4K untuk color grading dan editing', badge: 'recommended' },
    { id: 27, name: 'IPS Monitor 60Hz', category: 'monitor', price: 2499000, image: 'img/Monitor3.jpeg', rating: 4.6, reviews: 167, description: 'Monitor IPS 24" untuk office dan desain' },

    // Keyboard
    { id: 28, name: 'Mechanical Gaming Keyboard', category: 'keyboard', price: 1299000, image: 'img/Keyboard1.jpeg', rating: 4.8, reviews: 312, description: 'Keyboard mechanical RGB dengan switch gaming', badge: 'hot' },
    { id: 29, name: 'Wireless Keyboard Pro', category: 'keyboard', price: 899000, image: 'img/Keyboard2.jpeg', rating: 4.7, reviews: 245, description: 'Keyboard wireless dengan baterai tahan lama' },
    { id: 30, name: 'Compact Gaming Keyboard', category: 'keyboard', price: 799000, image: 'img/Keyboard3.jpeg', rating: 4.6, reviews: 189, description: 'Keyboard compact untuk gaming mobile' },

    // Mouse
    { id: 31, name: 'Gaming Mouse Pro', category: 'mouse', price: 799000, image: 'img/Mouse1.jpeg', rating: 4.9, reviews: 345, description: 'Mouse gaming dengan DPI tinggi dan presisi sempurna', badge: 'new' },
    { id: 32, name: 'Wireless Mouse', category: 'mouse', price: 499000, image: 'img/Mouse2.jpeg', rating: 4.7, reviews: 267, description: 'Mouse wireless ergonomis untuk office' },
    { id: 33, name: 'Ultra Precision Gaming', category: 'mouse', price: 899000, image: 'img/Mouse3.jpeg', rating: 4.8, reviews: 298, description: 'Mouse dengan sensor optik presisi tinggi' },

    // Aksesori
    { id: 34, name: 'RGB Gaming Headset', category: 'accessories', price: 1499000, image: 'img/accessories.jpg', rating: 4.8, reviews: 276, description: 'Headset gaming dengan surround sound 7.1' },
    { id: 35, name: 'Casing PC Premium', category: 'accessories', price: 2999000, image: 'img/casing.png', rating: 4.7, reviews: 198, description: 'Casing PC dengan airflow optimal dan desain modern', badge: 'recommended' },
];

// Data variants untuk hero showcase
const variants = [
    { name: 'Gaming', image: 'img/Gambarlaptop1.jpeg' },
    { name: 'Professional', image: 'img/Gambarlaptop2.jpeg' },
    { name: 'Ultimate', image: 'img/Pc1.jpeg' },
    { name: 'Budget', image: 'img/Gpu1.jpeg' },
    { name: 'Premium', image: 'img/Monitor1.jpeg' },
];

// Data Review
const reviews = [
    { name: 'Budi Santoso', role: 'Gamer Profesional', avatar: 'B', rating: 5, text: 'Produk berkualitas tinggi dengan pengiriman cepat. Sangat puas dengan pelayanannya!' },
    { name: 'Siti Nurhaliza', role: 'Content Creator', avatar: 'S', rating: 5, text: 'Harga sangat kompetitif dan produk original. Rekomendasi untuk semua orang!' },
    { name: 'Ahmad Wijaya', role: 'Profesional IT', avatar: 'A', rating: 4, text: 'Pelayanan customer service responsif dan membantu. Puas dengan pembelian saya.' },
    { name: 'Rina Hermawan', role: 'Graphic Designer', avatar: 'R', rating: 5, text: 'Kualitas packaging sangat baik, produk sampai dengan selamat dan sempurna!' },
    { name: 'Doni Pratama', role: 'Tech Enthusiast', avatar: 'D', rating: 5, text: 'Koleksi produk lengkap dan update. Akan membeli lagi di sini!' },
    { name: 'Lisa Maulida', role: 'Streamer', avatar: 'L', rating: 4, text: 'Produk sesuai dengan deskripsi, garansi resmi terjamin. Mantap!' },
];

// Carousel Variables
let currentSlide = 0;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Don't load products - they are already rendered by PHP
    // Only load if elements exist (for index page)
    // if (document.getElementById('productsGrid')) {
    //     loadProducts('all');
    // }
    if (document.getElementById('reviewsGrid')) {
        loadReviews();
    }
    if (document.getElementById('variantsGrid')) {
        loadVariants();
    }
    
    setupEventListeners();
    setupFAQ();
    checkTheme();
});

// Load Variants
function loadVariants() {
    const variantsGrid = document.getElementById('variantsGrid');
    if (!variantsGrid) return;
    
    variantsGrid.innerHTML = '';
    variants.forEach(variant => {
        const variantCard = document.createElement('div');
        variantCard.className = 'variant-card';
        variantCard.innerHTML = `
            <div class="variant-image">
                <img src="${variant.image}" alt="${variant.name}">
            </div>
            <div class="variant-info">
                <h4>${variant.name}</h4>
                <p>Koleksi Terpilih</p>
            </div>
        `;
        variantsGrid.appendChild(variantCard);
    });
}

// Filter products by category (for PHP-rendered products)
function filterProductsByCategory(category) {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    const allProducts = productsGrid.querySelectorAll('.product-card');
    const searchTerm = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';
    
    let visibleCount = 0;
    
    allProducts.forEach(card => {
        const productCategory = card.dataset.category;
        const productName = card.querySelector('.product-name') ? card.querySelector('.product-name').textContent.toLowerCase() : '';
        
        const matchesCategory = category === 'all' || productCategory === category;
        const matchesSearch = !searchTerm || productName.includes(searchTerm);
        
        if (matchesCategory && matchesSearch) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show "no products" message if needed
    const noProductsMsg = productsGrid.querySelector('.no-products-message');
    if (visibleCount === 0) {
        if (!noProductsMsg) {
            const msg = document.createElement('div');
            msg.className = 'no-products-message';
            msg.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 40px;';
            msg.innerHTML = '<p>Produk tidak ditemukan</p>';
            productsGrid.appendChild(msg);
        }
    } else {
        if (noProductsMsg) {
            noProductsMsg.remove();
        }
    }
}

// Load Products (Legacy - kept for compatibility but not used)
function loadProducts(category) {
    const productsGrid = document.getElementById('productsGrid');
    productsGrid.innerHTML = '';

    let filteredProducts = category === 'all' 
        ? products 
        : products.filter(p => p.category === category);

    // Apply search filter
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    if (searchTerm) {
        filteredProducts = filteredProducts.filter(p => 
            p.name.toLowerCase().includes(searchTerm) || 
            p.description.toLowerCase().includes(searchTerm)
        );
    }

    if (filteredProducts.length === 0) {
        productsGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p>Produk tidak ditemukan</p></div>';
        return;
    }

    filteredProducts.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Create Product Card
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    const badgeHTML = product.badge ? `<div class="product-badge ${product.badge}">${product.badge.toUpperCase()}</div>` : '';
    card.innerHTML = `
        ${badgeHTML}
        <img src="${product.image}" alt="${product.name}" class="product-image">
        <div class="product-info">
            <div class="product-category">${capitalizeCategory(product.category)}</div>
            <h3 class="product-name">${product.name}</h3>
            <div class="product-rating">
                <div class="stars">${generateStars(product.rating)}</div>
                <span class="rating-number">(${product.reviews})</span>
            </div>
            <div class="product-price">Rp ${formatPrice(product.price)}</div>
            <button class="add-btn" onclick="openProductModal(${product.id})">Detail Produk</button>
        </div>
    `;
    return card;
}

// Generate Stars
function generateStars(rating) {
    let stars = '';
    for (let i = 0; i < 5; i++) {
        if (i < Math.floor(rating)) {
            stars += '★';
        } else if (i < rating && rating % 1 !== 0) {
            stars += '⭐';
        } else {
            stars += '☆';
        }
    }
    return stars;
}

// Format Price
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID').format(price);
}

// Capitalize Category
function capitalizeCategory(category) {
    const categories = {
        laptop: 'Laptop',
        pc: 'PC Desktop',
        gpu: 'GPU',
        monitor: 'Monitor',
        keyboard: 'Keyboard',
        mouse: 'Mouse',
        accessories: 'Aksesori'
    };
    return categories[category] || category;
}

// Load Reviews
function loadReviews() {
    const reviewsGrid = document.getElementById('reviewsGrid');
    reviewsGrid.innerHTML = '';

    reviews.forEach(review => {
        const reviewCard = document.createElement('div');
        reviewCard.className = 'review-card';
        reviewCard.innerHTML = `
            <div class="reviewer-info">
                <div class="reviewer-avatar">${review.avatar}</div>
                <div class="reviewer-details">
                    <h4>${review.name}</h4>
                    <p>${review.role}</p>
                </div>
            </div>
            <div class="review-stars">${generateStars(review.rating)}</div>
            <p class="review-text">"${review.text}"</p>
        `;
        reviewsGrid.appendChild(reviewCard);
    });
}

// Open Product Modal
function openProductModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    document.getElementById('modalImage').src = product.image;
    document.getElementById('modalTitle').textContent = product.name;
    document.getElementById('modalCategory').textContent = capitalizeCategory(product.category);
    document.getElementById('modalStars').textContent = generateStars(product.rating);
    document.getElementById('modalReviewCount').textContent = `${product.reviews} ulasan`;
    document.getElementById('modalDescription').textContent = product.description;
    document.getElementById('modalPrice').textContent = `Rp ${formatPrice(product.price)}`;
    document.getElementById('productModal').classList.add('active');
    
    // Store current product ID for add to cart
    document.getElementById('productModal').dataset.productId = productId;
}

// Close Product Modal
function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
}

// Add to Cart
function addToCart() {
    alert('Produk ditambahkan ke keranjang! (Demo mode)');
    closeProductModal();
}

// Setup Event Listeners
function setupEventListeners() {
    // Category buttons - filter PHP-rendered products
    const categoryBtns = document.querySelectorAll('.category-btn');
    if (categoryBtns.length > 0) {
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                filterProductsByCategory(e.target.dataset.category);
            });
        });
    }

    // Search input - filter PHP-rendered products
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const activeCategoryBtn = document.querySelector('.category-btn.active');
            const activeCategory = activeCategoryBtn ? activeCategoryBtn.dataset.category : 'all';
            filterProductsByCategory(activeCategory);
        });
    }

    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Hamburger menu
    const hamburger = document.getElementById('hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
    }

    // Close modal when clicking outside
    const productModal = document.getElementById('productModal');
    if (productModal) {
        productModal.addEventListener('click', (e) => {
            if (e.target.id === 'productModal') {
                closeProductModal();
            }
        });
    }

    // Navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    if (navLinks.length > 0) {
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });
    }
}

// Setup FAQ
function setupFAQ() {
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            faqItem.classList.toggle('active');

            // Close other items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
        });
    });
}

// Toggle Theme
function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark-mode');
    
    // Save preference
    localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    
    // Update icon
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        if (icon) {
            if (body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    }
}

// Check Theme Preference
function checkTheme() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (prefersDark && !savedTheme)) {
        document.body.classList.add('dark-mode');
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
    }
}

// Toggle Mobile Menu
function toggleMobileMenu() {
    const navMenu = document.querySelector('.nav-menu');
    navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
}

// Carousel Functions
function nextSlide() {
    const carouselImages = [
        'img/Gambarlaptop1.jpeg',
        'img/Gambarlaptop2.jpeg',
        'img/Pc1.jpeg',
        'img/Gpu1.jpeg',
        'img/Monitor1.jpeg'
    ];
    
    currentSlide = (currentSlide + 1) % carouselImages.length;
    updateCarousel(carouselImages);
}

function prevSlide() {
    const carouselImages = [
        'img/Gambarlaptop1.jpeg',
        'img/Gambarlaptop2.jpeg',
        'img/Pc1.jpeg',
        'img/Gpu1.jpeg',
        'img/Monitor1.jpeg'
    ];
    
    currentSlide = (currentSlide - 1 + carouselImages.length) % carouselImages.length;
    updateCarousel(carouselImages);
}

function updateCarousel(carouselImages) {
    const mainImage = document.getElementById('carouselMainImage');
    const slideNumber = document.getElementById('slideNumber');
    
    if (mainImage) {
        mainImage.style.opacity = '0';
        setTimeout(() => {
            mainImage.src = carouselImages[currentSlide];
            mainImage.style.opacity = '1';
        }, 300);
    }
    
    if (slideNumber) {
        slideNumber.textContent = currentSlide + 1;
    }
}

// Auto carousel (5 detik)
setInterval(() => {
    nextSlide();
}, 5000);
