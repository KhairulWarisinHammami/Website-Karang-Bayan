<?php
// includes/config.php - HARUS dimulai dari baris pertama tanpa spasi apapun

// Aktifkan error reporting (opsional untuk development)
ini_set('display_errors', 0); // Disable untuk production
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/error_log.txt');

// Start session - HARUS di paling awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'desa_karang_bayan');

// Dapatkan path root website
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . "://" . $host . $script_path;
$base_url = rtrim($base_url, '/') . '/';

define('SITE_URL', $base_url);

// JANGAN ADA spasi atau karakter apapun setelah tag penutup PHP
// Lebih baik HAPUS tag penutup