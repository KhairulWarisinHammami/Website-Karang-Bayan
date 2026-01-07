<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

$conn = connectDB();

$success = '';
$error = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field password harus diisi!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        // Get current password from database
        $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($admin = $result->fetch_assoc()) {
            if (password_verify($current_password, $admin['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $_SESSION['admin_id']);
                
                if ($update_stmt->execute()) {
                    $success = "Password berhasil diubah!";
                    $_POST = []; // Clear form
                } else {
                    $error = "Gagal mengubah password: " . $conn->error;
                }
                $update_stmt->close();
            } else {
                $error = "Password saat ini salah!";
            }
        }
        $stmt->close();
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username']);
    
    if (empty($username)) {
        $error = "Username tidak boleh kosong!";
    } else {
        // Check if username already exists (excluding current user)
        $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Update username
            $update_stmt = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
            $update_stmt->bind_param("si", $username, $_SESSION['admin_id']);
            
            if ($update_stmt->execute()) {
                $success = "Profile berhasil diperbarui!";
                $_SESSION['admin_username'] = $username;
            } else {
                $error = "Gagal memperbarui profile: " . $conn->error;
            }
            $update_stmt->close();
        }
        $stmt->close();
    }
}

// Get admin data
$admin_data = [];
$stmt = $conn->prepare("SELECT id, username, created_at FROM admin WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();
$stmt->close();

// Get system stats
$stats = [];
$stmt = $conn->query("SELECT 
    (SELECT COUNT(*) FROM contents) as total_contents,
    (SELECT COUNT(*) FROM reviews) as total_reviews,
    (SELECT COUNT(*) FROM reports) as total_reports,
    (SELECT COUNT(*) FROM admin) as total_admin");
$stats = $stmt->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Desa Karang Bayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c5530;
            --secondary: #4a8c4f;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 25px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .card-header h5 {
            color: var(--primary);
            margin: 0;
            font-weight: 600;
        }
        
        .profile-card {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 15px;
        }
        
        .profile-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
        }
        
        .stats-card {
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .bg-content { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-review { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
        .bg-report { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }
        .bg-admin { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(44, 85, 48, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 85, 48, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-4">
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x mb-3"></i>
                <h4>Admin Panel</h4>
                <p class="mb-0">Desa Karang Bayan</p>
                <small class="text-white-50"><?= htmlspecialchars($_SESSION['admin_username']) ?></small>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="contents.php" class="nav-link">
                        <i class="fas fa-newspaper"></i> Kelola Konten
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reviews.php" class="nav-link">
                        <i class="fas fa-star"></i> Kelola Review
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reports.php" class="nav-link">
                        <i class="fas fa-flag"></i> Kelola Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link active">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Pengaturan</h1>
                <p class="text-muted mb-0">Kelola akun dan pengaturan sistem</p>
            </div>
            <div>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-4">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="profile-card">
                        <div class="profile-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4><?= htmlspecialchars($_SESSION['admin_username']) ?></h4>
                        <p class="mb-2">Administrator</p>
                        <small>Bergabung: <?= date('d M Y', strtotime($admin_data['created_at'])) ?></small>
                    </div>
                </div>
                
                <!-- System Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Statistik Sistem
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="stats-card bg-content">
                                    <div class="number"><?= $stats['total_contents'] ?></div>
                                    <div class="label">Konten</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card bg-review">
                                    <div class="number"><?= $stats['total_reviews'] ?></div>
                                    <div class="label">Review</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card bg-report">
                                    <div class="number"><?= $stats['total_reports'] ?></div>
                                    <div class="label">Laporan</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card bg-admin">
                                    <div class="number"><?= $stats['total_admin'] ?></div>
                                    <div class="label">Admin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-md-8">
                <!-- Update Profile Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>
                            Update Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($admin_data['username']) ?>" 
                                       required placeholder="Masukkan username baru">
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Username ini akan digunakan untuk login ke admin panel.
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Change Password Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Ganti Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="passwordForm">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required placeholder="Masukkan password saat ini">
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required placeholder="Masukkan password baru">
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required placeholder="Konfirmasi password baru">
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Pastikan password baru Anda kuat dan mudah diingat.
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i> Ganti Password
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- System Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Sistem
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>PHP Version</strong></td>
                                        <td><?= phpversion() ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Database</strong></td>
                                        <td>MySQL</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Server Time</strong></td>
                                        <td><?= date('d M Y H:i:s') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Max Upload Size</strong></td>
                                        <td>5MB (Gambar), 50MB (Video)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supported Formats</strong></td>
                                        <td>JPG, PNG, GIF, WEBP, MP4, AVI, MOV, WMV</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <a href="#" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-question-circle me-1"></i> Bantuan
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-bug me-1"></i> Laporkan Bug
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('Password baru minimal 6 karakter!');
                document.getElementById('new_password').focus();
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Password baru dan konfirmasi tidak cocok!');
                document.getElementById('confirm_password').focus();
                return false;
            }
            
            return true;
        });
        
        // Show password strength
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update UI based on strength (you can add visual feedback here)
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Real-time password match checking
        confirmPasswordInput.addEventListener('input', function() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (confirmPassword) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    </script>
</body>
</html>