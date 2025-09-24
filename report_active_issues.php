<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Active Issues';
include 'includes/header.php';

// Get all active issues
$active_issues = [];
try {
    $stmt = $pdo->query("
        SELECT bi.*, bm.name as book_name, bm.type, m.first_name, m.last_name 
        FROM book_issues bi 
        JOIN books_movies bm ON bi.serial_no = bm.serial_no 
        JOIN memberships m ON bi.membership_id = m.membership_id 
        WHERE bi.status = 'Active' 
        ORDER BY bi.issue_date DESC
    ");
    $active_issues = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading active issues: " . $e->getMessage();
}
?>

<!-- Include sidebar -->
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-hand-holding"></i> Active Issues</h2>
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
                <h5 class="mb-0"><i class="fas fa-list"></i> Active Issues</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Serial No Book/Movie</th>
                                <th>Name of Book/Movie</th>
                                <th>Membership Id</th>
                                <th>Date of Issue</th>
                                <th>Date of Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_issues as $issue): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($issue['serial_no']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($issue['book_name']); ?>
                                        <span class="badge bg-info ms-1"><?php echo htmlspecialchars($issue['type']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($issue['membership_id'] . ' (' . $issue['first_name'] . ' ' . $issue['last_name'] . ')'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($issue['issue_date'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($issue['expected_return_date'])); ?></td>
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
    let wb = XLSX.utils.table_to_book(table, {sheet: "Active Issues"});
    XLSX.writeFile(wb, "active_issues.xlsx");
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php include 'includes/footer.php'; ?>
