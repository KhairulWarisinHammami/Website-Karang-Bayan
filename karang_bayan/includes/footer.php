    </main> <!-- Menutup tag main yang dibuka di header.php -->

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Desa Karang Bayan</h3>
                    <p>Desa wisata yang menawarkan keindahan alam, budaya lokal, dan pengalaman yang tak terlupakan. Nikmati keramahan masyarakat dan keasrian alam pedesaan.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Raya Karang Bayan, Lombok Barat, NTB</p>
                    <p><i class="fas fa-phone"></i> (0370) 6123456</p>
                    <p><i class="fas fa-envelope"></i> info@desakarangbayan.id</p>
                    <p><i class="fas fa-clock"></i> Buka setiap hari: 08.00 - 17.00 WITA</p>
                </div>
                
                <div class="footer-section">
                    <h3>Tautan Cepat</h3>
                    <a href="index.php">Beranda</a>
                    <a href="tentang.php">Tentang Desa</a>
                    <a href="wisata.php">Destinasi Wisata</a>
                    <a href="galeri.php">Galeri Foto</a>
                    <a href="kontak.php">Hubungi Kami</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Desa Karang Bayan. Semua hak dilindungi.</p>
                <p>Dikembangkan dengan <i class="fas fa-heart" style="color:#e63946;"></i> untuk kemajuan desa wisata Indonesia</p>
            </div>
        </div>
    </footer>

    <style>
        /* Footer Styles */
        footer {
            background-color: #1a1a1a;
            color: #ddd;
            padding: 2.5rem 0 1.5rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            color: #ffd166;
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(255, 209, 102, 0.3);
        }
        
        .footer-section p, .footer-section a {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: #ffd166;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #333;
            border-radius: 50%;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background-color: #ffd166;
            color: #1a1a1a;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid #333;
            color: #888;
            font-size: 0.9rem;
        }
    </style>

    <script>
        // Mobile Menu Toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('show');
            
            // Ganti icon menu
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Rating Stars Functionality (hanya untuk halaman review)
        <?php if (basename($_SERVER['PHP_SELF']) == 'review.php'): ?>
        const stars = document.querySelectorAll('.stars i');
        const ratingInput = document.getElementById('rating');
        const ratingText = document.getElementById('ratingText');
        const ratingTexts = [
            "Klik bintang untuk memberikan rating",
            "Kurang Baik",
            "Cukup Baik",
            "Baik",
            "Sangat Baik",
            "Luar Biasa"
        ];
        
        if (stars.length > 0) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const ratingValue = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = ratingValue;
                    
                    // Update stars display
                    stars.forEach((s, index) => {
                        if (index < ratingValue) {
                            s.classList.remove('far');
                            s.classList.add('fas', 'active');
                        } else {
                            s.classList.remove('fas', 'active');
                            s.classList.add('far');
                        }
                    });
                    
                    // Update rating text
                    ratingText.textContent = ratingTexts[ratingValue];
                });
                
                // Hover effect
                star.addEventListener('mouseover', function() {
                    const hoverValue = parseInt(this.getAttribute('data-rating'));
                    
                    stars.forEach((s, index) => {
                        if (index < hoverValue) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
                
                // Reset to selected rating on mouseout
                star.addEventListener('mouseout', function() {
                    const currentRating = parseInt(ratingInput.value);
                    
                    stars.forEach((s, index) => {
                        if (index < currentRating) {
                            s.classList.remove('far');
                            s.classList.add('fas', 'active');
                        } else {
                            s.classList.remove('fas', 'active');
                            s.classList.add('far');
                        }
                    });
                });
            });
            
            // Form Submission untuk review
            const reviewForm = document.getElementById('reviewForm');
            if (reviewForm) {
                reviewForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form values
                    const name = document.getElementById('name').value;
                    const rating = document.getElementById('rating').value;
                    
                    // Validation
                    if (rating === "0") {
                        alert("Silakan berikan rating sebelum mengirim review.");
                        return;
                    }
                    
                    // In a real application, you would send this data to a server
                    // For demonstration, we'll just show an alert
                    alert(`Terima kasih ${name} atas review Anda!\nRating: ${rating}/5\nReview Anda telah berhasil dikirim.`);
                    
                    // Reset form
                    this.reset();
                    stars.forEach(star => {
                        star.classList.remove('fas', 'active');
                        star.classList.add('far');
                    });
                    ratingInput.value = "0";
                    ratingText.textContent = "Klik bintang untuk memberikan rating";
                });
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>