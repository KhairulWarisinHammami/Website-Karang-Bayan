<?php
// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debug: lihat data yang dikirim
    error_log("Form submitted: " . print_r($_POST, true));
    
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $review_text = isset($_POST['review']) ? sanitize($_POST['review']) : '';
    
    // Validasi
    if (empty($name)) {
        $error = "Nama harus diisi!";
    } elseif (empty($review_text)) {
        $error = "Review harus diisi!";
    } elseif (strlen($review_text) < 10) {
        $error = "Review minimal 10 karakter!";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Rating harus antara 1-5!";
    } else {
        try {
            $conn = connectDB();
            
            // Debug: cek koneksi
            error_log("Database connected");
            
            $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, review, status) VALUES (?, ?, ?, ?, 'pending')");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ssis", $name, $email, $rating, $review_text);
            
            if ($stmt->execute()) {
                $success = "Review berhasil dikirim! Terima kasih atas masukan Anda.";
                // Reset form
                $_POST = array();
                error_log("Review inserted successfully");
            } else {
                $error = "Gagal mengirim review: " . $stmt->error;
                error_log("Insert error: " . $stmt->error);
            }
            
            $stmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
            error_log("Exception: " . $e->getMessage());
        }
    }
}

// Ambil beberapa review yang sudah disetujui untuk ditampilkan
$approved_reviews = array();
try {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $result = $stmt->get_result();
    $approved_reviews = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error fetching reviews: " . $e->getMessage());
}

include 'includes/header.php';
?>

<style>
/* Review Page Styles dengan Warna Hijau (#2c5530) */
.review-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Hero Section - Warna Hijau */
.review-hero {
    background: linear-gradient(rgba(44, 85, 48, 0.9), rgba(44, 85, 48, 0.95));
    color: white;
    padding: 60px 0 40px;
    text-align: center;
    position: relative;
}

.review-hero-title {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: white;
}

.review-hero-subtitle {
    font-size: 1.4rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ecf0f1;
}

.review-hero-divider {
    width: 80px;
    height: 3px;
    background-color: #4a8c4f;
    margin: 0 auto 25px;
}

.review-hero-description {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    color: #d4edda;
    line-height: 1.6;
}

/* Container */
.review-container {
    padding: 40px 0;
}

/* Alerts - Warna Hijau */
.review-alert {
    display: flex;
    align-items: flex-start;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: 1px solid transparent;
    animation: slideDown 0.3s ease;
}

.review-alert.success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
    border-left: 4px solid #2c5530;
}

.review-alert.error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-icon {
    font-size: 1.5rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.review-alert.success .alert-icon {
    color: #2c5530;
}

.review-alert.error .alert-icon {
    color: #dc3545;
}

.alert-content h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Content Wrapper */
.review-content-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

@media (max-width: 992px) {
    .review-content-wrapper {
        grid-template-columns: 1fr;
    }
}

/* Section Header */
.section-header {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.section-title {
    font-size: 1.6rem;
    color: #2c5530;
    margin-bottom: 8px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #4a8c4f;
}

.section-title .badge {
    background: #4a8c4f;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-left: 8px;
}

.section-subtitle {
    color: #6c757d;
    line-height: 1.5;
    margin: 0;
    font-size: 0.95rem;
}

/* Review Form */
.review-form-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #2c5530;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-label i {
    color: #4a8c4f;
    margin-right: 8px;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    border-color: #4a8c4f;
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
}

.form-hint {
    display: block;
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 5px;
}

.form-hint i {
    margin-right: 5px;
    color: #4a8c4f;
}

/* Star Rating */
.rating-group {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.star-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 2rem;
    color: #dee2e6;
    cursor: pointer;
    transition: all 0.2s ease;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #ffc107;
}

.star-rating input:checked + label {
    color: #ffc107;
}

.rating-text {
    margin-left: 15px;
    font-size: 1rem;
    color: #2c5530;
    font-weight: 600;
}

.rating-text span {
    color: #4a8c4f;
    font-weight: 700;
}

/* Textarea */
.review-textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
    line-height: 1.5;
}

.textarea-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.char-counter {
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.3s ease;
}

.char-counter.warning {
    color: #dc3545;
    background: #f8d7da;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background-color: #2c5530;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background-color: #3a6b3e;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(44, 85, 48, 0.2);
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
}

/* Reviews List */
.reviews-list-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.reviews-grid {
    display: grid;
    gap: 20px;
}

/* Review Card */
.review-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    background: #fff;
    transition: all 0.3s ease;
}

.review-card:hover {
    border-color: #4a8c4f;
    box-shadow: 0 6px 15px rgba(44, 85, 48, 0.1);
    transform: translateY(-3px);
}

