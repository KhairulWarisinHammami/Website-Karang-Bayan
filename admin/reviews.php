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
        case 'approve':
            $stmt = $conn->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Review berhasil disetujui!";
            }
            $stmt->close();
            break;
            
        case 'reject':
            $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Review berhasil dihapus!";
            }
            $stmt->close();
            break;
    }
    
    header("Location: reviews.php");
    exit;
}

// Ambil semua review
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
if ($status_filter == 'all') {
    $stmt = $conn->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Review - Admin Desa Karang Bayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .badge-pending {
            background-color: #f59e0b;
        }
        
        .badge-approved {
            background-color: #10b981;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Kelola Review</h1>
            <div class="btn-group">
                <a href="reviews.php?status=all" class="btn btn-outline-primary <?= $status_filter == 'all' ? 'active' : '' ?>">Semua</a>
                <a href="reviews.php?status=pending" class="btn btn-outline-warning <?= $status_filter == 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="reviews.php?status=approved" class="btn btn-outline-success <?= $status_filter == 'approved' ? 'active' : '' ?>">Disetujui</a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-body">
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h5>Belum ada review</h5>
                        <p class="text-muted">Review yang masuk akan muncul di sini</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($review['name']) ?></td>
                                        <td><?= htmlspecialchars($review['email'] ?? '-') ?></td>
                                        <td>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-secondary' ?>"></i>
                                            <?php endfor; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reviewModal"
                                                    data-review="<?= htmlspecialchars($review['review']) ?>">
                                                Lihat
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge <?= $review['status'] == 'approved' ? 'badge-approved' : 'badge-pending' ?>">
                                                <?= $review['status'] == 'approved' ? 'Disetujui' : 'Pending' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($review['created_at'])) ?></td>
                                        <td>
                                            <?php if ($review['status'] == 'pending'): ?>
                                                <a href="reviews.php?action=approve&id=<?= $review['id'] ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   onclick="return confirm('Setujui review ini?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="reviews.php?action=reject&id=<?= $review['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Hapus review ini?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="reviews.php?action=reject&id=<?= $review['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Hapus review ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
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
                Total: <?= count($reviews) ?> review
            </small>
        </div>
    </div>
    
    <!-- Modal untuk melihat review lengkap -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modalReviewText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal untuk melihat review
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const reviewText = button.getAttribute('data-review');
                document.getElementById('modalReviewText').textContent = reviewText;
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
    </script>
</body>
</html>