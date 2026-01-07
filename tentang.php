<?php
$pageTitle = "Tentang Desa";
include 'includes/header.php';
?>

<style>
/* About Page Styles */
.about-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Hero Section */
.about-hero {
    background: linear-gradient(rgba(44, 85, 48, 0.95), rgba(74, 140, 79, 0.9));
    color: white;
    padding: 100px 0 70px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
    background-size: cover;
    opacity: 0.15;
    z-index: 1;
}

.about-hero .container {
    position: relative;
    z-index: 2;
}

.about-hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    color: white;
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.4);
    animation: fadeInDown 1s ease;
}

.about-hero-subtitle {
    font-size: 1.6rem;
    font-weight: 300;
    margin-bottom: 30px;
    color: #ecf0f1;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    animation: fadeInUp 1s ease 0.3s both;
}

.about-hero-divider {
    width: 120px;
    height: 5px;
    background-color: #ffd166;
    margin: 0 auto 40px;
    border-radius: 3px;
    animation: slideIn 1s ease 0.6s both;
}

.about-hero-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #d4edda;
    max-width: 900px;
    margin: 0 auto 40px;
    animation: fadeIn 1s ease 0.9s both;
}

/* About Container */
.about-container {
    padding: 60px 0;
}

/* Section Common Styles */
.about-section {
    margin-bottom: 80px;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-icon {
    font-size: 3rem;
    color: #2c5530;
    margin-bottom: 20px;
    animation: bounceIn 1s ease;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, #2c5530, #4a8c4f);
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    max-width: 700px;
    margin: 20px auto 0;
    line-height: 1.6;
}

/* History Section */
.history-timeline {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 0;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #2c5530, #4a8c4f);
    transform: translateX(-50%);
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 60px;
    width: 45%;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease;
}

.timeline-item.visible {
    opacity: 1;
    transform: translateY(0);
}

.timeline-item:nth-child(odd) {
    left: 0;
    text-align: right;
    padding-right: 60px;
}

.timeline-item:nth-child(even) {
    left: 55%;
    padding-left: 60px;
}

.timeline-year {
    position: absolute;
    top: 0;
    background: #2c5530;
    color: white;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(44, 85, 48, 0.3);
    z-index: 2;
}

.timeline-item:nth-child(odd) .timeline-year {
    right: -20px;
}

.timeline-item:nth-child(even) .timeline-year {
    left: -20px;
}

.timeline-content {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    border-color: #2c5530;
}

.timeline-content h4 {
    color: #2c5530;
    font-size: 1.4rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.timeline-content p {
    color: #6c757d;
    line-height: 1.7;
    margin: 0;
}

.timeline-dot {
    position: absolute;
    top: 20px;
    width: 20px;
    height: 20px;
    background: white;
    border: 4px solid #2c5530;
    border-radius: 50%;
    z-index: 2;
}

.timeline-item:nth-child(odd) .timeline-dot {
    right: -48px;
}

.timeline-item:nth-child(even) .timeline-dot {
    left: -48px;
}

/* Vision Mission Section */
.vision-mission {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
}

.vm-card {
    background: white;
    padding: 40px 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(30px);
}

.vm-card.visible {
    opacity: 1;
    transform: translateY(0);
}

.vm-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    border-color: #2c5530;
}

.vm-icon {
    font-size: 3rem;
    color: #2c5530;
    margin-bottom: 25px;
}

.vm-card h3 {
    color: #2c5530;
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-weight: 600;
}

.vm-card p {
    color: #6c757d;
    line-height: 1.8;
    margin-bottom: 20px;
}

.vm-list {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.vm-list li {
    color: #495057;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 12px;
}

.vm-list li:last-child {
    border-bottom: none;
}

.vm-list i {
    color: #2c5530;
    font-size: 1.2rem;
}

/* Geography Section */
.geography-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
    max-width: 1100px;
    margin: 0 auto;
}

.geography-image {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    transition: all 0.5s ease;
    opacity: 0;
    transform: translateX(-50px);
}

.geography-image.visible {
    opacity: 1;
    transform: translateX(0);
}

.geography-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.8s ease;
}

.geography-image:hover img {
    transform: scale(1.05);
}

.geography-image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    padding: 30px;
    color: white;
}

.geography-image-overlay h4 {
    margin: 0 0 10px 0;
    font-size: 1.4rem;
}

.geography-info {
    opacity: 0;
    transform: translateX(50px);
    transition: all 0.5s ease 0.3s;
}

.geography-info.visible {
    opacity: 1;
    transform: translateX(0);
}

.geography-info h3 {
    color: #2c5530;
    font-size: 2rem;
    margin-bottom: 25px;
    font-weight: 700;
}

