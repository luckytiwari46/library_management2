<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Issue Requests';
include 'includes/header.php';

// Get all issue requests
$issue_requests = [];
try {
    $stmt = $pdo->query("
        SELECT ir.*, m.first_name, m.last_name 
        FROM issue_requests ir 
        JOIN memberships m ON ir.membership_id = m.membership_id 
        ORDER BY ir.requested_date DESC
    ");
    $issue_requests = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading issue requests: " . $e->getMessage();
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-clock"></i> Issue Requests</h2>
            <a href="reports.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Issue Requests</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Membership Id</th>
                                <th>Name of Book/Movie</th>
                                <th>Requested Date</th>
                                <th>Request Fulfilled Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issue_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['membership_id'] . ' (' . $request['first_name'] . ' ' . $request['last_name'] . ')'); ?></td>
                                    <td><?php echo htmlspecialchars($request['book_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($request['requested_date'])); ?></td>
                                    <td>
                                        <?php if ($request['request_fulfilled_date']): ?>
                                            <?php echo date('d/m/Y', strtotime($request['request_fulfilled_date'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not fulfilled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($request['status'] === 'Pending'): ?>
                                            <span class="badge bg-warning"><?php echo htmlspecialchars($request['status']); ?></span>
                                        <?php elseif ($request['status'] === 'Fulfilled'): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($request['status']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?php echo htmlspecialchars($request['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button class="btn btn-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    // Simple Excel export functionality
    let table = document.querySelector('table');
    let wb = XLSX.utils.table_to_book(table, {sheet: "Issue Requests"});
    XLSX.writeFile(wb, "issue_requests.xlsx");
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php include 'includes/footer.php'; ?>
