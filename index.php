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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        if (login($username, $password)) {
            if ($_SESSION['user_type'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .header p {
            margin-top: 10px;
            opacity: 0.85;
        }
        .content {
            display: flex;
            flex-wrap: wrap;
        }
        .welcome-section {
            flex: 1;
            padding: 40px 30px;
            min-width: 300px;
            background-color: #ffffff;
        }
        .login-section {
            flex: 1;
            padding: 40px 30px;
            background: #f8f9fa;
            min-width: 300px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            background-color: white;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .feature-item {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }
        .feature-item:hover {
            transform: translateX(5px);
        }
        .feature-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            font-size: 18px;
        }
        .chart-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Chart Link -->
    <a href="chart.html" class="btn btn-outline-light chart-link shadow">
        <i class="fas fa-chart-line"></i> Chart
    </a>

    <div class="main-container">
        <div class="header">
            <h1><i class="fas fa-book-open"></i> Library Management System</h1>
            <p>Your gateway to knowledge and entertainment</p>
        </div>
        
        <div class="content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h3 class="mb-4 text-primary fw-bold">Welcome to Our Library</h3>
                <p class="mb-4 text-muted">Discover a world of books, movies, and resources at your fingertips.</p>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">Extensive Collection</h5>
                        <p class="mb-0 text-muted">Access thousands of books and movies</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">24/7 Access</h5>
                        <p class="mb-0 text-muted">Browse and reserve items anytime</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">User-Friendly</h5>
                        <p class="mb-0 text-muted">Simple interface for all users</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="signup.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> Create New Account
                    </a>
                </div>
            </div>
            
            <!-- Login Section -->
            <div class="login-section">
                <h3 class="mb-4 text-center text-primary fw-bold"><i class="fas fa-sign-in-alt"></i> Login</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Don't have an account? <a href="signup.php" class="text-decoration-none fw-bold">Sign up here</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut();
            
            // Form validation
            $('form').on('submit', function(e) {
                let isValid = true;
                const requiredFields = $(this).find('[required]');
                
                requiredFields.each(function() {
                    if ($(this).val().trim() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    $('<div class="alert alert-danger mt-3">Please fill in all required fields.</div>')
                        .insertAfter($(this))
                        .delay(5000).fadeOut();
                }
            });
            
            // Remove validation classes on input
            $('input').on('input', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
</body>
</html>
