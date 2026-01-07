<?php
// kontak.php
$pageTitle = "Kontak Kami";
require_once 'includes/header.php';

// Konfigurasi untuk pengiriman email (simulasi)
$success = '';
$error = '';
$name = $email = $subject = $message = '';

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validasi
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // Simulasi pengiriman email (dalam produksi, gunakan PHPMailer atau mail())
        $to = "info@desakarangbayan.id";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_body = "
        <html>
        <head>
            <title>Pesan dari Website Desa Karang Bayan</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2c5530; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #333; }
                .value { color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Pesan Baru dari Website</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Nama:</div>
                        <div class='value'>$name</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>$email</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Subjek:</div>
                        <div class='value'>$subject</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Pesan:</div>
                        <div class='value'>" . nl2br($message) . "</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Dalam produksi, uncomment baris berikut dan sesuaikan dengan konfigurasi server
        /*
        if (mail($to, $subject, $email_body, $headers)) {
            $success = "Pesan Anda telah berhasil dikirim! Kami akan membalas secepatnya.";
            $name = $email = $subject = $message = ''; // Reset form
        } else {
            $error = "Maaf, terjadi kesalahan dalam pengiriman pesan. Silakan coba lagi.";
        }
        */
        
        // Untuk demo, kita simulasikan berhasil
        $success = "Pesan Anda telah berhasil dikirim! Kami akan membalas secepatnya.";
        $name = $email = $subject = $message = ''; // Reset form
    }
}
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Hubungi Kami</h1>
            <p class="hero-subtitle">Pusat informasi dan komunikasi Desa Karang Bayan</p>
        </div>
    </div>
</section>

<!-- Kontak Section -->
<section class="kontak-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Kontak & Informasi</h2>
            <p class="section-subtitle">Jangan ragu untuk menghubungi kami untuk informasi lebih lanjut</p>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row g-4">
            <!-- Informasi Kontak -->
            <div class="col-lg-5">
                <div class="kontak-info-card" style="background: white; border-radius: 15px; padding: 40px 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); height: 100%;">
                    <h3 class="mb-4" style="color: #2c5530; font-size: 1.8rem; font-weight: 600;">Informasi Kontak</h3>
                    
                    <div class="kontak-item d-flex align-items-start mb-4">
                        <div class="kontak-icon" style="background: linear-gradient(135deg, #2c5530 0%, #4a8c4f 100%); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 style="color: #333; font-weight: 600; margin-bottom: 5px;">Alamat Kantor Desa</h5>
                            <p style="color: #666; margin: 0;">Jl. Raya Karang Bayan No. 123, Lingsar, Lombok Barat, Nusa Tenggara Barat 83371</p>
                        </div>
                    </div>
                    
                    <div class="kontak-item d-flex align-items-start mb-4">
                        <div class="kontak-icon" style="background: linear-gradient(135deg, #2c5530 0%, #4a8c4f 100%); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h5 style="color: #333; font-weight: 600; margin-bottom: 5px;">Telepon & WhatsApp</h5>
                            <p style="color: #666; margin: 0 0 5px 0;">
                                <a href="tel:+623706123456" style="color: #2c5530; text-decoration: none;">(0370) 6123456</a>
                            </p>
                            <p style="color: #666; margin: 0;">
                                <a href="https://wa.me/6281234567890" style="color: #25D366; text-decoration: none;">
                                    <i class="fab fa-whatsapp me-1"></i> 0812-3456-7890
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="kontak-item d-flex align-items-start mb-4">
                        <div class="kontak-icon" style="background: linear-gradient(135deg, #2c5530 0%, #4a8c4f 100%); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h5 style="color: #333; font-weight: 600; margin-bottom: 5px;">Email</h5>
                            <p style="color: #666; margin: 0 0 5px 0;">
                                <a href="mailto:info@desakarangbayan.id" style="color: #2c5530; text-decoration: none;">info@desakarangbayan.id</a>
                            </p>
                            <p style="color: #666; margin: 0;">
                                <a href="mailto:sekretariat@desakarangbayan.id" style="color: #2c5530; text-decoration: none;">sekretariat@desakarangbayan.id</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="kontak-item d-flex align-items-start mb-4">
                        <div class="kontak-icon" style="background: linear-gradient(135deg, #2c5530 0%, #4a8c4f 100%); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5 style="color: #333; font-weight: 600; margin-bottom: 5px;">Jam Operasional</h5>
                            <p style="color: #666; margin: 0;">Senin - Jumat: 08.00 - 16.00 WITA</p>
                            <p style="color: #666; margin: 0;">Sabtu: 08.00 - 14.00 WITA</p>
                            <p style="color: #666; margin: 0;">Minggu & Hari Libur: Tutup</p>
                        </div>
                    </div>
                    
                    <!-- Sosial Media -->
                    <div class="mt-5">
                        <h5 style="color: #333; font-weight: 600; margin-bottom: 20px;">Ikuti Kami di Sosial Media</h5>
                        <div class="social-links d-flex gap-3">
                            <a href="#" class="social-icon" style="width: 45px; height: 45px; background: #3b5998; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon" style="width: 45px; height: 45px; background: #E4405F; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-icon" style="width: 45px; height: 45px; background: #1DA1F2; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-icon" style="width: 45px; height: 45px; background: #FF0000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s;">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Kontak -->

