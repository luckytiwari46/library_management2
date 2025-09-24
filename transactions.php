<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Transactions';
include 'includes/header.php';
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
            <h2><i class="fas fa-exchange-alt"></i> Transactions</h2>
            <a href="<?php echo $_SESSION['user_type'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Home
            </a>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Is book available?</h5>
                        <p class="card-text">Search for book availability and get detailed information</p>
                        <a href="book_availability.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Check Availability
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Issue book?</h5>
                        <p class="card-text">Issue books to members with automatic validation</p>
                        <a href="book_issue.php" class="btn btn-success">
                            <i class="fas fa-hand-holding"></i> Issue Book
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Return book?</h5>
                        <p class="card-text">Return books and calculate fines if applicable</p>
                        <a href="book_return.php" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Return Book
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Pay Fine?</h5>
                        <p class="card-text">Process fine payments for overdue returns</p>
                        <a href="pay_fine.php" class="btn btn-info">
                            <i class="fas fa-credit-card"></i> Pay Fine
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
