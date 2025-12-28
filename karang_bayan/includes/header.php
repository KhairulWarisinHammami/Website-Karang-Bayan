<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Desa Karang Bayan' : 'Desa Karang Bayan'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Styles yang sebelumnya ada di bagian header */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background-color: #2c5530;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.2rem;
            color: #ffd166;
        }
        
        .logo-text h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo-text p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffd166;
        }
        
        nav a.active {
            color: #ffd166;
            border-bottom: 2px solid #ffd166;
        }
        
        /* Responsive Header */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .logo {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
        
        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
            nav ul {
                display: none;
                flex-direction: column;
                width: 100%;
                text-align: center;
            }
            
            nav ul.show {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="logo-text">
                    <h1>Desa Karang Bayan</h1>
                    <p>Desa Wisata Budaya & Alam</p>
                </div>
            </div>
            
            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav>
                <ul id="navMenu">
                    <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Beranda</a></li>
                    <li><a href="tentang.php" <?php echo basename($_SERVER['PHP_SELF']) == 'tentang.php' ? 'class="active"' : ''; ?>>Tentang Desa</a></li>
                    <li><a href="laporan.php" <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'class="active"' : ''; ?>>Laporan</a></li>
                    <li><a href="konten.php" <?php echo basename($_SERVER['PHP_SELF']) == 'konten.php' ? 'class="active"' : ''; ?>>Konten</a></li>
                    <li><a href="review.php" <?php echo basename($_SERVER['PHP_SELF']) == 'review.php' ? 'class="active"' : ''; ?>>Review</a></li>
                    <li><a href="kontak.php" <?php echo basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'class="active"' : ''; ?>>Kontak</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>