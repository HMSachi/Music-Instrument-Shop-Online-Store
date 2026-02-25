<?php
if (!isset($page_title)) {
    $page_title = 'Melody Masters - Music Instrument Shop';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="csrf-token" content="<?php echo generateCsrfToken(); ?>">
    
    <!-- Modern Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/modern-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        const SITE_URL = '<?php echo SITE_URL; ?>';
    </script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-music"></i>
                        <span>Melody Masters</span>
                    </a>
                </div>
                
                <button class="menu-toggle" aria-label="Toggle Navigation">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php" <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'class="active"' : ''; ?>>Shop</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/index.php#categories">Categories</a></li>
                        <li><a href="#">About Us</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <a href="<?php echo SITE_URL; ?>/cart.php" class="cart-icon" title="View Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <?php 
                        $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                        if ($cart_count > 0): 
                        ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <div class="user-menu">
                        <?php if (isLoggedIn()): ?>
                            <span class="user-name">Hi, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                            <?php if (isAdmin()): ?>
                                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-sm btn-primary">Admin</a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="btn btn-sm btn-primary">Dashboard</a>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-sm btn-logout">Logout</a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-sm btn-login">Login</a>
                            <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-register">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container flash-messages">
            <?php
            $success_msg = getFlashMessage('success');
            $error_msg = getFlashMessage('error');
            $info_msg = getFlashMessage('info');
            
            if ($success_msg): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($info_msg): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <?php echo $info_msg; ?>
                </div>
            <?php endif; ?>
        </div>