.geography-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin: 30px 0;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    border-color: #2c5530;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.geography-description {
    color: #6c757d;
    line-height: 1.8;
    font-size: 1.05rem;
    margin-top: 25px;
}

/* Culture Section */
.culture-highlights {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}

.culture-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(30px);
}

.culture-item.visible {
    opacity: 1;
    transform: translateY(0);
}

.culture-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    border-color: #2c5530;
}

.culture-image {
    height: 200px;
    overflow: hidden;
}

.culture-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.culture-item:hover .culture-image img {
    transform: scale(1.1);
}

.culture-body {
    padding: 25px;
}

.culture-body h4 {
    color: #2c5530;
    font-size: 1.3rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.culture-body p {
    color: #6c757d;
    line-height: 1.7;
    margin: 0;
}

/* CTA Section */
.about-cta {
    background: linear-gradient(135deg, #2c5530 0%, #4a8c4f 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
    border-radius: 20px;
    margin-top: 60px;
    position: relative;
    overflow: hidden;
}

.about-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
    background-size: cover;
    opacity: 0.1;
}

.about-cta .container {
    position: relative;
    z-index: 2;
}

.cta-title {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: white;
}

.cta-text {
    font-size: 1.3rem;
    margin-bottom: 40px;
    opacity: 0.9;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-cta {
    padding: 16px 35px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.btn-cta-primary {
    background: white;
    color: #2c5530;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.btn-cta-primary:hover {
    background: #f8f9fa;
    color: #3a6b3e;
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
}

.btn-cta-secondary {
    background: transparent;
    color: white;
    border: 3px solid white;
}

.btn-cta-secondary:hover {
    background: white;
    color: #2c5530;
    transform: translateY(-3px);
}

/* Responsive Design */
@media (max-width: 992px) {
    .about-hero-title {
        font-size: 2.8rem;
    }
    
    .geography-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .geography-image {
        transform: translateX(0);
    }
    
    .geography-info {
        transform: translateX(0);
    }
}

@media (max-width: 768px) {
    .about-hero {
        padding: 80px 0 50px;
    }
    
    .about-hero-title {
        font-size: 2.4rem;
    }
    
    .about-hero-subtitle {
        font-size: 1.3rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .timeline-item {
        width: 90%;
        left: 5% !important;
        padding-left: 60px !important;
        padding-right: 20px !important;
        text-align: left !important;
    }
    
    .timeline-year {
        left: -20px !important;
        right: auto !important;
    }
    
    .timeline-dot {
        left: -48px !important;
        right: auto !important;
    }
    
    .history-timeline::before {
        left: 0;
    }
    
    .geography-stats {
        grid-template-columns: 1fr;
    }
    
    .cta-title {
        font-size: 2.2rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-cta {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .about-hero-title {
        font-size: 2rem;
    }
    
    .about-hero-subtitle {
        font-size: 1.1rem;
    }
    
    .section-title {
        font-size: 1.7rem;
    }
    
    .vision-mission {
        grid-template-columns: 1fr;
    }
    
    .culture-highlights {
        grid-template-columns: 1fr;
    }
    
    .cta-title {
        font-size: 1.8rem;
    }
    
    .cta-text {
        font-size: 1.1rem;
    }
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
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

@keyframes slideIn {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        width: 120px;
        opacity: 1;
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
    }
}

.geography-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.geography-map {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.map-container {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 400px;
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: none;
    display: block;
}

.map-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    padding: 20px;
    text-align: center;
}

.map-overlay h4 {
    margin: 0 0 5px 0;
    font-size: 1.2rem;
}

.map-overlay p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.geography-image {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    height: 200px;
}

.geography-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.geography-image:hover img {
    transform: scale(1.05);
}

.geography-image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    padding: 15px;
}

.geography-info {
    padding: 20px;
}

.geography-info h3 {
    color: #2c5530;
    margin-bottom: 30px;
    font-size: 1.8rem;
}

.geography-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 992px) {
    .geography-content {
        grid-template-columns: 1fr;
    }
    
    .geography-map {
        order: 1;
    }
    
    .geography-info {
        order: 2;
    }
}

@media (max-width: 576px) {
    .geography-stats {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2rem;
    }
}
</style>

<div class="about-page">
    <!-- Hero Section -->
    <div class="about-hero">
        <div class="container">
            <h1 class="about-hero-title">Desa Karang Bayan</h1>
            <p class="about-hero-subtitle">Desa Wisata Budaya & Alam yang Kaya Akan Keindahan</p>
            <div class="about-hero-divider"></div>
            <p class="about-hero-text">
                Desa Karang Bayan merupakan salah satu desa wisata unggulan yang menyajikan 
                harmoni antara keindahan alam, kekayaan budaya, dan keramahan masyarakat. 
                Terletak di wilayah yang asri, desa ini menjadi destinasi favorit bagi 
                wisatawan yang ingin menikmati kedamaian pedesaan sambil mempelajari 
                kearifan lokal yang masih terjaga.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container about-container">
        <!-- Sejarah Desa -->
        <section class="about-section" id="sejarah">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h2 class="section-title">Sejarah Desa</h2>
                <p class="section-subtitle">
                    Melacak jejak perjalanan Desa Karang Bayan dari masa ke masa
                </p>
            </div>
            
            <div class="history-timeline">
                <div class="timeline-item">
                    <div class="timeline-year">1850</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h4>Masa Awal Pemukiman</h4>
                        <p>
                            Desa Karang Bayan mulai dihuni oleh sekelompok masyarakat 
                            yang mencari lahan pertanian subur. Nama "Karang Bayan" 
                            diambil dari keberadaan karang besar yang menjadi landmark 
                            alami di tengah pemukiman.
                        </p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">1920</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h4>Pembentukan Desa Resmi</h4>
                        <p>
                            Desa Karang Bayan resmi berdiri sebagai desa administratif. 
                            Sistem pemerintahan desa mulai terbentuk dengan struktur 
                            yang terorganisir dan peraturan adat yang kuat.
                        </p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">1975</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h4>Pengembangan Pertanian</h4>
                        <p>
                            Desa mengalami perkembangan pesat di sektor pertanian. 
                            Sistem irigasi tradisional dibangun dan teknik pertanian 
                            organik mulai diterapkan secara luas.
                        </p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2005</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h4>Penetapan Desa Wisata</h4>
                        <p>
                            Atas dasar potensi alam dan budaya yang dimiliki, 
                            Desa Karang Bayan secara resmi ditetapkan sebagai 
                            desa wisata oleh pemerintah daerah.
                        </p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h4>Modernisasi Berkelanjutan</h4>
                        <p>
                            Desa Karang Bayan mengembangkan infrastruktur wisata 
                            modern tanpa mengesampingkan kelestarian alam dan 
                            budaya tradisional.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visi & Misi -->
        <section class="about-section" id="visi-misi">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h2 class="section-title">Visi & Misi</h2>
                <p class="section-subtitle">
                    Panduan dan tujuan pembangunan Desa Karang Bayan
                </p>
            </div>
            
            <div class="vision-mission">
                <div class="vm-card">
                    <div class="vm-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Visi Desa</h3>
                    <p>
                        "Menjadi desa wisata berkelanjutan yang mandiri, 
                        sejahtera, dan berwawasan lingkungan dengan melestarikan 
                        kearifan lokal dan mengembangkan potensi alam secara optimal."
                    </p>
                </div>
                
                <div class="vm-card">
                    <div class="vm-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Misi Desa</h3>
                    <ul class="vm-list">
                        <li><i class="fas fa-check-circle"></i> Mengembangkan pariwisata berbasis alam dan budaya</li>
                        <li><i class="fas fa-check-circle"></i> Melestarikan adat istiadat dan tradisi lokal</li>
                        <li><i class="fas fa-check-circle"></i> Meningkatkan kesejahteraan masyarakat</li>
                        <li><i class="fas fa-check-circle"></i> Mengelola sumber daya alam secara berkelanjutan</li>
                        <li><i class="fas fa-check-circle"></i> Membangun infrastruktur yang mendukung pariwisata</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Geografi & Demografi -->
        <section class="about-section" id="geografi">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h2 class="section-title">Geografi & Demografi</h2>
                <p class="section-subtitle">
                    Kondisi geografis dan karakteristik penduduk Desa Karang Bayan
                </p>
            </div>
            
          <div class="geography-content">
    <div class="geography-map">
        <!-- Google Maps Embed -->
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31585.385726289058!2d116.2534542371582!3d-8.510172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcc0ea138945177%3A0x82ad5ae0d1874242!2sKarang%20Bayan%2C%20Lingsar%2C%20West%20Lombok%20Regency%2C%20West%20Nusa%20Tenggara!5e0!3m2!1sen!2sid!4v1702739200000!5m2!1sen!2sid" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <div class="map-overlay">
                <h4>Peta Lokasi Desa Karang Bayan</h4>
                <p>Klik untuk menjelajahi area</p>
            </div>
        </div>
        
        <!-- Gambar Tambahan (opsional) -->
        <div class="geography-image">
            <img src="https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                 alt="Pemandangan Desa Karang Bayan">
            <div class="geography-image-overlay">
                <h4>Pemandangan Alam</h4>
                <p>Panorama perbukitan dan sawah</p>
            </div>
        </div>
    </div>
    
    <div class="geography-info">
        <h3>Karakteristik Wilayah</h3>
        
        <div class="geography-stats">
            <div class="stat-item">
                <div class="stat-number">1.250</div>
                <div class="stat-label">Hektar Luas Wilayah</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">3.245</div>
                <div class="stat-label">Jiwa Penduduk</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">750</div>
                <div class="stat-label">Kepala Keluarga</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">7</div>
                <div class="stat-label">Dusun / Lingkungan</div>
            </div>
        </div>
                    
                    <div class="geography-description">
                        <p>
                            Desa Karang Bayan terletak pada ketinggian 250-500 mdpl dengan 
                            topografi berupa perbukitan dan lembah. Memiliki iklim tropis 
                            dengan suhu rata-rata 22-28°C. Sumber daya alam utama meliputi 
                            hutan produksi, lahan pertanian, dan beberapa mata air alami.
                        </p>
                        <p>
                            Mayoritas penduduk bekerja di sektor pertanian, peternakan, 
                            dan pariwisata. Tingkat pendidikan masyarakat terus meningkat 
                            dengan adanya fasilitas pendidikan dari PAUD hingga SMP.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Budaya & Tradisi -->
        <section class="about-section" id="budaya">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-theater-masks"></i>
                </div>
                <h2 class="section-title">Budaya & Tradisi</h2>
                <p class="section-subtitle">
                    Kekayaan budaya yang menjadi identitas Desa Karang Bayan
                </p>
            </div>
            
            <div class="culture-highlights">
                <div class="culture-item">
                    <div class="culture-image">
                        <img src="https://images.unsplash.com/photo-1562086788-b2d5d2b6a4e3?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                             alt="Tari Tradisional">
                    </div>
                    <div class="culture-body">
                        <h4>Tari Balean Dadas</h4>
                        <p>
                            Tarian tradisional yang melambangkan rasa syukur kepada 
                            alam. Dilakukan dalam upacara-upacara adat penting.
                        </p>
                    </div>
                </div>
                
                <div class="culture-item">
                    <div class="culture-image">
                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                             alt="Kuliner Khas">
                    </div>
                    <div class="culture-body">
                        <h4>Kuliner Khas</h4>
                        <p>
                            Ayam Taliwang, Plecing Kangkung, dan Sate Rembiga 
                            adalah beberapa kuliner khas yang wajib dicoba.
                        </p>
                    </div>
                </div>
                
                <div class="culture-item">
                    <div class="culture-image">
                        <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                             alt="Kerajinan Tangan">
                    </div>
                    <div class="culture-body">
                        <h4>Kerajinan Tenun</h4>
                        <p>
                            Tenun tangan dengan motif tradisional yang dibuat 
                            oleh pengrajin lokal menggunakan teknik turun-temurun.
                        </p>
                    </div>
                </div>
                
                <div class="culture-item">
                    <div class="culture-image">
                        <img src="https://images.unsplash.com/photo-1527525443983-6e60c75fff46?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" 
                             alt="Festival Budaya">
                    </div>
                    <div class="culture-body">
                        <h4>Festival Tahunan</h4>
                        <p>
                            Festival Bumi Karang diselenggarakan setiap tahun 
                            untuk merayakan keberagaman budaya dan hasil bumi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="about-cta">
            <div class="container">
                <h2 class="cta-title">Tertarik Mengunjungi Desa Karang Bayan?</h2>
                <p class="cta-text">
                    Rencanakan perjalanan Anda dan nikmati pengalaman tak terlupakan 
                    di tengah keindahan alam dan keramahan masyarakat Desa Karang Bayan.
                </p>
                <div class="cta-buttons">
                    <a href="kontak.php" class="btn-cta btn-cta-primary">
                        <i class="fas fa-phone-alt"></i> Hubungi Kami
                    </a>
                    <a href="review.php" class="btn-cta btn-cta-secondary">
                        <i class="fas fa-star"></i> Berikan Review
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    // Observe timeline items
    document.querySelectorAll('.timeline-item').forEach(item => {
        observer.observe(item);
    });
    
    // Observe vision mission cards
    document.querySelectorAll('.vm-card').forEach(card => {
        observer.observe(card);
    });
    
    // Observe geography elements
    document.querySelectorAll('.geography-image, .geography-info').forEach(el => {
        observer.observe(el);
    });
    
    // Observe culture items
    document.querySelectorAll('.culture-item').forEach(item => {
        observer.observe(item);
    });
    
    // Smooth scrolling for navigation
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
    
    // Add parallax effect to hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.about-hero');
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
    
    // Add hover effect to cards
    const cards = document.querySelectorAll('.vm-card, .stat-item, .culture-item');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>