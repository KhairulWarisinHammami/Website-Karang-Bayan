<?php
// includes/functions.php
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }
    return $conn;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . SITE_URL . "admin/login.php");
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function uploadFile($file, $type = 'image') {
    $target_dir = dirname(__DIR__) . "/assets/uploads/";
    
    if ($type == 'image') {
        $target_dir .= "images/";
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    } else {
        $target_dir .= "videos/";
        $allowed = ['mp4', 'avi', 'mov', 'wmv'];
    }
    
    // Buat folder jika belum ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed)) {
        return ['success' => false, 'error' => 'Format file tidak diizinkan. Format yang diizinkan: ' . implode(', ', $allowed)];
    }
    
    // Validasi ukuran file
    if ($file['size'] > ($type == 'image' ? 5 * 1024 * 1024 : 50 * 1024 * 1024)) { // 5MB untuk gambar, 50MB untuk video
        return ['success' => false, 'error' => 'Ukuran file terlalu besar'];
    }
    
    $new_filename = uniqid() . '_' . date('Ymd_His') . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    }
    
    return ['success' => false, 'error' => 'Gagal mengupload file'];
}

function deleteFile($filename, $type = 'image') {
    $path = dirname(__DIR__) . "/assets/uploads/";
    $path .= ($type == 'image' ? 'images/' : 'videos/') . $filename;
    
    if (file_exists($path) && is_file($path)) {
        return unlink($path);
    }
    return false;
}
?>

