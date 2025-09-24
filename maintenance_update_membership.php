<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Update Membership';
include 'includes/header.php';

$error = '';
$success = '';
$membership = null;

// Get all memberships for dropdown
$memberships = [];
try {
    $stmt = $pdo->query("SELECT membership_id, first_name, last_name FROM memberships ORDER BY membership_id");
    $memberships = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading memberships: " . $e->getMessage();
}

// Handle membership selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_membership'])) {
    $membership_id = $_POST['membership_id'];
    
    if (!empty($membership_id)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id = ?");
            $stmt->execute([$membership_id]);
            $membership = $stmt->fetch();
            
            if (!$membership) {
                $error = 'Membership not found';
            }
        } catch (Exception $e) {
            $error = "Error loading membership: " . $e->getMessage();
        }
    } else {
        $error = 'Please select a membership';
    }
}

// Handle membership update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_membership'])) {
    $membership_id = $_POST['membership_id'];
    $action = $_POST['action'];
    
    if (empty($membership_id) || empty($action)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id = ?");
            $stmt->execute([$membership_id]);
            $membership = $stmt->fetch();
            
            if (!$membership) {
                $error = 'Membership not found';
            } else {
                $new_end_date = new DateTime($membership['end_date']);
                
                if ($action === 'extend_6_months') {
                    $new_end_date->add(new DateInterval('P6M'));
                    $success = 'Membership extended by 6 months';
                } elseif ($action === 'extend_1_year') {
                    $new_end_date->add(new DateInterval('P1Y'));
                    $success = 'Membership extended by 1 year';
                } elseif ($action === 'cancel') {
                    $new_end_date = new DateTime();
                    $success = 'Membership cancelled';
                }
                
                // Update membership
                $stmt = $pdo->prepare("UPDATE memberships SET end_date = ?, status = ? WHERE membership_id = ?");
                $status = ($action === 'cancel') ? 'Inactive' : 'Active';
                $stmt->execute([$new_end_date->format('Y-m-d'), $status, $membership_id]);
                
                // Reload membership data
                $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id = ?");
                $stmt->execute([$membership_id]);
                $membership = $stmt->fetch();
            }
            
        } catch (Exception $e) {
            $error = "Error updating membership: " . $e->getMessage();
        }
    }
}
?>

<div class="col-md-2">
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="admin_dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="nav-link" href="maintenance.php">
                <i class="fas fa-tools"></i> Maintenance
            </a>
            <a class="nav-link active" href="maintenance_update_membership.php">
                <i class="fas fa-user-edit"></i> Update Membership
            </a>
        </nav>
    </div>
</div>

<div class="col-md-10">
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-edit"></i> Update Membership</h2>
            <a href="maintenance.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Maintenance
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Select Membership -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search"></i> Select Membership</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="membership_id" class="form-label">Membership Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="membership_id" name="membership_id" required>
                                <option value="">Select Membership</option>
                                <?php foreach ($memberships as $mem): ?>
                                    <option value="<?php echo htmlspecialchars($mem['membership_id']); ?>"
                                            <?php echo (isset($_POST['membership_id']) && $_POST['membership_id'] === $mem['membership_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mem['membership_id'] . ' - ' . $mem['first_name'] . ' ' . $mem['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" name="select_membership" class="btn btn-primary d-block">
                                <i class="fas fa-search"></i> Load Membership
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Membership -->
        <?php if ($membership): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Update Membership</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="membership_id" value="<?php echo htmlspecialchars($membership['membership_id']); ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Membership ID</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($membership['membership_id']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Member Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($membership['first_name'] . ' ' . $membership['last_name']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Start Date</label>
                                <input type="text" class="form-control" value="<?php echo date('d/m/Y', strtotime($membership['start_date'])); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current End Date</label>
                                <input type="text" class="form-control" value="<?php echo date('d/m/Y', strtotime($membership['end_date'])); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Status</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($membership['status']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Action <span class="text-danger">*</span></label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="extend_6_months" value="extend_6_months" checked>
                                        <label class="form-check-label" for="extend_6_months">
                                            Six months extension
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="extend_1_year" value="extend_1_year">
                                        <label class="form-check-label" for="extend_1_year">
                                            One year extension
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="cancel" value="cancel">
                                        <label class="form-check-label" for="cancel">
                                            Cancel membership
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" name="update_membership" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Membership
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
