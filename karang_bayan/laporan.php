<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $report_type = sanitize($_POST['report_type']);
    $description = sanitize($_POST['description']);
    
    if (empty($name) || empty($report_type) || empty($description)) {
        $error = "Semua field yang wajib diisi harus dilengkapi!";
    } else {
        $conn = connectDB();
        $stmt = $conn->prepare("INSERT INTO reports (name, email, report_type, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $report_type, $description);
        
        if ($stmt->execute()) {
            $success = "Laporan berhasil dikirim! Terima kasih atas kontribusi Anda.";
            $_POST = []; // Clear form
        } else {
            $error = "Gagal mengirim laporan: " . $conn->error;
        }
        
        $stmt->close();
        $conn->close();
    }
}

include 'includes/header.php';
?>

<style>
/* Report Page Styles - Sinkron dengan tema hijau (#2c5530) */
.report-page {
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Hero Section */
.report-hero {
    background: linear-gradient(rgba(44, 85, 48, 0.9), rgba(44, 85, 48, 0.95));
    color: white;
    padding: 60px 0 40px;
    text-align: center;
    position: relative;
}

.report-hero-title {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: white;
}

.report-hero-subtitle {
    font-size: 1.4rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ecf0f1;
}

.report-hero-divider {
    width: 80px;
    height: 3px;
    background-color: #4a8c4f;
    margin: 0 auto 25px;
}

.report-hero-description {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    color: #d4edda;
    line-height: 1.6;
}

/* Report Container */
.report-container {
    padding: 40px 0;
    max-width: 900px;
    margin: 0 auto;
}

/* Report Card */
.report-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.report-card-header {
    background: #2c5530;
    color: white;
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.report-card-header h4 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.report-card-header i {
    color: #4a8c4f;
}

.report-card-body {
    padding: 30px;
}

/* Report Alerts */
.report-alert {
    display: flex;
    align-items: flex-start;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: 1px solid transparent;
    animation: slideDown 0.3s ease;
}

.report-alert.success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
    border-left: 4px solid #2c5530;
}

.report-alert.error {
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

.report-alert.success .alert-icon {
    color: #2c5530;
}

.report-alert.error .alert-icon {
    color: #dc3545;
}

.alert-content h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Intro Text */
.report-intro {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 1rem;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #4a8c4f;
}

/* Report Form */
.report-form {
    padding: 10px 0;
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
    margin-bottom: 25px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #2c5530;
    margin-bottom: 8px;
    font-size: 1rem;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    border-color: #4a8c4f;
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
}

.form-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
    cursor: pointer;
}

.form-select:focus {
    border-color: #4a8c4f;
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
}

.form-text {
    display: block;
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 5px;
    line-height: 1.5;
}

/* Textarea */
.report-textarea {
    min-height: 150px;
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
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
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

.btn-report {
    background-color: #2c5530;
    color: white;
    flex: 1;
}

.btn-report:hover {
    background-color: #3a6b3e;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(44, 85, 48, 0.2);
}

.btn-back {
    background-color: #6c757d;
    color: white;
}

.btn-back:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
}

/* Report Type Options */
.report-type-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.report-type-option {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    background: #f8f9fa;
}

.report-type-option:hover {
    border-color: #4a8c4f;
    background: #e9f5ea;
}

.report-type-option.selected {
    border-color: #2c5530;
    background: #d4edda;
    color: #155724;
    font-weight: 600;
}

.report-type-icon {
    font-size: 1.5rem;
    margin-bottom: 8px;
    color: #4a8c4f;
}

.report-type-option.selected .report-type-icon {
    color: #2c5530;
}

/* Report Information Box */
.report-info-box {
    background: #e7f3e9;
    border-left: 4px solid #2c5530;
    padding: 15px;
    border-radius: 0 8px 8px 0;
    margin: 20px 0;
}

.report-info-box h5 {
    color: #2c5530;
    margin-bottom: 10px;
    font-size: 1.1rem;
    font-weight: 600;
}

.report-info-box ul {
    margin: 0;
    padding-left: 20px;
    color: #495057;
}

.report-info-box li {
    margin-bottom: 5px;
    line-height: 1.5;
}

/* Animations */
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

.report-card {
    animation: fadeIn 0.5s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .report-hero-title {
        font-size: 2.2rem;
    }
    
    .report-hero-subtitle {
        font-size: 1.2rem;
    }
    
    .report-hero-description {
        font-size: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .report-type-options {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .report-hero {
        padding: 40px 0 30px;
    }
    
    .report-container {
        padding: 20px 15px;
    }
    
    .report-card-body {
        padding: 20px;
    }
    
    .report-card-header {
        padding: 15px 20px;
    }
    
    .report-card-header h4 {
        font-size: 1.4rem;
    }
}

/* Optional: Confirmation Message */
.confirmation-message {
    text-align: center;
    padding: 40px 20px;
    animation: fadeIn 0.5s ease;
}

.confirmation-icon {
    font-size: 4rem;
    color: #2c5530;
    margin-bottom: 20px;
}

.confirmation-message h3 {
    color: #2c5530;
    margin-bottom: 15px;
}

.confirmation-message p {
    color: #6c757d;
    margin-bottom: 20px;
    line-height: 1.6;
}
</style>

<div class="report-page">
    <!-- Hero Section -->
    <div class="report-hero">
        <div class="container">
            <h1 class="report-hero-title">Desa Karang Bayan</h1>
            <h2 class="report-hero-subtitle">Sistem Pelaporan Masyarakat</h2>
            <div class="report-hero-divider"></div>
            <p class="report-hero-description">
                Sampaikan laporan dan saran Anda untuk kemajuan Desa Karang Bayan
            </p>
        </div>
    </div>

    <div class="container report-container">
        <!-- Report Card -->
        <div class="report-card">
            <div class="report-card-header">
                <h4><i class="fas fa-flag"></i> Kirim Laporan</h4>
            </div>
            <div class="report-card-body">
                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="report-alert success">
                        <div class="alert-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="alert-content">
                            <h4>Laporan Berhasil Dikirim!</h4>
                            <p><?= htmlspecialchars($success) ?></p>
                            <small>Laporan Anda telah direkam dan akan segera ditindaklanjuti.</small>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="report-alert error">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alert-content">
                            <h4>Oops! Terjadi Kesalahan</h4>
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Intro Text -->
                <div class="report-intro">
                    <p>
                        Laporkan masalah atau berikan saran untuk perbaikan Desa Karang Bayan. 
                        Laporan Anda akan membantu kami meningkatkan pelayanan dan fasilitas.
                    </p>
                </div>
                
                <!-- Report Information Box -->
                <div class="report-info-box">
                    <h5><i class="fas fa-info-circle"></i> Informasi Penting:</h5>
                    <ul>
                        <li>Pastikan laporan Anda jelas dan detail</li>
                        <li>Sertakan lokasi dan waktu kejadian jika memungkinkan</li>
                        <li>Laporan akan ditindaklanjuti dalam waktu 1-3 hari kerja</li>
                        <li>Email digunakan hanya untuk konfirmasi, tidak akan dipublikasikan</li>
                    </ul>
                </div>
                
                <!-- Report Form -->
                <form method="POST" action="" id="reportForm" class="report-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nama Lengkap <span class="required">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                                   placeholder="Masukkan nama lengkap Anda" required>
                            <small class="form-text">Nama Anda akan dicatat sebagai pelapor</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email
                            </label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                   placeholder="email@contoh.com">
                            <small class="form-text">
                                <i class="fas fa-lock"></i> Email tidak akan ditampilkan publik
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="report_type" class="form-label">
                            Jenis Laporan <span class="required">*</span>
                        </label>
                        <select id="report_type" name="report_type" class="form-select" required>
                            <option value="">Pilih jenis laporan</option>
                            <option value="infrastruktur" <?= (isset($_POST['report_type']) && $_POST['report_type'] == 'infrastruktur') ? 'selected' : '' ?>>Infrastruktur</option>
                            <option value="kebersihan" <?= (isset($_POST['report_type']) && $_POST['report_type'] == 'kebersihan') ? 'selected' : '' ?>>Kebersihan</option>
                            <option value="keamanan" <?= (isset($_POST['report_type']) && $_POST['report_type'] == 'keamanan') ? 'selected' : '' ?>>Keamanan</option>
                            <option value="administrasi" <?= (isset($_POST['report_type']) && $_POST['report_type'] == 'administrasi') ? 'selected' : '' ?>>Administrasi</option>
                            <option value="lainnya" <?= (isset($_POST['report_type']) && $_POST['report_type'] == 'lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                        <small class="form-text">Pilih kategori yang sesuai dengan laporan Anda</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Deskripsi Laporan <span class="required">*</span>
                        </label>
                        <textarea id="description" name="description" class="form-control report-textarea" 
                                  rows="5" required placeholder="Jelaskan laporan Anda secara detail..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                        <div class="textarea-footer">
                            <small class="form-text">
                                Jelaskan dengan jelas apa yang ingin Anda laporkan. Sertakan lokasi dan waktu jika memungkinkan.
                            </small>
                            <div class="char-counter" id="charCounter">
                                <span id="charCount">0</span>/100
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-report">
                            <i class="fas fa-paper-plane"></i> Kirim Laporan
                        </button>
                        <a href="index.php" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript untuk interaktivitas
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const charCounter = document.getElementById('charCounter');
    
    descriptionTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length < 20) {
            charCounter.classList.add('warning');
            charCounter.innerHTML = `<span id="charCount">${length}</span>/100 <small>(minimal 20 karakter)</small>`;
        } else {
            charCounter.classList.remove('warning');
            charCounter.innerHTML = `<span id="charCount">${length}</span>/100`;
        }
    });
    
    // Trigger input event untuk inisialisasi
    descriptionTextarea.dispatchEvent(new Event('input'));
    
    // Form validation
    const reportForm = document.getElementById('reportForm');
    reportForm.addEventListener('submit', function(e) {
        const description = descriptionTextarea.value.trim();
        const reportType = document.getElementById('report_type').value;
        
        if (description.length < 20) {
            e.preventDefault();
            // Create custom alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'report-alert error';
            alertDiv.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Deskripsi Terlalu Pendek!</h4>
                    <p>Deskripsi laporan harus minimal 20 karakter.</p>
                </div>
            `;
            
            // Insert alert before form
            const cardBody = document.querySelector('.report-card-body');
            const intro = document.querySelector('.report-intro');
            cardBody.insertBefore(alertDiv, intro.nextSibling);
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus on textarea
            descriptionTextarea.focus();
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
            
            return false;
        }
        
        if (!reportType) {
            e.preventDefault();
            // Create custom alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'report-alert error';
            alertDiv.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Jenis Laporan Belum Dipilih!</h4>
                    <p>Harap pilih jenis laporan.</p>
                </div>
            `;
            
            // Insert alert before form
            const cardBody = document.querySelector('.report-card-body');
            const intro = document.querySelector('.report-intro');
            cardBody.insertBefore(alertDiv, intro.nextSibling);
            
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
});
</script>

<?php include 'includes/footer.php'; ?>