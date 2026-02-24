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
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
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

<style>
    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border-left: 5px solid;
        font-size: 15px;
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .alert-error {
        background-color: #ffe6e6;
        color: #d32f2f;
        border-left-color: #d32f2f;
    }
    
    .alert-success {
        background-color: #e6ffe6;
        color: #2e7d32;
        border-left-color: #2e7d32;
    }
    
    .alert a {
        color: inherit;
        text-decoration: underline;
        font-weight: bold;
    }
    
    .alert a:hover {
        opacity: 0.8;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: all 0.3s;
        box-sizing: border-box;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-group .error-text {
        color: #d32f2f;
        font-size: 13px;
        margin-top: 5px;
    }
    
    .form-group small {
        color: #666;
        display: block;
        margin-top: 5px;
        font-size: 13px;
    }
    
    .btn-register {
        width: 100%;
        padding: 12px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .btn-register:hover {
        background-color: #2563eb;
    }
    
    .btn-register:active {
        transform: scale(0.98);
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .auth-footer a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }
    
    .auth-footer a:hover {
        text-decoration: underline;
    }
    
    .form-group [required]::after {
        content: ' *';
        color: #d32f2f;
    }
</style>

<div class="auth-container">
    <div class="auth-box">
        <h2><i class="fas fa-user-plus"></i> Create Your Account</h2>
        <p style="color: #666; margin-bottom: 20px;">Join Melody Masters and start shopping for musical instruments!</p>
        
        <!-- Display Error Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <div><?php echo $error; ?></div>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm" novalidate>
            <div class="form-group">
                <label for="full_name">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    required 
                    placeholder="John Doe"
                    minlength="3"
                    maxlength="100"
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                    autocomplete="name"
                >
                <small>Minimum 3 characters</small>
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="john@example.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    autocomplete="email"
                >
                <small>We'll never share your email with anyone</small>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    minlength="6"
                    placeholder="Minimum 6 characters"
                    autocomplete="new-password"
                >
                <small>At least 6 characters with letters (A-Z) and numbers (0-9)</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-check"></i> Confirm Password
                </label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required 
                    minlength="6"
                    placeholder="Re-enter your password"
                    autocomplete="new-password"
                >
            </div>
            
            <div class="form-group">
                <label for="phone">
                    <i class="fas fa-phone"></i> Phone (Optional)
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    placeholder="123-456-7890"
                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    autocomplete="tel"
                >
            </div>
            
            <div class="form-group">
                <label for="address">
                    <i class="fas fa-map-marker-alt"></i> Address (Optional)
                </label>
                <textarea 
                    id="address" 
                    name="address" 
                    rows="3"
                    placeholder="Your delivery address"
                    autocomplete="street-address"
                ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Create My Account
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">🔐 Login here</a></p>
            <p style="margin-top: 10px; font-size: 13px; color: #666;">
                By registering, you agree to our Terms of Service and Privacy Policy
            </p>
<?php include 'includes/footer.php'; ?>
