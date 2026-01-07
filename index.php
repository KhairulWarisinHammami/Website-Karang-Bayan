<?php
$pageTitle = "Beranda";
include 'includes/header.php';

// Ambil konten terbaru dari database
require_once 'includes/config.php';
require_once 'includes/functions.php';

$conn = connectDB();
$recent_contents = [];
$featured_contents = [];

// PERBAIKAN: Gunakan kolom 'content' bukan 'description' atau 'excerpt'
// Query untuk 6 konten terbaru
$stmt = $conn->prepare("SELECT id, title, content, category, image, created_at FROM contents ORDER BY created_at DESC LIMIT 6");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recent_contents[] = $row;
    }
    $stmt->close();
} else {
    error_log("Error preparing recent contents query: " . $conn->error);
}

// Query untuk 3 konten featured (kategori tertentu)
$stmt = $conn->prepare("SELECT id, title, content, category, image, created_at FROM contents WHERE category IN ('wisata', 'budaya', 'kuliner') ORDER BY created_at DESC LIMIT 3");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $featured_contents[] = $row;
    }
    $stmt->close();
} else {
    error_log("Error preparing featured contents query: " . $conn->error);
}

// Ambil review terbaru
$recent_reviews = [];
$stmt = $conn->prepare("SELECT name, rating, review, created_at FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 4");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recent_reviews[] = $row;
    }
    $stmt->close();
} else {
    error_log("Error preparing reviews query: " . $conn->error);
}

$conn->close();
?>

<!-- Hero Section -->
<!-- Hero Section -->
<section class="hero-section">
    <!-- Video Background -->
    <div class="hero-video-container">
        <video autoplay muted loop playsinline class="hero-video" preload="auto" poster="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80">
            <source src="assets/uploads/videos/video-banner.mp4" type="video/mp4">
            <!-- Fallback text -->
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay"></div>
    </div>
    
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Selamat Datang di Desa Karang Bayan</h1>
            <p class="hero-subtitle">Temukan keindahan alam, kekayaan budaya, dan keramahan masyarakat desa yang akan membuat pengalaman Anda tak terlupakan.</p>
            <div class="hero-buttons">
                <a href="#jelajahi" class="btn btn-explore">Jelajahi Sekarang</a>
                <a href="review.php" class="btn btn-review">Berikan Review</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Content Section -->
<section class="featured-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Destinasi Unggulan</h2>
            <p class="section-subtitle">Temukan tempat-tempat terbaik yang wajib Anda kunjungi di Desa Karang Bayan</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($featured_contents)): ?>
                <?php foreach ($featured_contents as $content): ?>
                    <div class="col-md-4">
                        <div class="featured-card">
                            <div class="featured-img">
                                <?php if (!empty($content['image'])): ?>
                                    <img src="assets/uploads/images/<?= htmlspecialchars($content['image']) ?>" alt="<?= htmlspecialchars($content['title']) ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Default Image">
                                <?php endif; ?>
                                <div class="category-badge">
                                    <?= htmlspecialchars($content['category']) ?>
                                </div>
                            </div>
                            <div class="featured-body">
                                <h3 class="featured-title"><?= htmlspecialchars($content['title']) ?></h3>
                                <p class="featured-text">
                                    <?= htmlspecialchars(substr(strip_tags($content['content'] ?? ''), 0, 100)) ?>...
                                </p>
                                <a href="konten.php?action=view&id=<?= $content['id'] ?>" class="btn-featured">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada konten featured tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Jelajahi Konten Section -->
<section id="jelajahi" class="explore-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Jelajahi Konten</h2>
            <p class="section-subtitle">Temukan berbagai informasi menarik tentang Desa Karang Bayan</p>
        </div>
        
        <div class="explore-categories mb-5">
            <div class="row g-3 justify-content-center">
                <div class="col-md-3 col-sm-6">
                    <a href="konten.php?category=wisata" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-mountain"></i>
                        </div>
                        <h4>Wisata Alam</h4>
                        <p>Keindahan alam yang memukau</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="konten.php?category=budaya" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-theater-masks"></i>
                        </div>
                        <h4>Budaya</h4>
                        <p>Kearifan lokal dan tradisi</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="konten.php?category=kuliner" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Kuliner</h4>
                        <p>Makanan khas desa</p>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="explore-content">
            <h3 class="text-center mb-4">Konten Terbaru</h3>
            <div class="row g-4">
                <?php if (!empty($recent_contents)): ?>
                    <?php foreach ($recent_contents as $content): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="content-card">
                                <div class="content-img">
                                    <?php if (!empty($content['image'])): ?>
                                        <img src="assets/uploads/images/<?= htmlspecialchars($content['image']) ?>" alt="<?= htmlspecialchars($content['title']) ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Default Image">
                                    <?php endif; ?>
                                </div>
                                <div class="content-body">
                                    <span class="content-category"><?= htmlspecialchars($content['category']) ?></span>
                                    <h5 class="content-title"><?= htmlspecialchars($content['title']) ?></h5>
                                    <p class="content-text">
                                        <?= htmlspecialchars(substr(strip_tags($content['content'] ?? ''), 0, 150)) ?>...
                                    </p>
                                    <div class="content-footer">
                                        <span class="content-date">
                                            <i class="far fa-calendar"></i> <?= date('d M Y', strtotime($content['created_at'])) ?>
                                        </span>
                                        <a href="konten.php?action=view&id=<?= $content['id'] ?>" class="btn-read">Baca</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada konten tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="konten.php" class="btn btn-view-all">
                    <i class="fas fa-list me-2"></i> Lihat Semua Konten
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimoni Section -->
<section class="testimonial-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Review</h2>
            <p class="section-subtitle">Review dari pengunjung Desa Karang Bayan</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($recent_reviews)): ?>
                <?php foreach ($recent_reviews as $review): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'star-active' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="testimonial-text">"<?= htmlspecialchars(substr($review['review'], 0, 100)) ?>..."</p>
                            <div class="testimonial-author">
                                <h5><?= htmlspecialchars($review['name']) ?></h5>
                                <span><?= date('d M Y', strtotime($review['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada review tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="review.php" class="btn btn-testimonial">
                <i class="fas fa-pen me-2"></i> Berikan Review Anda
            </a>
        </div>
    </div>
</section>



<?php include 'includes/footer.php'; ?>