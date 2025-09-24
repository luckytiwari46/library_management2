<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Maintenance';
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
            <h2><i class="fas fa-tools"></i> Maintenance</h2>
            <a href="admin_dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="row">
            <!-- Housekeeping Section -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-broom"></i> Housekeeping</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-user-plus fa-2x text-success mb-3"></i>
                                        <h6 class="card-title">Membership - Add</h6>
                                        <p class="card-text">Add new membership</p>
                                        <a href="maintenance_add_membership.php" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Add Membership
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-user-edit fa-2x text-warning mb-3"></i>
                                        <h6 class="card-title">Membership - Update</h6>
                                        <p class="card-text">Update existing membership</p>
                                        <a href="maintenance_update_membership.php" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Update Membership
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-book fa-2x text-primary mb-3"></i>
                                        <h6 class="card-title">Books/Movies - Add</h6>
                                        <p class="card-text">Add new books or movies</p>
                                        <a href="maintenance_add_book.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add Book/Movie
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-edit fa-2x text-info mb-3"></i>
                                        <h6 class="card-title">Books/Movies - Update</h6>
                                        <p class="card-text">Update existing books or movies</p>
                                        <a href="maintenance_update_book.php" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> Update Book/Movie
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-user-plus fa-2x text-secondary mb-3"></i>
                                        <h6 class="card-title">User Management - Add</h6>
                                        <p class="card-text">Add new user</p>
                                        <a href="maintenance_add_user.php" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-plus"></i> Add User
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-user-cog fa-2x text-dark mb-3"></i>
                                        <h6 class="card-title">User Management - Update</h6>
                                        <p class="card-text">Update existing user</p>
                                        <a href="maintenance_update_user.php" class="btn btn-dark btn-sm">
                                            <i class="fas fa-edit"></i> Update User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
