<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if connection was successful
if (!$conn) {
    setFlashMessage('error', 'Database connection failed. Please check your configuration.');
    redirect('index.php');
}

$error = '';

// Check if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } elseif (isStaff()) {
        redirect('staff/dashboard.php');
    } else {
        redirect('customer/dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't sanitize password to preserve characters
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, full_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                setFlashMessage('success', 'Logged in successfully! Welcome back, ' . htmlspecialchars($user['full_name']));
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect('admin/dashboard.php');
                } elseif ($user['role'] === 'staff') {
                    redirect('staff/dashboard.php');
                } else {
                    redirect('customer/dashboard.php');
                }
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        
        $stmt->close();
    }
}

$page_title = 'Login - Melody Masters';
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box animate-fade-in-up">
        <h2>Welcome Back</h2>
        <p style="text-align: center; color: var(--text-light); margin-top: -2rem; margin-bottom: 3rem;">Sign in to continue to Melody Masters.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <?php echo csrfInput(); ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       placeholder="example@mail.com"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            <p>New to Melody Masters? <a href="register.php">Create Account</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
