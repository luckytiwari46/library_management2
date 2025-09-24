<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Reports';
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
            <h2><i class="fas fa-chart-bar"></i> Available Reports</h2>
            <a href="<?php echo $_SESSION['user_type'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Home
            </a>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Master List of Books</h5>
                        <p class="card-text">View complete list of all books in the library</p>
                        <a href="report_master_books.php" class="btn btn-primary">
                            <i class="fas fa-book"></i> View Books
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-film fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Master List of Movies</h5>
                        <p class="card-text">View complete list of all movies in the library</p>
                        <a href="report_master_movies.php" class="btn btn-success">
                            <i class="fas fa-film"></i> View Movies
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Master List of Memberships</h5>
                        <p class="card-text">View complete list of all library memberships</p>
                        <a href="report_master_memberships.php" class="btn btn-info">
                            <i class="fas fa-users"></i> View Memberships
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Active Issues</h5>
                        <p class="card-text">View all currently active book/movie issues</p>
                        <a href="report_active_issues.php" class="btn btn-warning">
                            <i class="fas fa-hand-holding"></i> View Active Issues
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Overdue Returns</h5>
                        <p class="card-text">View all overdue book/movie returns with fine calculations</p>
                        <a href="report_overdue_returns.php" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle"></i> View Overdue Returns
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x text-secondary mb-3"></i>
                        <h5 class="card-title">Issue Requests</h5>
                        <p class="card-text">View all pending and fulfilled issue requests</p>
                        <a href="report_issue_requests.php" class="btn btn-secondary">
                            <i class="fas fa-clock"></i> View Issue Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Note: If logged in as Admin - home will take to Admin Home Page<br>
                If logged in as user - home will take to User Home Page
            </small>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
