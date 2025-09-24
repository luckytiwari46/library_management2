<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Master List of Memberships';
include 'includes/header.php';

// Get all memberships
$memberships = [];
try {
    $stmt = $pdo->query("SELECT * FROM memberships ORDER BY membership_id");
    $memberships = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading memberships: " . $e->getMessage();
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Master List of Memberships</h2>
        <div>
            <a href="add_membership.php" class="btn btn-success me-2">
                <i class="fas fa-user-plus"></i> Add Membership
            </a>
            <a href="reports.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Membership Id</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Contact Address</th>
                        <th>Aadhar Card No</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Amount Pending</th>
                        <th>Update Mebership</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($memberships)): ?>
                        <?php foreach ($memberships as $membership): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($membership['membership_id']); ?></td>
                                <td><?php echo htmlspecialchars($membership['first_name'] . ' ' . $membership['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($membership['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($membership['contact_address']); ?></td>
                                <td><?php echo htmlspecialchars($membership['aadhar_card_no']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($membership['start_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($membership['end_date'])); ?></td>
                                <td>
                                    <?php if ($membership['status'] === 'Active'): ?>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($membership['status']); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?php echo htmlspecialchars($membership['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>Rs. <?php echo number_format($membership['amount_pending'], 2); ?></td>
                                <td>
                                    <a href="update_membership.php?membership_id=<?php echo $membership['membership_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No memberships found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="mt-3">
                <button class="btn btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="btn btn-primary" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    let table = document.querySelector('table');
    let wb = XLSX.utils.table_to_book(table, { sheet: "Memberships" });
    XLSX.writeFile(wb, "memberships_report.xlsx");
}
</script>

<?php include 'includes/footer.php'; ?>