.review-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 15px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reviewer-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #2c5530, #4a8c4f);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: 600;
}

.reviewer-name {
    margin: 0 0 5px 0;
    color: #2c5530;
    font-size: 1.05rem;
    font-weight: 600;
}

.review-date {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 5px;
}

.review-date i {
    color: #4a8c4f;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 3px;
}

.review-rating i {
    font-size: 1.1rem;
    color: #dee2e6;
}

.review-rating i.active {
    color: #ffc107;
}

.rating-number {
    margin-left: 8px;
    font-weight: 600;
    color: #2c5530;
    font-size: 0.9rem;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
}

.review-card-body {
    margin-bottom: 15px;
}

.review-text {
    color: #495057;
    line-height: 1.6;
    margin: 0;
    font-size: 0.95rem;
}

.review-card-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.review-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85rem;
    padding: 4px 12px;
    border-radius: 15px;
    font-weight: 600;
}

.review-status.approved {
    background: #d4edda;
    color: #155724;
}

.review-status i {
    font-size: 0.8rem;
}

/* No Reviews */
.no-reviews {
    text-align: center;
    padding: 50px 20px;
    color: #6c757d;
}

.no-reviews-icon {
    font-size: 3.5rem;
    color: #dee2e6;
    margin-bottom: 15px;
}

.no-reviews h4 {
    margin: 0 0 10px 0;
    color: #2c5530;
    font-weight: 600;
}

.no-reviews p {
    margin: 0;
    color: #6c757d;
}

