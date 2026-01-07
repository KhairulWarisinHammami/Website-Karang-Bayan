<?php
// debug_upload.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_once '../includes/functions.php';

echo "<h2>Debug Upload System</h2>";

// Cek folder permissions
$folders = [
    '../assets/uploads/',
    '../assets/uploads/images/',
    '../assets/uploads/videos/'
];

foreach ($folders as $folder) {
    if (is_dir($folder)) {
        echo "✓ Folder $folder ada<br>";
        echo "&nbsp;&nbsp;Permission: " . substr(sprintf('%o', fileperms($folder)), -4) . "<br>";
        echo "&nbsp;&nbsp;Writable: " . (is_writable($folder) ? 'Ya' : 'Tidak') . "<br>";
    } else {
        echo "✗ Folder $folder tidak ada<br>";
    }
}

// Cek php.ini settings
echo "<h3>PHP Settings</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

// Test form upload
echo "<h3>Test Upload Form</h3>";
?>
<form action="debug_upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file">
    <button type="submit">Test Upload</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['test_file'])) {
    echo "<h4>Upload Result:</h4>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    $upload = uploadFile($_FILES['test_file'], 'video');
    echo "<pre>";
    print_r($upload);
    echo "</pre>";
}