<?php
include 'includes/db_connection.php';
include 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthHandler($db);
    $result = $auth->login($_POST['email'], $_POST['password']);
    
    if ($result['success']) {
        $redirect_url = '/index.php';
        
        if ($_SESSION['role'] === 'admin') {
            $redirect_url = '/admin/dashboard.php';
        } elseif ($_SESSION['role'] === 'staff') {
            $redirect_url = '/staff/dashboard.php';
        } elseif ($_SESSION['role'] === 'customer') {
            $redirect_url = '/customer/dashboard.php';
        }
        
        header("Location: $redirect_url");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
        </nav>
    </header>

    <main class="container">
        <div style="max-width: 500px; margin: 3rem auto;">
            <div class="card">
                <h1 class="text-center mb-3">Login to Your Account</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </form>
                
                <p class="text-center mt-3">
                    Don't have an account? <a href="/signup.php">Sign up here</a>
                </p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
