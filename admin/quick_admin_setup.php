<?php
// quick_admin_setup.php - Setup admin dengan satu klik

// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'desa_karang_bayan';

$conn = new mysqli($host, $user, $pass);

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbname);

// Buat tabel admin
$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Buat admin dengan password: Admin123!
$username = 'admin';
$password = 'Admin123!';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert atau update admin
$sql = "INSERT INTO admin (username, password) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE password = VALUES(password)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hashed_password);
$stmt->execute();

echo "<h2>Admin Setup Selesai!</h2>";
echo "<p><strong>Username:</strong> $username</p>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><a href='admin/login.php'>Login ke Admin Panel</a></p>";

$stmt->close();
$conn->close();
?>