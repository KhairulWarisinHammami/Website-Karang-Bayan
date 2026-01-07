<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

$conn = connectDB();

// Ambil statistik
$stats = [];

// Total konten
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM contents");
$stmt->execute();
$stats['contents'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Total review
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews");
$stmt->execute();
$stats['reviews'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Total laporan
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reports");
$stmt->execute();
$stats['reports'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Review pending
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews WHERE status = 'pending'");
$stmt->execute();
$stats['pending_reviews'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Laporan pending
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reports WHERE status = 'pending'");
$stmt->execute();
$stats['pending_reports'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Konten terbaru
$stmt = $conn->prepare("SELECT * FROM contents ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_contents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Review terbaru
$stmt = $conn->prepare("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Desa Karang Bayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #10b981;
            --info: #3b82f6;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
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
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, var(--info) 0%, #60a5fa 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #fbbf24 100%);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .badge-pending {
            background-color: var(--warning);
            color: white;
        }
        
        .badge-approved {
            background-color: var(--success);
            color: white;
        }
        
        .badge-processed {
            background-color: var(--info);
            color: white;
        }
        
        .badge-resolved {
            background-color: var(--success);
            color: white;
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
                    <a href="dashboard.php" class="nav-link active">
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
                    <a href="settings.php" class="nav-link">
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
            <h1 class="h3 mb-0">Dashboard</h1>
            <div>
                <span class="text-muted me-3"><?= date('d F Y') ?></span>
                <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt me-2"></i> Lihat Website
                </a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-white bg-gradient-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Konten</h5>
                                <h2 class="mb-0"><?= $stats['contents'] ?></h2>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-white bg-gradient-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Review</h5>
                                <h2 class="mb-0"><?= $stats['reviews'] ?></h2>
                                <small><?= $stats['pending_reviews'] ?> pending</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-white bg-gradient-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Laporan</h5>
                                <h2 class="mb-0"><?= $stats['reports'] ?></h2>
                                <small><?= $stats['pending_reports'] ?> pending</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-flag"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card text-dark bg-gradient-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Aksi Cepat</h5>
                                <div class="mt-2">
                                    <a href="contents.php?action=add" class="btn btn-sm btn-light me-2">
                                        <i class="fas fa-plus"></i> Konten
                                    </a>
                                    <a href="reviews.php" class="btn btn-sm btn-light">
                                        <i class="fas fa-check"></i> Review
                                    </a>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Konten Terbaru -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Konten Terbaru</h5>
                        <a href="contents.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_contents as $content): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($content['title']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($content['category']) ?></span></td>
                                            <td><?= date('d/m/Y', strtotime($content['created_at'])) ?></td>
                                            <td>
                                                <a href="contents.php?action=edit&id=<?= $content['id'] ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Review Terbaru -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Review Terbaru</h5>
                        <a href="reviews.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_reviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($review['name']) ?></h6>
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="small mb-0"><?= htmlspecialchars(substr($review['review'], 0, 100)) ?>...</p>
                                    </div>
                                    <span class="badge <?= $review['status'] == 'approved' ? 'badge-approved' : 'badge-pending' ?>">
                                        <?= $review['status'] ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>