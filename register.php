<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if connection was successful
if (!$conn) {
    die('Database connection failed. Please check your configuration.');
}

$error = '';
$success = '';
$errors = [];

// Check if already logged in
if (isLoggedIn()) {
    redirect('customer/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    
    // Validation - Collect ALL errors
    if (empty($full_name)) {
        $errors[] = '👤 Full name is required';
    } elseif (strlen($full_name) < 3) {
        $errors[] = '👤 Name must be at least 3 characters (you entered: ' . strlen($full_name) . ')';
    }
    
    if (empty($email)) {
        $errors[] = '📧 Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '📧 Invalid email format. Example: user@example.com';
    }
    
    if (empty($password)) {
        $errors[] = '🔐 Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = '🔐 Password must be at least 6 characters (you entered: ' . strlen($password) . ')';
    } elseif (!preg_match('/[A-Za-z]/', $password)) {
        $errors[] = '🔐 Password must contain at least one letter (A-Z or a-z)';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = '🔐 Password must contain at least one number (0-9)';
    }
    
    if (empty($confirm_password)) {
        $errors[] = '✓ Please confirm your password';
    } elseif ($password !== $confirm_password) {
        $errors[] = '✓ Passwords do not match. Please re-enter your password';
    }
    
    // If there are validation errors, display them
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                $error = 'Database error: ' . $stmt->error;
            } else {
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = '❌ Email already registered! This email is already in use. <a href="login.php">Login here</a> or use a different email.';
                } else {
                    $stmt->close();
                    
                    // Hash password using bcrypt (PASSWORD_DEFAULT = bcrypt)
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new user
                    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, phone, address, created_at) VALUES (?, ?, ?, 'customer', ?, ?, NOW())");
                    
                    if (!$stmt) {
                        $error = 'Database error: ' . $conn->error;
                    } else {
                        $stmt->bind_param("sssss", $full_name, $email, $hashed_password, $phone, $address);
                        
                        if ($stmt->execute()) {
                            // Store success message in session
                            $_SESSION['flash_message'] = '✅ Registration successful! Welcome, ' . $full_name . '! Please login with your email and password.';
                            
                            $stmt->close();
                            
                            // Redirect to login
                            header("Location: " . SITE_URL . "/login.php");
                            exit();
                        } else {
                            $error = '❌ Registration failed: ' . $stmt->error . '. Please try again later.';
                        }
                    }
                }
            }
            
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}

$page_title = 'Register - Melody Masters';
include 'includes/header.php';
?>



<div class="auth-container">
    <div class="auth-box animate-fade-in-up">
        <h2>Join Melody Masters</h2>
        <p style="text-align: center; color: var(--text-light); margin-top: -2rem; margin-bottom: 3rem;">Start your musical journey with us today.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <div><?php echo $error; ?></div>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm" novalidate>
            <?php echo csrfInput(); ?>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required placeholder="John Doe"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Min. 6 chars">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter">
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone (Optional)</label>
                <input type="tel" id="phone" name="phone" placeholder="+44 123 456 7890"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Mailing Address</label>
                <textarea id="address" name="address" rows="3" placeholder="Where should we ship your instruments?"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already a member? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
