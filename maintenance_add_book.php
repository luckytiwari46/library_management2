<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Add Book/Movie';
include 'includes/header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $serial_no = trim($_POST['serial_no']);
    $name = trim($_POST['name']);
    $author = trim($_POST['author']);
    $category = $_POST['category'];
    $type = $_POST['type'];
    $cost = $_POST['cost'];
    $procurement_date = $_POST['procurement_date'];
    
    // Validation
    if (empty($serial_no) || empty($name) || empty($author) || empty($category) || 
        empty($type) || empty($cost) || empty($procurement_date)) {
        $error = 'All fields are mandatory';
    } else {
        try {
            // Check if serial number already exists
            $stmt = $pdo->prepare("SELECT id FROM books_movies WHERE serial_no = ?");
            $stmt->execute([$serial_no]);
            if ($stmt->fetch()) {
                $error = 'Serial number already exists';
            } else {
                // Insert book/movie
                $stmt = $pdo->prepare("
                    INSERT INTO books_movies (serial_no, name, author, category, type, cost, procurement_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$serial_no, $name, $author, $category, $type, $cost, $procurement_date]);
                
                $success = "Book/Movie added successfully";
                
                // Clear form
                $_POST = [];
            }
            
        } catch (Exception $e) {
            $error = "Error adding book/movie: " . $e->getMessage();
        }
    }
}
?>

<?php
// Define current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-2">
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($current_page === 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">
                <i class="fas fa-home"></i> Admin Home Page
            </a>
            <a class="nav-link <?php echo ($current_page === 'maintenance.php' || strpos($current_page, 'maintenance_') === 0) ? 'active' : ''; ?>" href="maintenance.php">
                <i class="fas fa-tools"></i> Maintenance
            </a>
            <a class="nav-link <?php echo ($current_page === 'reports.php' || strpos($current_page, 'report_') === 0) ? 'active' : ''; ?>" href="reports.php">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a class="nav-link <?php echo ($current_page === 'transactions.php' || strpos($current_page, 'book_') === 0 || $current_page === 'pay_fine.php') ? 'active' : ''; ?>" href="transactions.php">
                <i class="fas fa-exchange-alt"></i> Transactions
            </a>
            <a class="nav-link <?php echo ($current_page === 'user_dashboard.php') ? 'active' : ''; ?>" href="user_dashboard.php">
                <i class="fas fa-user"></i> User Home Page
            </a>
        </nav>
    </div>
</div>

<div class="col-md-10">
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-book"></i> Add Book/Movie</h2>
            <a href="maintenance.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Maintenance
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Add Book/Movie Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book"></i> Add Book/Movie</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="book" value="Book" 
                                           <?php echo (!isset($_POST['type']) || $_POST['type'] === 'Book') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="book">
                                        Book
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="movie" value="Movie"
                                           <?php echo (isset($_POST['type']) && $_POST['type'] === 'Movie') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="movie">
                                        Movie
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="serial_no" class="form-label">Serial No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" 
                                   value="<?php echo isset($_POST['serial_no']) ? htmlspecialchars($_POST['serial_no']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Author/Director <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Science" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Science') ? 'selected' : ''; ?>>Science</option>
                                <option value="Economics" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Economics') ? 'selected' : ''; ?>>Economics</option>
                                <option value="Fiction" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
                                <option value="Children" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Children') ? 'selected' : ''; ?>>Children</option>
                                <option value="Personal Development" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Personal Development') ? 'selected' : ''; ?>>Personal Development</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cost" class="form-label">Cost (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="cost" name="cost" 
                                   value="<?php echo isset($_POST['cost']) ? htmlspecialchars($_POST['cost']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="procurement_date" class="form-label">Procurement Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="procurement_date" name="procurement_date" 
                                   value="<?php echo isset($_POST['procurement_date']) ? $_POST['procurement_date'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="add_book" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Book/Movie
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Clear
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        All fields are required. By default, Book type is selected.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
