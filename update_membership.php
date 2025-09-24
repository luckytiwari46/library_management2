<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Update Membership';
include 'includes/header.php';

$message = '';

// Check if membership_id is provided
if (!isset($_GET['membership_id'])) {
    header("Location: report_master_memberships.php");
    exit;
}

$membership_id = $_GET['membership_id'];

// Fetch current membership details
try {
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id = ?");
    $stmt->execute([$membership_id]);
    $membership = $stmt->fetch();
    if (!$membership) {
        $message = "Membership not found!";
    }
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}

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
            UPDATE memberships SET
            first_name = ?, last_name = ?, contact_number = ?, contact_address = ?, 
            aadhar_card_no = ?, start_date = ?, end_date = ?, status = ?, amount_pending = ?
            WHERE membership_id = ?
        ");
        $stmt->execute([
            $first_name, $last_name, $contact_number, $contact_address,
            $aadhar_card_no, $start_date, $end_date, $status, $amount_pending, $membership_id
        ]);
        $message = "Membership updated successfully!";
        
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id = ?");
        $stmt->execute([$membership_id]);
        $membership = $stmt->fetch();
    } catch (Exception $e) {
        $message = "Error updating membership: " . $e->getMessage();
    }
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content p-4">
    <h2><i class="fas fa-edit"></i> Update Membership</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($membership): ?>
        <form method="post" style="max-width:700px;">
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($membership['first_name']); ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($membership['last_name']); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($membership['contact_number']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Address</label>
                <textarea class="form-control" name="contact_address" rows="2" required><?php echo htmlspecialchars($membership['contact_address']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Aadhar Card Number</label>
                <input type="text" class="form-control" name="aadhar_card_no" value="<?php echo htmlspecialchars($membership['aadhar_card_no']); ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $membership['start_date']; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $membership['end_date']; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="Active" <?php if($membership['status']=='Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if($membership['status']=='Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount Pending (Fine)</label>
                <input type="number" step="0.01" class="form-control" name="amount_pending" value="<?php echo $membership['amount_pending']; ?>">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Membership</button>
            <a href="report_master_memberships.php" class="btn btn-secondary">Back to Memberships</a>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
