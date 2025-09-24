<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Add Membership';
include 'includes/header.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $contact_address = $_POST['contact_address'];
    $aadhar_card_no = $_POST['aadhar_card_no'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $amount_pending = $_POST['amount_pending'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO memberships
            (first_name, last_name, contact_number, contact_address, aadhar_card_no, start_date, end_date, status, amount_pending)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $first_name, $last_name, $contact_number, $contact_address,
            $aadhar_card_no, $start_date, $end_date, $status, $amount_pending
        ]);
        $message = "Membership added successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content p-4">
    <h2><i class="fas fa-user-plus"></i> Add Membership</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" style="max-width:700px;">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="last_name" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" class="form-control" name="contact_number" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Address</label>
            <textarea class="form-control" name="contact_address" rows="2" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Aadhar Card Number</label>
            <input type="text" class="form-control" name="aadhar_card_no" required>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Start Date of Membership</label>
                <input type="date" class="form-control" name="start_date" required>
            </div>
            <div class="col">
                <label class="form-label">End Date of Membership</label>
                <input type="date" class="form-control" name="end_date" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount Pending (Fine)</label>
            <input type="number" step="0.01" class="form-control" name="amount_pending" value="0">
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Add Membership</button>
        <a href="report_master_memberships.php" class="btn btn-secondary">Back to Memberships</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