<!-- Map Section -->
<section class="map-section" style="padding: 60px 0 0 0;">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Lokasi Kami</h2>
            <p class="section-subtitle">Kunjungi kantor desa kami di lokasi berikut</p>
        </div>
        
        <div class="map-container" style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); height: 500px;">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31585.385726289058!2d116.2534542371582!3d-8.510172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcc0ea138945177%3A0x82ad5ae0d1874242!2sKarang%20Bayan%2C%20Lingsar%2C%20West%20Lombok%20Regency%2C%20West%20Nusa%20Tenggara!5e0!3m2!1sen!2sid!4v1702739200000!5m2!1sen!2sid" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4 text-center">
                <div class="transport-info p-3">
                    <i class="fas fa-bus fa-2x mb-3" style="color: #2c5530;"></i>
                    <h5>Akses Transportasi</h5>
                    <p class="small">15 menit dari Bandara Internasional Lombok</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="transport-info p-3">
                    <i class="fas fa-car fa-2x mb-3" style="color: #2c5530;"></i>
                    <h5>Parkir Tersedia</h5>
                    <p class="small">Area parkir luas untuk kendaraan pribadi dan bus</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="transport-info p-3">
                    <i class="fas fa-wheelchair fa-2x mb-3" style="color: #2c5530;"></i>
                    <h5>Akses Ramah Disabilitas</h5>
                    <p class="small">Fasilitas lengkap untuk pengunjung disabilitas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->

<style>
/* Additional Styles */
.kontak-section .kontak-icon {
    transition: transform 0.3s;
}

.kontak-section .kontak-item:hover .kontak-icon {
    transform: scale(1.1);
}

.social-icon:hover {
    transform: translateY(-3px) !important;
}

.faq-item {
    transition: transform 0.3s;
}

.faq-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.transport-info {
    transition: all 0.3s;
    border-radius: 10px;
}

.transport-info:hover {
    background: white;
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Form Focus Styles */
.form-control:focus {
    border-color: #2c5530 !important;
    box-shadow: 0 0 0 0.25rem rgba(44, 85, 48, 0.25) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .kontak-section .row {
        flex-direction: column;
    }
    
    .map-section iframe {
        height: 300px;
    }
    
    .faq-section .row {
        flex-direction: column;
    }
}

/* Animation for alert */
.alert {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi!');
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid!');
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
            submitBtn.disabled = true;
            
            return true;
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // WhatsApp button click tracking
    const waLink = document.querySelector('a[href*="wa.me"]');
    if (waLink) {
        waLink.addEventListener('click', function() {
            // You can add analytics tracking here
            console.log('WhatsApp link clicked');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>