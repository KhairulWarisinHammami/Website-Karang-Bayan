<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Konten Desa";

// Ambil parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$content_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Koneksi database
$conn = connectDB();

// Tentukan konten yang akan ditampilkan berdasarkan action
$contents = [];
$single_content = null;
$content_categories = [];

if ($action == 'view' && $content_id > 0) {
    // Ambil konten tunggal berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM contents WHERE id = ?");
    $stmt->bind_param("i", $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $single_content = $result->fetch_assoc();
    $stmt->close();
    
    if ($single_content) {
        $pageTitle = $single_content['title'];
    }
} else {
    // Ambil konten berdasarkan filter
    if (!empty($category)) {
        $stmt = $conn->prepare("SELECT * FROM contents WHERE category = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $category);
        $pageTitle = "Konten " . ucfirst($category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM contents ORDER BY created_at DESC");
    }
    
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }
        $stmt->close();
    }
}

// Ambil kategori unik untuk filter
$stmt = $conn->prepare("SELECT DISTINCT category FROM contents ORDER BY category");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $content_categories[] = $row['category'];
}
$stmt->close();

$conn->close();

include 'includes/header.php';
?>

<style>
/* Content Page Styles */
.content-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Hero Section */
.content-hero {
    background: linear-gradient(rgba(44, 85, 48, 0.9), rgba(44, 85, 48, 0.95));
    color: white;
    padding: 80px 0 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.content-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    opacity: 0.1;
    z-index: 1;
}

.content-hero .container {
    position: relative;
    z-index: 2;
}

.content-hero-title {
    font-size: 3.2rem;
    font-weight: 800;
    margin-bottom: 15px;
    color: white;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
}

.content-hero-subtitle {
    font-size: 1.5rem;
    font-weight: 300;
    margin-bottom: 25px;
    color: #ecf0f1;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.content-hero-divider {
    width: 100px;
    height: 4px;
    background-color: #ffd166;
    margin: 0 auto 30px;
    border-radius: 2px;
}

/* Breadcrumb */
.breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 12px 25px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}

.breadcrumb-item {
    color: #d4edda;
    font-size: 0.95rem;
}

.breadcrumb-item.active {
    color: #ffd166;
    font-weight: 600;
}

.breadcrumb-separator {
    color: #4a8c4f;
}

