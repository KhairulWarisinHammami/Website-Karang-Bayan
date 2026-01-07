<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireAdminLogin();

$conn = connectDB();

$success = '';
$error = '';

// Handle form submission (Add/Edit Content)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category = sanitize($_POST['category']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if (empty($title) || empty($content) || empty($category)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Handle image upload
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload = uploadFile($_FILES['image'], 'image');
            if ($upload['success']) {
                $image_name = $upload['filename'];
                
                // Delete old image if editing
                if ($id > 0) {
                    $stmt = $conn->prepare("SELECT image FROM contents WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc() && !empty($row['image'])) {
                        deleteFile($row['image'], 'image');
                    }
                    $stmt->close();
                }
            } else {
                $error = "Gagal upload gambar: " . $upload['error'];
            }
        }
        
        // Handle video upload
        $video_name = '';
        if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
            $upload = uploadFile($_FILES['video'], 'video');
            if ($upload['success']) {
                $video_name = $upload['filename'];
                
                // Delete old video if editing
                if ($id > 0) {
                    $stmt = $conn->prepare("SELECT video FROM contents WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc() && !empty($row['video'])) {
                        deleteFile($row['video'], 'video');
                    }
                    $stmt->close();
                }
            } else {
                $error = "Gagal upload video: " . $upload['error'];
            }
        }
        
        if (empty($error)) {
            if ($id > 0) {
                // Update existing content
                if (!empty($image_name) && !empty($video_name)) {
                    $stmt = $conn->prepare("UPDATE contents SET title = ?, content = ?, category = ?, image = ?, video = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("sssssi", $title, $content, $category, $image_name, $video_name, $id);
                } elseif (!empty($image_name)) {
                    $stmt = $conn->prepare("UPDATE contents SET title = ?, content = ?, category = ?, image = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("ssssi", $title, $content, $category, $image_name, $id);
                } elseif (!empty($video_name)) {
                    $stmt = $conn->prepare("UPDATE contents SET title = ?, content = ?, category = ?, video = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("ssssi", $title, $content, $category, $video_name, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE contents SET title = ?, content = ?, category = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("sssi", $title, $content, $category, $id);
                }
            } else {
                // Insert new content
                if (!empty($image_name) && !empty($video_name)) {
                    $stmt = $conn->prepare("INSERT INTO contents (title, content, category, image, video) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $title, $content, $category, $image_name, $video_name);
                } elseif (!empty($image_name)) {
                    $stmt = $conn->prepare("INSERT INTO contents (title, content, category, image) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $title, $content, $category, $image_name);
                } elseif (!empty($video_name)) {
                    $stmt = $conn->prepare("INSERT INTO contents (title, content, category, video) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $title, $content, $category, $video_name);
                } else {
                    $stmt = $conn->prepare("INSERT INTO contents (title, content, category) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $title, $content, $category);
                }
            }
            
            if ($stmt->execute()) {
                $success = $id > 0 ? "Konten berhasil diperbarui!" : "Konten berhasil ditambahkan!";
                $_POST = []; // Clear form
            } else {
                $error = "Gagal menyimpan konten: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get content details
    $stmt = $conn->prepare("SELECT image, video FROM contents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($content = $result->fetch_assoc()) {
        // Delete files
        if (!empty($content['image'])) {
            deleteFile($content['image'], 'image');
        }
        if (!empty($content['video'])) {
            deleteFile($content['video'], 'video');
        }
        
        // Delete from database
        $stmt2 = $conn->prepare("DELETE FROM contents WHERE id = ?");
        $stmt2->bind_param("i", $id);
        if ($stmt2->execute()) {
            $success = "Konten berhasil dihapus!";
        } else {
            $error = "Gagal menghapus konten: " . $conn->error;
        }
        $stmt2->close();
    }
    $stmt->close();
    
    header("Location: contents.php");
    exit;
}

// Get content for editing
$edit_content = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM contents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_content = $result->fetch_assoc();
    $stmt->close();
}

// Get all contents
$contents = [];
$stmt = $conn->prepare("SELECT * FROM contents ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $contents[] = $row;
}
$stmt->close();

$conn->close();

// Define available categories
$categories = [
    'wisata' => 'Wisata Alam',
    'budaya' => 'Budaya & Tradisi',
    'kuliner' => 'Kuliner Khas',
    'sejarah' => 'Sejarah Desa',
    'ekonomi' => 'Ekonomi & UMKM',
    'pendidikan' => 'Pendidikan',
    'kesehatan' => 'Kesehatan',
    'infrastruktur' => 'Infrastruktur'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konten - Admin Desa Karang Bayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c5530;
            --secondary: #4a8c4f;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
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
        
        .table th {
            background-color: #f1f8e9;
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .badge-category {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-wisata { background-color: #e7f3e9; color: var(--primary); }
        .badge-budaya { background-color: #d1ecf1; color: #0c5460; }
        .badge-kuliner { background-color: #f8d7da; color: #721c24; }
        .badge-sejarah { background-color: #fff3cd; color: #856404; }
        .badge-ekonomi { background-color: #d4edda; color: #155724; }
        .badge-pendidikan { background-color: #cce5ff; color: #004085; }
        .badge-kesehatan { background-color: #e2e3e5; color: #383d41; }
        .badge-infrastruktur { background-color: #d1ecf1; color: #0c5460; }
        
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        
        .video-icon {
            width: 80px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(44, 85, 48, 0.25);
        }
        
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload:hover {
            border-color: var(--primary);
            background: #e9f5ea;
        }
        
        .file-upload i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .file-preview {
            margin-top: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .preview-video {
            width: 100%;
            height: 150px;
            border-radius: 5px;
            margin-bottom: 10px;
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
                    <a href="contents.php" class="nav-link active">
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
            <div>
                <h1 class="h3 mb-0">Kelola Konten</h1>
                <p class="text-muted mb-0">Tambah, edit, dan kelola konten untuk ditampilkan di website</p>
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
        
        <!-- Add/Edit Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    <?= $edit_content ? 'Edit Konten' : 'Tambah Konten Baru' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data" id="contentForm">
                    <?php if ($edit_content): ?>
                        <input type="hidden" name="id" value="<?= $edit_content['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Konten *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= $edit_content ? htmlspecialchars($edit_content['title']) : (isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '') ?>"
                                       required placeholder="Masukkan judul konten">
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $key => $name): ?>
                                        <option value="<?= $key ?>" 
                                            <?= ($edit_content && $edit_content['category'] == $key) || (isset($_POST['category']) && $_POST['category'] == $key) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Isi Konten *</label>
                                <textarea class="form-control" id="content" name="content" rows="8" 
                                          required placeholder="Tulis isi konten disini..."><?= $edit_content ? htmlspecialchars($edit_content['content']) : (isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '') ?></textarea>
                                <div class="form-text">Gunakan HTML untuk formatting jika diperlukan</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label class="form-label">Gambar Thumbnail</label>
                                <div class="file-upload" id="imageUpload">
                                    <i class="fas fa-image"></i>
                                    <p class="mb-2">Klik untuk upload gambar</p>
                                    <small class="text-muted">Format: JPG, PNG, GIF (Max: 5MB)</small>
                                    <input type="file" class="d-none" id="imageInput" name="image" accept="image/*">
                                </div>
                                
                                <div id="imagePreview" class="file-preview">
                                    <?php if ($edit_content && !empty($edit_content['image'])): ?>
                                        <img src="../assets/uploads/images/<?= htmlspecialchars($edit_content['image']) ?>" 
                                             alt="Preview" class="preview-image">
                                        <small class="text-muted">Gambar saat ini</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Video Upload -->
                            <div class="mb-4">
                                <label class="form-label">Video (Opsional)</label>
                                <div class="file-upload" id="videoUpload">
                                    <i class="fas fa-video"></i>
                                    <p class="mb-2">Klik untuk upload video</p>
                                    <small class="text-muted">Format: MP4, AVI, MOV (Max: 50MB)</small>
                                    <input type="file" class="d-none" id="videoInput" name="video" accept="video/*">
                                </div>
                                
                                <div id="videoPreview" class="file-preview">
                                    <?php if ($edit_content && !empty($edit_content['video'])): ?>
                                        <video controls class="preview-video">
                                            <source src="../assets/uploads/videos/<?= htmlspecialchars($edit_content['video']) ?>" type="video/mp4">
                                        </video>
                                        <small class="text-muted">Video saat ini</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    <?= $edit_content ? 'Update Konten' : 'Simpan Konten' ?>
                                </button>
                                <?php if ($edit_content): ?>
                                    <a href="contents.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Batal Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Content List -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Konten
                    </h5>
                    <small class="text-muted">Total: <?= count($contents) ?> konten</small>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($contents)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5>Belum ada konten</h5>
                        <p class="text-muted">Mulai tambahkan konten pertama Anda</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Video</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contents as $index => $content): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <?php if (!empty($content['image'])): ?>
                                                <img src="../assets/uploads/images/<?= htmlspecialchars($content['image']) ?>" 
                                                     alt="Thumbnail" class="thumbnail">
                                            <?php else: ?>
                                                <div class="thumbnail bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($content['title']) ?></strong>
                                            <div class="small text-muted">
                                                <?= substr(strip_tags($content['content']), 0, 50) ?>...
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-category badge-<?= $content['category'] ?>">
                                                <?= htmlspecialchars($categories[$content['category']] ?? ucfirst($content['category'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($content['video'])): ?>
                                                <div class="video-icon">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($content['created_at'])) ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="contents.php?action=edit&id=<?= $content['id'] ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="contents.php?action=delete&id=<?= $content['id'] ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus konten ini?')"
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <a href="../konten.php?action=view&id=<?= $content['id'] ?>" 
                                                   target="_blank" class="btn btn-info" title="Lihat">
                                                    <i class="fas fa-eye"></i>
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload preview
        document.addEventListener('DOMContentLoaded', function() {
            // Image upload
            const imageUpload = document.getElementById('imageUpload');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            
            imageUpload.addEventListener('click', function() {
                imageInput.click();
            });
            
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="preview-image">
                            <small class="text-muted">Gambar baru akan menggantikan yang lama</small>
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Video upload
            const videoUpload = document.getElementById('videoUpload');
            const videoInput = document.getElementById('videoInput');
            const videoPreview = document.getElementById('videoPreview');
            
            videoUpload.addEventListener('click', function() {
                videoInput.click();
            });
            
            videoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        videoPreview.innerHTML = `
                            <video controls class="preview-video">
                                <source src="${e.target.result}" type="${file.type}">
                            </video>
                            <small class="text-muted">Video baru akan menggantikan yang lama</small>
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Form validation
            const contentForm = document.getElementById('contentForm');
            const titleInput = document.getElementById('title');
            const categorySelect = document.getElementById('category');
            const contentTextarea = document.getElementById('content');
            
            contentForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Title validation
                if (titleInput.value.trim().length < 5) {
                    isValid = false;
                    showAlert('Judul konten minimal 5 karakter!', 'danger');
                    titleInput.focus();
                }
                
                // Category validation
                if (!categorySelect.value) {
                    isValid = false;
                    showAlert('Pilih kategori konten!', 'danger');
                    categorySelect.focus();
                }
                
                // Content validation
                if (contentTextarea.value.trim().length < 20) {
                    isValid = false;
                    showAlert('Isi konten minimal 20 karakter!', 'danger');
                    contentTextarea.focus();
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
        
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert after header
            const header = document.querySelector('.main-content .d-flex.justify-content-between');
            header.parentNode.insertBefore(alertDiv, header.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>