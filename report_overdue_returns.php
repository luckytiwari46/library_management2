<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Overdue Returns';
include 'includes/header.php';

// Get all overdue returns
$overdue_returns = [];
try {
    $stmt = $pdo->query("
        SELECT bi.*, bm.name as book_name, m.first_name, m.last_name 
        FROM book_issues bi 
        JOIN books_movies bm ON bi.serial_no = bm.serial_no 
        JOIN memberships m ON bi.membership_id = m.membership_id 
        WHERE bi.status = 'Active' AND bi.expected_return_date < CURDATE()
        ORDER BY bi.expected_return_date ASC
    ");
    $overdue_returns = $stmt->fetchAll();
    
    // Calculate fines for each overdue return
    foreach ($overdue_returns as &$return) {
        $expected_return = new DateTime($return['expected_return_date']);
        $today = new DateTime();
        $days_overdue = $expected_return->diff($today)->days;
        $return['calculated_fine'] = $days_overdue * 10; // Rs. 10 per day
    }
} catch (Exception $e) {
    $error = "Error loading overdue returns: " . $e->getMessage();
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-exclamation-triangle"></i> Overdue Returns</h2>
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
                <h5 class="mb-0"><i class="fas fa-list"></i> Overdue Returns</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Serial No Book</th>
                                <th>Name of Book</th>
                                <th>Membership Id</th>
                                <th>Date of Issue</th>
                                <th>Date of Return</th>
                                <th>Fine Calculations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overdue_returns as $return): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($return['serial_no']); ?></td>
                                    <td><?php echo htmlspecialchars($return['book_name']); ?></td>
                                    <td><?php echo htmlspecialchars($return['membership_id'] . ' (' . $return['first_name'] . ' ' . $return['last_name'] . ')'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($return['issue_date'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($return['expected_return_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-danger">
                                            Rs. <?php echo number_format($return['calculated_fine'], 2); ?>
                                        </span>
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
    let wb = XLSX.utils.table_to_book(table, {sheet: "Overdue Returns"});
    XLSX.writeFile(wb, "overdue_returns.xlsx");
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php include 'includes/footer.php'; ?>
