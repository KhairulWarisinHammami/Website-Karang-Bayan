<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Clear remember cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit;
?>