/* Animasi */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.review-card {
    animation: fadeIn 0.5s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .review-hero-title {
        font-size: 2.2rem;
    }
    
    .review-hero-subtitle {
        font-size: 1.2rem;
    }
    
    .review-hero-description {
        font-size: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .review-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .reviewer-info {
        width: 100%;
    }
    
    .review-card-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .review-hero {
        padding: 40px 0 30px;
    }
    
    .review-container {
        padding: 20px 0;
    }
    
    .review-form-section,
    .reviews-list-section {
        padding: 20px;
    }
    
    .section-title {
        font-size: 1.4rem;
    }
    
    .star-rating label {
        font-size: 1.8rem;
    }
}

/* Additional styling for form sections */
.form-section-title {
    color: #2c5530;
    font-size: 1.1rem;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

.form-hint-box {
    background: #f8f9fa;
    border-left: 3px solid #4a8c4f;
    padding: 10px 15px;
    margin: 10px 0;
    border-radius: 0 4px 4px 0;
    font-size: 0.9rem;
    color: #6c757d;
}
</style>

<div class="review-page">
    <!-- Hero Section -->
    <div class="review-hero">
        <div class="container">
            <h1 class="review-hero-title">Desa Karang Bayan</h1>
            <h2 class="review-hero-subtitle">Desa Wisata Budaya & Alam</h2>
            <div class="review-hero-divider"></div>
            <p class="review-hero-description">
                Bagikan pengalaman Anda dan bantu pengunjung lain mengetahui keindahan Desa Karang Bayan
            </p>
        </div>
    </div>

    <div class="container review-container">
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="review-alert success">
                <div class="alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Review Berhasil Dikirim!</h4>
                    <p><?= htmlspecialchars($success) ?></p>
                    <small>Review Anda sedang dalam proses moderasi dan akan segera ditampilkan.</small>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="review-alert error">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Oops! Terjadi Kesalahan</h4>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="review-content-wrapper">
            <!-- Form Section -->
            <div class="review-form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-pen-alt"></i> Berikan Review Anda
                    </h3>
                    <p class="section-subtitle">
                        Kami sangat menghargai pendapat Anda tentang Desa Karang Bayan. 
                        Review Anda akan ditinjau oleh admin sebelum ditampilkan di website.
                    </p>
                </div>

                <form method="POST" action="" id="reviewForm" class="review-form">
                    <!-- Nama Lengkap Section -->
                    <div class="form-section-title">Nama Lengkap *</div>
                    <div class="form-group">
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                               placeholder="Masukkan nama lengkap Anda" required>
                        <div class="form-hint-box">
                            <i class="fas fa-info-circle"></i> Nama akan ditampilkan bersama review Anda
                        </div>
                    </div>

                    <!-- Email Section -->
                    <div class="form-section-title">Email</div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                               placeholder="email@contoh.com">
                        <div class="form-hint-box">
                            <i class="fas fa-lock"></i> Email tidak akan ditampilkan publik
                        </div>
                    </div>

                    <!-- Rating Section -->
                    <div class="form-section-title">Rating *</div>
                    <div class="form-group rating-group">
                        <div class="star-rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                       <?= (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'checked' : ($i == 5 ? 'checked' : '') ?>>
                                <label for="star<?= $i ?>" title="<?= $i ?> bintang">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                            <div class="rating-text">
                                <span id="ratingValue">5</span> / 5 bintang
                            </div>
                        </div>
                        <div class="form-hint-box">
                            <i class="fas fa-star"></i> Pilih rating 1-5 bintang
                        </div>
                    </div>

                    <!-- Review Section -->
                    <div class="form-section-title">Review Anda *</div>
                    <div class="form-group">
                        <textarea id="review" name="review" class="form-control review-textarea" 
                                  placeholder="Ceritakan pengalaman Anda di Desa Karang Bayan..." 
                                  rows="5" required><?= isset($_POST['review']) ? htmlspecialchars($_POST['review']) : '' ?></textarea>
                        <div class="textarea-footer">
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i> Minimal 10 karakter
                            </div>
                            <div class="char-counter" id="charCounter">
                                <span id="charCount">0</span>/10
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Review
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>

            <!-- Reviews List Section -->
            <div class="reviews-list-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-comments"></i> Review Pengunjung Lainnya
                        <span class="badge"><?= count($approved_reviews) ?></span>
                    </h3>
                    <p class="section-subtitle">
                        Pengalaman nyata dari pengunjung Desa Karang Bayan
                    </p>
                </div>

                <?php if (!empty($approved_reviews)): ?>
                    <div class="reviews-grid">
                        <?php foreach ($approved_reviews as $rev): 
                            $date = date('d F Y', strtotime($rev['created_at']));
                            $time = date('H:i', strtotime($rev['created_at']));
                        ?>
                            <div class="review-card">
                                <div class="review-card-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?= strtoupper(substr($rev['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h4 class="reviewer-name"><?= htmlspecialchars($rev['name']) ?></h4>
                                            <div class="review-date">
                                                <i class="far fa-calendar"></i> <?= $date ?>
                                                <span style="margin: 0 5px;">•</span>
                                                <i class="far fa-clock"></i> <?= $time ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $rev['rating'] ? 'active' : '' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="rating-number"><?= $rev['rating'] ?>.0</span>
                                    </div>
                                </div>
                                <div class="review-card-body">
                                    <p class="review-text"><?= nl2br(htmlspecialchars($rev['review'])) ?></p>
                                </div>
                                <div class="review-card-footer">
                                    <span class="review-status approved">
                                        <i class="fas fa-check-circle"></i> Disetujui
                                    </span>
                                    <span class="review-date">
                                        <?= $date ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-reviews">
                        <div class="no-reviews-icon">
                            <i class="far fa-comment-dots"></i>
                        </div>
                        <h4>Belum ada review</h4>
                        <p>Jadilah yang pertama memberikan review tentang Desa Karang Bayan!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript untuk interaktivitas
document.addEventListener('DOMContentLoaded', function() {
    // Update rating text
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingValue = document.getElementById('ratingValue');
    
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            ratingValue.textContent = this.value;
        });
    });
    
    // Character counter
    const reviewTextarea = document.getElementById('review');
    const charCount = document.getElementById('charCount');
    const charCounter = document.getElementById('charCounter');
    
    reviewTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length < 10) {
            charCounter.classList.add('warning');
        } else {
            charCounter.classList.remove('warning');
        }
    });
    
    // Trigger input event untuk inisialisasi
    reviewTextarea.dispatchEvent(new Event('input'));
    
    // Form validation
    const reviewForm = document.getElementById('reviewForm');
    reviewForm.addEventListener('submit', function(e) {
        const reviewText = reviewTextarea.value.trim();
        const rating = document.querySelector('input[name="rating"]:checked');
        
        if (reviewText.length < 10) {
            e.preventDefault();
            // Create custom alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'review-alert error';
            alertDiv.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Review Terlalu Pendek!</h4>
                    <p>Review harus minimal 10 karakter.</p>
                </div>
            `;
            
            // Insert alert before form
            const formSection = document.querySelector('.review-form-section');
            formSection.insertBefore(alertDiv, formSection.firstChild);
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus on textarea
            reviewTextarea.focus();
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
            
            return false;
        }
        
        if (!rating) {
            e.preventDefault();
            // Create custom alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'review-alert error';
            alertDiv.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Rating Belum Dipilih!</h4>
                    <p>Harap pilih rating untuk review Anda.</p>
                </div>
            `;
            
            // Insert alert before form
            const formSection = document.querySelector('.review-form-section');
            formSection.insertBefore(alertDiv, formSection.firstChild);
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
            
            return false;
        }
        
        return true;
    });
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>