/* Content Container */
.content-container {
    padding: 40px 0 60px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Content Filter */
.content-filter {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    padding: 25px 30px;
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filter-header h3 {
    color: #2c5530;
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
}

.filter-reset {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s;
}

.filter-reset:hover {
    background: #e9ecef;
    color: #495057;
}

.filter-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-btn {
    padding: 10px 20px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    color: #495057;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn:hover {
    background: #e9f5ea;
    border-color: #4a8c4f;
    color: #2c5530;
}

.filter-btn.active {
    background: #2c5530;
    border-color: #2c5530;
    color: white;
}

.filter-btn i {
    font-size: 0.9rem;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.content-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.content-empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.content-empty h4 {
    color: #6c757d;
    margin-bottom: 10px;
}

.content-empty p {
    color: #adb5bd;
    margin: 0;
}

/* Content Card */
.content-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.content-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    border-color: #2c5530;
}

.content-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.content-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.content-item:hover .content-image img {
    transform: scale(1.05);
}

.content-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(44, 85, 48, 0.95);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    backdrop-filter: blur(5px);
}

.content-body {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.content-title {
    color: #2c5530;
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.content-excerpt {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.content-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.content-date {
    color: #adb5bd;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.content-read {
    background: #2c5530;
    color: white;
    text-decoration: none;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.content-read:hover {
    background: #3a6b3e;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(44, 85, 48, 0.2);
}

/* Single Content View */
.single-content {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.single-header {
    padding: 40px 40px 20px;
    border-bottom: 1px solid #e9ecef;
}

.single-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.single-category {
    background: #e7f3e9;
    color: #2c5530;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.single-date {
    color: #6c757d;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.single-title {
    color: #2c5530;
    font-size: 2.2rem;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 10px;
}

.single-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    font-weight: 300;
}

.single-featured-image {
    height: 400px;
    overflow: hidden;
}

.single-featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.single-body {
    padding: 40px;
    line-height: 1.8;
    color: #495057;
    font-size: 1.05rem;
}

.single-body h2, 
.single-body h3, 
.single-body h4 {
    color: #2c5530;
    margin: 30px 0 15px;
}

.single-body p {
    margin-bottom: 20px;
}

.single-body ul, 
.single-body ol {
    margin: 20px 0;
    padding-left: 20px;
}

.single-body li {
    margin-bottom: 10px;
}

.single-body img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
}

.single-footer {
    padding: 20px 40px 40px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.back-btn {
    background: #6c757d;
    color: white;
    text-decoration: none;
    padding: 10px 25px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-btn:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.share-buttons {
    display: flex;
    gap: 10px;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.share-facebook { background: #3b5998; }
.share-twitter { background: #1da1f2; }
.share-whatsapp { background: #25d366; }

/* Pagination */
.content-pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 40px;
}

.pagination-btn {
    padding: 10px 20px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
}

.pagination-btn:hover {
    background: #e9f5ea;
    border-color: #4a8c4f;
    color: #2c5530;
}

.pagination-btn.active {
    background: #2c5530;
    border-color: #2c5530;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .content-hero-title {
        font-size: 2.4rem;
    }
    
    .content-hero-subtitle {
        font-size: 1.2rem;
    }
    
    .filter-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .single-header,
    .single-body,
    .single-footer {
        padding: 25px;
    }
    
    .single-featured-image {
        height: 250px;
    }
    
    .single-title {
        font-size: 1.8rem;
    }
    
    .single-footer {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .back-btn {
        text-align: center;
        justify-content: center;
    }
    
    .share-buttons {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .content-hero {
        padding: 60px 0 40px;
    }
    
    .content-hero-title {
        font-size: 2rem;
    }
    
    .filter-categories {
        justify-content: center;
    }
    
    .single-title {
        font-size: 1.6rem;
    }
    
    .single-body {
        font-size: 1rem;
    }
}
</style>

<div class="content-page">
    <!-- Hero Section -->
    <div class="content-hero">
        <div class="container">
            <h1 class="content-hero-title">
                <?php 
                if ($action == 'view' && $single_content) {
                    echo htmlspecialchars($single_content['title']);
                } else if (!empty($category)) {
                    echo "Konten " . htmlspecialchars(ucfirst($category));
                } else {
                    echo "Konten Desa";
                }
                ?>
            </h1>
            <p class="content-hero-subtitle">
                <?php 
                if ($action == 'view' && $single_content) {
                    echo "Baca artikel lengkap tentang Desa Karang Bayan";
                } else {
                    echo "Temukan berbagai informasi menarik tentang Desa Karang Bayan";
                }
                ?>
            </p>
            <div class="content-hero-divider"></div>
            
            <?php if ($action != 'view'): ?>
            <div class="breadcrumb">
                <a href="index.php" class="breadcrumb-item">Beranda</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item active">Konten</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container content-container">
        <?php if ($action == 'view'): ?>
            <!-- Single Content View -->
            <?php if ($single_content): ?>
                <div class="single-content">
                    <div class="single-header">
                        <div class="single-meta">
                            <span class="single-category">
                                <?= htmlspecialchars(ucfirst($single_content['category'])) ?>
                            </span>
                            <span class="single-date">
                                <i class="far fa-calendar"></i>
                                <?= date('d F Y', strtotime($single_content['created_at'])) ?>
                            </span>
                        </div>
                        <h1 class="single-title"><?= htmlspecialchars($single_content['title']) ?></h1>
                        <?php if (!empty($single_content['content']) && strlen(strip_tags($single_content['content'])) > 200): ?>
                            <p class="single-subtitle"><?= htmlspecialchars(substr(strip_tags($single_content['content']), 0, 200)) ?>...</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($single_content['image'])): ?>
                        <div class="single-featured-image">
                            <img src="assets/uploads/images/<?= htmlspecialchars($single_content['image']) ?>" 
                                 alt="<?= htmlspecialchars($single_content['title']) ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="single-body">
                        <?= nl2br(htmlspecialchars($single_content['content'])) ?>
                    </div>
                    
                    <div class="single-footer">
                        <a href="konten.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Konten
                        </a>
                        
                        <div class="share-buttons">
                            <a href="#" class="share-btn share-facebook" title="Bagikan ke Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="share-btn share-twitter" title="Bagikan ke Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="share-btn share-whatsapp" title="Bagikan ke WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="content-empty">
                    <div class="content-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Konten Tidak Ditemukan</h4>
                    <p>Konten yang Anda cari tidak tersedia atau telah dihapus.</p>
                    <div style="margin-top: 20px;">
                        <a href="konten.php" class="btn btn-primary">Kembali ke Daftar Konten</a>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Content List View -->
            <!-- Filter Section -->
            <div class="content-filter">
                <div class="filter-header">
                    <h3>Filter Kategori</h3>
                    <?php if (!empty($category)): ?>
                        <a href="konten.php" class="filter-reset">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="filter-categories">
                    <a href="konten.php" class="filter-btn <?= empty($category) ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Semua Konten
                    </a>
                    
                    <?php foreach ($content_categories as $cat): ?>
                        <a href="konten.php?category=<?= urlencode($cat) ?>" 
                           class="filter-btn <?= $category == $cat ? 'active' : '' ?>">
                            <i class="fas fa-folder"></i> <?= htmlspecialchars(ucfirst($cat)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Content Grid -->
            <?php if (!empty($contents)): ?>
                <div class="content-grid">
                    <?php foreach ($contents as $content): ?>
                        <div class="content-item">
                            <div class="content-image">
                                <?php if (!empty($content['image'])): ?>
                                    <img src="assets/uploads/images/<?= htmlspecialchars($content['image']) ?>" 
                                         alt="<?= htmlspecialchars($content['title']) ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                                         alt="Default Image">
                                <?php endif; ?>
                                <span class="content-badge"><?= htmlspecialchars(ucfirst($content['category'])) ?></span>
                            </div>
                            
                            <div class="content-body">
                                <h3 class="content-title"><?= htmlspecialchars($content['title']) ?></h3>
                                <p class="content-excerpt">
                                    <?= htmlspecialchars(substr(strip_tags($content['content'] ?? ''), 0, 150)) ?>...
                                </p>
                                
                                <div class="content-footer">
                                    <span class="content-date">
                                        <i class="far fa-calendar"></i>
                                        <?= date('d M Y', strtotime($content['created_at'])) ?>
                                    </span>
                                    <a href="konten.php?action=view&id=<?= $content['id'] ?>" class="content-read">
                                        Baca <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <div class="content-pagination">
                    <a href="#" class="pagination-btn active">1</a>
                    <a href="#" class="pagination-btn">2</a>
                    <a href="#" class="pagination-btn">3</a>
                    <a href="#" class="pagination-btn">Selanjutnya <i class="fas fa-arrow-right"></i></a>
                </div>
                
            <?php else: ?>
                <div class="content-empty">
                    <div class="content-empty-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h4><?= empty($category) ? 'Belum Ada Konten' : 'Tidak Ada Konten dalam Kategori Ini' ?></h4>
                    <p>Konten akan segera ditambahkan. Silakan kunjungi kembali halaman ini nanti.</p>
                    <?php if (!empty($category)): ?>
                        <div style="margin-top: 20px;">
                            <a href="konten.php" class="btn btn-primary">Lihat Semua Konten</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
    
    // Initialize share buttons
    const currentUrl = encodeURIComponent(window.location.href);
    const currentTitle = encodeURIComponent(document.title);
    
    // Update share buttons URLs
    const facebookBtn = document.querySelector('.share-facebook');
    const twitterBtn = document.querySelector('.share-twitter');
    const whatsappBtn = document.querySelector('.share-whatsapp');
    
    if (facebookBtn) {
        facebookBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
        facebookBtn.target = '_blank';
    }
    
    if (twitterBtn) {
        twitterBtn.href = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${currentTitle}`;
        twitterBtn.target = '_blank';
    }
    
    if (whatsappBtn) {
        whatsappBtn.href = `https://wa.me/?text=${currentTitle}%20${currentUrl}`;
        whatsappBtn.target = '_blank';
    }
    
    // Add hover effect to content items
    const contentItems = document.querySelectorAll('.content-item');
    contentItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>