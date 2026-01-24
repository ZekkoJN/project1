<?php
$page_title = "Kontak Kami";
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    // In real application, send email or save to database
    $success_message = "Terima kasih! Pesan Anda telah diterima. Kami akan segera menghubungi Anda.";
}
?>

<style>
    /* Contact Form Styles */
    .contact-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
    }

    .contact-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .contact-hero p {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .contact-info h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        color: var(--primary-color);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .info-content h3 {
        font-size: 1.2rem;
        margin-bottom: 8px;
        color: #333;
    }

    .info-content p {
        color: #666;
        margin: 0;
    }

    .contact-form {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .contact-form h2 {
        font-size: 2rem;
        margin-bottom: 30px;
        color: var(--primary-color);
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

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 150px;
    }

    .submit-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: var(--secondary-color);
    }

    .success-message {
        background: #4caf50;
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .contact-info h2,
    .contact-form h2,
    .info-item,
    .contact-form,
    .info-content h3,
    .form-group label,
    .info-content p,
    .form-group input,
    .form-group textarea {
        transition: all 0.3s ease;
    }

    body.dark-mode .contact-info h2,
    body.dark-mode .contact-form h2 {
        color: var(--primary-color);
    }

    body.dark-mode .info-item,
    body.dark-mode .contact-form {
        background: #2d2d2d;
    }

    body.dark-mode .info-content h3,
    body.dark-mode .form-group label {
        color: #fff;
    }

    body.dark-mode .info-content p {
        color: #ccc;
    }

    body.dark-mode .form-group input,
    body.dark-mode .form-group textarea {
        background: #1a1a1a;
        color: #fff;
        border-color: #444;
    }

    @media (max-width: 768px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .contact-hero h1 {
            font-size: 2rem;
        }

        .contact-form {
            padding: 30px 20px;
        }
    }
</style>

<section class="contact-hero">
    <div class="container">
        <h1>Hubungi Kami</h1>
        <p>Ada pertanyaan? Kami siap membantu Anda!</p>
        <p>Tim customer service kami siap melayani 24/7</p>
    </div>
</section>

<div class="contact-container">
    <div class="contact-info">
        <h2>Informasi Kontak</h2>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
                <h3>Alamat</h3>
                <p>Jl. Teknologi No. 123<br>Jakarta Selatan, DKI Jakarta<br>Indonesia 12345</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
                <h3>Telepon</h3>
                <p>+62 812-3456-7890<br>+62 21-1234-5678</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <h3>Email</h3>
                <p>info@techhub.com<br>support@techhub.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
                <h3>Jam Operasional</h3>
                <p>Senin - Jumat: 09:00 - 18:00<br>Sabtu: 09:00 - 15:00<br>Minggu: Tutup</p>
            </div>
        </div>
    </div>

    <div class="contact-form">
        <h2>Kirim Pesan</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Nama Lengkap *</label>
                <input type="text" id="name" name="name" required placeholder="Masukkan nama Anda">
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required placeholder="nama@email.com">
            </div>

            <div class="form-group">
                <label for="subject">Subjek *</label>
                <input type="text" id="subject" name="subject" required placeholder="Subjek pesan Anda">
            </div>

            <div class="form-group">
                <label for="message">Pesan *</label>
                <textarea id="message" name="message" required placeholder="Tulis pesan Anda di sini..."></textarea>
            </div>

            <button type="submit" name="submit_contact" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Kirim Pesan
            </button>
        </form>
    </div>
</div>

<section class="key-features" style="background: #f8f9fa;">
    <div class="container">
        <h2 class="section-title">Mengapa Menghubungi Kami?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Customer Service 24/7</h3>
                <p>Tim support kami siap membantu Anda kapan saja tanpa henti</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Respon Cepat</h3>
                <p>Kami merespon setiap pertanyaan dalam waktu maksimal 2 jam</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Tim Profesional</h3>
                <p>Dilayani oleh tim yang berpengalaman dan ramah</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <h3>Solusi Terbaik</h3>
                <p>Kami memberikan solusi terbaik untuk setiap masalah Anda</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
