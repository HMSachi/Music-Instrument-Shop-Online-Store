<?php
if (!isset($page_title)) {
    $page_title = 'Melody Masters - Music Instrument Shop';
}

$is_admin_route = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false);
if ($is_admin_route) {
    if (!isset($body_class)) {
        $body_class = 'admin-mode';
    } else {
        $body_class .= ' admin-mode';
    }
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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/modern-theme.css?v=<?php echo time(); ?>">
    <?php if ($is_admin_route ?? false): ?>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin-theme.css?v=<?php echo time(); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        const SITE_URL = '<?php echo SITE_URL; ?>';
    </script>
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : ''; ?>">
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
        <!-- Floating Toasts -->
        <style>
            .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
            .toast-message { pointer-events: auto; background: var(--bg-card); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 16px 24px; display: flex; align-items: center; gap: 12px; font-weight: 500; transform: translateX(120%); animation: slideInX 0.4s forwards; border-left: 4px solid #ccc; background-color: rgba(255,255,255,0.95); }
            .toast-message.success { border-color: var(--success); color: var(--success); }
            .toast-message.error { border-color: var(--error); color: var(--error); }
            .toast-message.info { border-color: var(--info); color: var(--info); }
            @keyframes slideInX { 80% { transform: translateX(-15px); } 100% { transform: translateX(0); } }
            @keyframes slideOutFade { to { transform: translateY(-20px); opacity: 0; } }
            .toast-hide { animation: slideOutFade 0.4s forwards; }
            body.admin-mode .toast-message { background: var(--admin-bg-surface); color: white; -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); border: 1px solid var(--admin-border); border-left-width: 4px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        </style>
        <div class="toast-container" id="toastBox">
            <?php
            $success_msg = getFlashMessage('success');
            $error_msg = getFlashMessage('error');
            $info_msg = getFlashMessage('info');
            
            if ($success_msg): ?>
                <div class="toast-message success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="toast-message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($info_msg): ?>
                <div class="toast-message info">
                    <i class="fas fa-info-circle"></i> <?php echo $info_msg; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            setTimeout(() => {
                document.querySelectorAll('.toast-message').forEach(el => {
                    el.classList.add('toast-hide');
                    setTimeout(() => el.remove(), 400);
                });
            }, 5000);
        </script>
