<?php
require_once 'includes/auth.php';

// If user is already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: user_dashboard.php');
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = $_POST['user_type'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    
    // Validation
    if (empty($username) || empty($password) || empty($confirm_password) || 
        empty($user_type) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = 'Please enter a valid email address';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists. Please choose a different username.';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, password, user_type, full_name, email) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$username, $hashed_password, $user_type, $full_name, $email]);
                
                $success = 'Account created successfully! You can now login.';
                
                // Clear form
                $_POST = [];
            }
        } catch (Exception $e) {
            $error = "Error creating account: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signup-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .signup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .signup-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .signup-body {
            padding: 40px 30px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-signup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .chart-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .login-link {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Chart Link -->
    <a href="chart.html" class="btn btn-outline-light chart-link">
        <i class="fas fa-chart-line"></i> Chart
    </a>

    <!-- Login Link -->
    <a href="index.php" class="btn btn-outline-light login-link">
        <i class="fas fa-sign-in-alt"></i> Login
    </a>

    <div class="signup-container">
        <div class="signup-header">
            <h2><i class="fas fa-user-plus"></i> Create Account</h2>
        </div>
        <div class="signup-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    <br><br>
                    <a href="index.php" class="btn btn-success btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <input type="hidden" name="user_type" value="user">
                    
                    <button type="submit" class="btn btn-primary btn-signup w-100">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Already have an account? <a href="index.php" class="text-decoration-none">Login here</a>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Password confirmation validation
            $('#confirm_password').on('input', function() {
                var password = $('#password').val();
                var confirmPassword = $(this).val();
                
                if (password !== confirmPassword) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Passwords do not match</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            $('#password').on('input', function() {
                var password = $(this).val();
                var confirmPassword = $('#confirm_password').val();
                
                if (password !== confirmPassword && confirmPassword !== '') {
                    $('#confirm_password').addClass('is-invalid');
                    if (!$('#confirm_password').next('.invalid-feedback').length) {
                        $('#confirm_password').after('<div class="invalid-feedback">Passwords do not match</div>');
                    }
                } else {
                    $('#confirm_password').removeClass('is-invalid');
                    $('#confirm_password').next('.invalid-feedback').remove();
                }
            });
            
            // Username validation
            $('#username').on('input', function() {
                var username = $(this).val();
                if (username.length < 3) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Username must be at least 3 characters</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            // Password strength validation
            $('#password').on('input', function() {
                var password = $(this).val();
                if (password.length < 6) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Password must be at least 6 characters</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
        });
    </script>
</body>
</html>
