<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Admin Dashboard';
include 'includes/header.php';

// Get statistics
$stats = [];
try {
    // Total books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM books_movies");
    $stats['total_books_movies'] = $stmt->fetch()['total'];
    
    // Available books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM books_movies WHERE status = 'Available'");
    $stats['available'] = $stmt->fetch()['total'];
    
    // Issued books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_issues WHERE status = 'Active'");
    $stats['issued'] = $stmt->fetch()['total'];
    
    // Total members
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM memberships WHERE status = 'Active'");
    $stats['active_members'] = $stmt->fetch()['total'];
    
    // Overdue returns
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_issues WHERE status = 'Active' AND expected_return_date < CURDATE()");
    $stats['overdue'] = $stmt->fetch()['total'];
    
    // Pending requests
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM issue_requests WHERE status = 'Pending'");
    $stats['pending_requests'] = $stmt->fetch()['total'];
    
    // Total system users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Recent signups (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['recent_signups'] = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    $error = "Error loading statistics: " . $e->getMessage();
}

// Get recent signups data
$recent_signups = [];
try {
    $stmt = $pdo->query("
        SELECT username, full_name, user_type, email, created_at 
        FROM users 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $recent_signups = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading signup data: " . $e->getMessage();
}
?>

<?php
// Include the sidebar
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <a href="user_dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <a href="report_master_books.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-book fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Total Books/Movies</h5>
                            <h3 class="text-primary"><?php echo $stats['total_books_movies'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="book_availability.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Available</h5>
                            <h3 class="text-success"><?php echo $stats['available'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="report_active_issues.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-hand-holding fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">Issued</h5>
                            <h3 class="text-warning"><?php echo $stats['issued'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <a href="report_master_memberships.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-info mb-2"></i>
                            <h5 class="card-title">Active Members</h5>
                            <h3 class="text-info"><?php echo $stats['active_members'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="report_overdue_returns.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">Overdue Returns</h5>
                            <h3 class="text-danger"><?php echo $stats['overdue'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="report_issue_requests.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-secondary mb-2"></i>
                            <h5 class="card-title">Pending Requests</h5>
                            <h3 class="text-secondary"><?php echo $stats['pending_requests'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- User Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-5 mb-3">
                <a href="maintenance_add_user.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Total System Users</h5>
                            <h3 class="text-primary"><?php echo $stats['total_users'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-5 mb-3">
                <a href="user_signups.php" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-plus fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Recent Signups (30 days)</h5>
                            <h3 class="text-success"><?php echo $stats['recent_signups'] ?? 0; ?></h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Signups -->
        <div class="card mb-4" style="width:80%" >
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Recent Signups</h5>
                <a href="user_signups.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-users"></i> View All Signup Details
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive " >
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>User Type</th>
                                <th>Email</th>
                                <th>Signup Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_signups)): ?>
                                <?php foreach ($recent_signups as $user): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td>
                                            <?php if ($user['user_type'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($user['email'])): ?>
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not provided</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($user['user_type'] === 'admin'): ?>
                                                <span class="badge bg-warning">System Admin</span>
                                            <?php else: ?>
                                                <a href="maintenance_update_user.php?username=<?php echo urlencode($user['username']); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Manage
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> No users found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <a href="maintenance_add_user.php" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button class="btn btn-info" onclick="exportSignupsToExcel()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="card" style="width:80%">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Product Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Code No From</th>
                                <th>Code No To</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SC(B/M)000001</td>
                                <td>SC(B/M)000004</td>
                                <td>Science</td>
                            </tr>
                            <tr>
                                <td>EC(B/M)000001</td>
                                <td>EC(B/M)000004</td>
                                <td>Economics</td>
                            </tr>
                            <tr>
                                <td>FC(B/M)000001</td>
                                <td>FC(B/M)000004</td>
                                <td>Fiction</td>
                            </tr>
                            <tr>
                                <td>CH(B/M)000001</td>
                                <td>CH(B/M)000004</td>
                                <td>Children</td>
                            </tr>
                            <tr>
                                <td>PD(B/M)000001</td>
                                <td>PD(B/M)000004</td>
                                <td>Personal Development</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportSignupsToExcel() {
    // Get the signups table data
    let table = document.querySelector('.card .table');
    let wb = XLSX.utils.table_to_book(table, {sheet: "Recent Signups"});
    XLSX.writeFile(wb, "recent_signups.xlsx");
}
</script>

<?php include 'includes/footer.php'; ?>
