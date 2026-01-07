<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

$conn = connectDB();

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    switch ($action) {
        case 'process':
            $stmt = $conn->prepare("UPDATE reports SET status = 'processed' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Laporan berhasil ditandai sebagai diproses!";
            }
            $stmt->close();
            break;
            
        case 'resolve':
            $stmt = $conn->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Laporan berhasil ditandai sebagai selesai!";
            }
            $stmt->close();
            break;
            
        case 'reject':
            $stmt = $conn->prepare("UPDATE reports SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Laporan berhasil ditandai sebagai ditolak!";
            }
            $stmt->close();
            break;
            
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Laporan berhasil dihapus!";
            }
            $stmt->close();
            break;
    }
    
    header("Location: reports.php");
    exit;
}

// Ambil semua laporan dengan filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query untuk mengambil data laporan
if ($status_filter == 'all') {
    $stmt = $conn->prepare("SELECT * FROM reports ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung statistik
$stats = [
    'total' => 0,
    'pending' => 0,
    'processed' => 0,
    'resolved' => 0,
    'rejected' => 0
];

$stats_stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM reports GROUP BY status");
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();

while ($row = $stats_result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

$stats_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan - Admin Desa Karang Bayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c5530 0%, #4a8c4f 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            padding: 20px 0;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-processed {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-resolved {
            background-color: #28a745;
            color: white;
        }
        
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        
        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .report-type-badge {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 10px;
        }
        
        .type-infrastruktur { background-color: #e7f3e9; color: #2c5530; }
        .type-kebersihan { background-color: #d1ecf1; color: #0c5460; }
        .type-keamanan { background-color: #f8d7da; color: #721c24; }
        .type-administrasi { background-color: #fff3cd; color: #856404; }
        .type-lainnya { background-color: #e2e3e5; color: #383d41; }
        
        .table-hover tbody tr:hover {
            background-color: rgba(44, 85, 48, 0.05);
        }
        
        .btn-action {
            padding: 5px 10px;
            font-size: 0.875rem;
            margin-right: 5px;
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
            
            .stats-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4 class="mb-3">
                <i class="fas fa-tree me-2"></i>
                Desa Karang Bayan
            </h4>
            <p class="small mb-0">Admin Panel</p>
        </div>
        
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link text-white">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="reviews.php" class="nav-link text-white">
                <i class="fas fa-star me-2"></i> Kelola Review
            </a>
            <a href="reports.php" class="nav-link text-white active" style="background-color: rgba(255,255,255,0.1);">
                <i class="fas fa-flag me-2"></i> Kelola Laporan
            </a>
            <a href="settings.php" class="nav-link text-white">
                <i class="fas fa-cog me-2"></i> Pengaturan
            </a>
            <hr class="text-white-50 mx-3">
            <a href="logout.php" class="nav-link text-white">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </nav>
        
        <div class="position-absolute bottom-0 start-0 w-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 40px; height: 40px;">
                    <i class="fas fa-user"></i>
                </div>
                <div class="ms-2">
                    <small class="d-block"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></small>
                    <small class="text-white-50">Administrator</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Kelola Laporan</h1>
                <p class="text-muted mb-0">Kelola dan tinjau laporan dari pengunjung</p>
            </div>
            <div>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
        
        <!-- Status Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                        <a href="reports.php?status=all" class="btn btn-outline-primary <?= $status_filter == 'all' ? 'active' : '' ?>">Semua</a>
                        <a href="reports.php?status=pending" class="btn btn-outline-warning <?= $status_filter == 'pending' ? 'active' : '' ?>">Pending</a>
                        <a href="reports.php?status=processed" class="btn btn-outline-info <?= $status_filter == 'processed' ? 'active' : '' ?>">Diproses</a>
                        <a href="reports.php?status=resolved" class="btn btn-outline-success <?= $status_filter == 'resolved' ? 'active' : '' ?>">Selesai</a>
                        <a href="reports.php?status=rejected" class="btn btn-outline-danger <?= $status_filter == 'rejected' ? 'active' : '' ?>">Ditolak</a>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Total: <?= $stats['total'] ?> laporan</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body text-center">
                        <div class="stats-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <h3><?= $stats['total'] ?></h3>
                        <p class="mb-0">Total Laporan</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card stats-card bg-warning">
                    <div class="card-body text-center">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3><?= $stats['pending'] ?></h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card stats-card text-white bg-info">
                    <div class="card-body text-center">
                        <div class="stats-icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <h3><?= $stats['processed'] ?></h3>
                        <p class="mb-0">Diproses</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card stats-card text-white bg-success">
                    <div class="card-body text-center">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3><?= $stats['resolved'] ?></h3>
                        <p class="mb-0">Selesai</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card stats-card text-white bg-danger">
                    <div class="card-body text-center">
                        <div class="stats-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h3><?= $stats['rejected'] ?></h3>
                        <p class="mb-0">Ditolak</p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Reports Table -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (empty($reports)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                        <h5>Belum ada laporan</h5>
                        <p class="text-muted">Laporan yang masuk akan muncul di sini</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pelapor</th>
                                    <th>Email</th>
                                    <th>Jenis Laporan</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?= $report['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($report['name']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($report['email'] ?? '-') ?></td>
                                        <td>
                                            <span class="report-type-badge type-<?= $report['report_type'] ?>">
                                                <?= ucfirst($report['report_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reportModal"
                                                    data-description="<?= htmlspecialchars($report['description']) ?>"
                                                    data-name="<?= htmlspecialchars($report['name']) ?>"
                                                    data-type="<?= $report['report_type'] ?>"
                                                    data-date="<?= date('d/m/Y H:i', strtotime($report['created_at'])) ?>">
                                                Lihat
                                            </button>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                        <td>
                                            <?php 
                                            $badge_class = 'badge-' . $report['status'];
                                            $status_text = '';
                                            switch ($report['status']) {
                                                case 'pending':
                                                    $status_text = 'Pending';
                                                    break;
                                                case 'processed':
                                                    $status_text = 'Diproses';
                                                    break;
                                                case 'resolved':
                                                    $status_text = 'Selesai';
                                                    break;
                                                case 'rejected':
                                                    $status_text = 'Ditolak';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= $status_text ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if ($report['status'] == 'pending'): ?>
                                                    <a href="reports.php?action=process&id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-info btn-action" 
                                                       title="Tandai Diproses"
                                                       onclick="return confirm('Tandai laporan ini sebagai diproses?')">
                                                        <i class="fas fa-spinner"></i>
                                                    </a>
                                                    <a href="reports.php?action=resolve&id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-success btn-action" 
                                                       title="Tandai Selesai"
                                                       onclick="return confirm('Tandai laporan ini sebagai selesai?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="reports.php?action=reject&id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-danger btn-action" 
                                                       title="Tandai Ditolak"
                                                       onclick="return confirm('Tandai laporan ini sebagai ditolak?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php elseif ($report['status'] == 'processed'): ?>
                                                    <a href="reports.php?action=resolve&id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-success btn-action" 
                                                       title="Tandai Selesai"
                                                       onclick="return confirm('Tandai laporan ini sebagai selesai?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="reports.php?action=reject&id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-danger btn-action" 
                                                       title="Tandai Ditolak"
                                                       onclick="return confirm('Tandai laporan ini sebagai ditolak?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <?php if ($report['status'] == 'rejected'): ?>
                                                        <a href="reports.php?action=process&id=<?= $report['id'] ?>" 
                                                           class="btn btn-sm btn-info btn-action" 
                                                           title="Ubah ke Diproses"
                                                           onclick="return confirm('Ubah status laporan ini menjadi diproses?')">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <!-- Delete Button -->
                                                <a href="reports.php?action=delete&id=<?= $report['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-action" 
                                                   title="Hapus"
                                                   onclick="return confirm('Hapus laporan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3 text-end">
            <small class="text-muted">
                Menampilkan <?= count($reports) ?> dari <?= $stats['total'] ?> laporan
            </small>
        </div>
    </div>
    
    <!-- Modal untuk melihat detail laporan -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Nama Pelapor:</strong>
                        <p id="modalReportName" class="mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <strong>Jenis Laporan:</strong>
                        <p id="modalReportType" class="mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <strong>Tanggal:</strong>
                        <p id="modalReportDate" class="mb-0"></p>
                    </div>
                    <div>
                        <strong>Deskripsi Laporan:</strong>
                        <p id="modalReportDescription" class="mb-0" style="white-space: pre-line;"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal untuk melihat detail laporan
        const reportModal = document.getElementById('reportModal');
        if (reportModal) {
            reportModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const description = button.getAttribute('data-description');
                const name = button.getAttribute('data-name');
                const type = button.getAttribute('data-type');
                const date = button.getAttribute('data-date');
                
                document.getElementById('modalReportName').textContent = name;
                document.getElementById('modalReportType').textContent = type;
                document.getElementById('modalReportDate').textContent = date;
                document.getElementById('modalReportDescription').textContent = description;
            });
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Add print functionality
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>