<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Add Membership';
include 'includes/header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_membership'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact_number = trim($_POST['contact_number']);
    $contact_address = trim($_POST['contact_address']);
    $aadhar_card_no = trim($_POST['aadhar_card_no']);
    $start_date = $_POST['start_date'];
    $membership_duration = $_POST['membership_duration'];
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($contact_number) || 
        empty($contact_address) || empty($aadhar_card_no) || empty($start_date) || 
        empty($membership_duration)) {
        $error = 'All fields are mandatory';
    } else {
        try {
            // Calculate end date based on membership duration
            $start_date_obj = new DateTime($start_date);
            $end_date_obj = clone $start_date_obj;
            
            if ($membership_duration === '6_months') {
                $end_date_obj->add(new DateInterval('P6M'));
            } elseif ($membership_duration === '1_year') {
                $end_date_obj->add(new DateInterval('P1Y'));
            } elseif ($membership_duration === '2_years') {
                $end_date_obj->add(new DateInterval('P2Y'));
            }
            
            // Generate membership ID
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM memberships");
            $count = $stmt->fetch()['count'] + 1;
            $membership_id = 'MEM' . str_pad($count, 3, '0', STR_PAD_LEFT);
            
            // Insert membership
            $stmt = $pdo->prepare("
                INSERT INTO memberships (membership_id, first_name, last_name, contact_number, contact_address, aadhar_card_no, start_date, end_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $membership_id, $first_name, $last_name, $contact_number, 
                $contact_address, $aadhar_card_no, $start_date, $end_date_obj->format('Y-m-d')
            ]);
            
            $success = "Membership added successfully with ID: $membership_id";
            
            // Clear form
            $_POST = [];
            
        } catch (Exception $e) {
            $error = "Error adding membership: " . $e->getMessage();
        }
    }
}
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
            <h2><i class="fas fa-user-plus"></i> Add Membership</h2>
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

        <!-- Add Membership Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Add Membership</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                   value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="aadhar_card_no" class="form-label">Aadhar Card No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="aadhar_card_no" name="aadhar_card_no" 
                                   value="<?php echo isset($_POST['aadhar_card_no']) ? htmlspecialchars($_POST['aadhar_card_no']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="contact_address" class="form-label">Contact Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="contact_address" name="contact_address" rows="3" required><?php echo isset($_POST['contact_address']) ? htmlspecialchars($_POST['contact_address']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Membership Duration <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="membership_duration" id="six_months" value="6_months" 
                                           <?php echo (!isset($_POST['membership_duration']) || $_POST['membership_duration'] === '6_months') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="six_months">
                                        Six Months
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="membership_duration" id="one_year" value="1_year"
                                           <?php echo (isset($_POST['membership_duration']) && $_POST['membership_duration'] === '1_year') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="one_year">
                                        One Year
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="membership_duration" id="two_years" value="2_years"
                                           <?php echo (isset($_POST['membership_duration']) && $_POST['membership_duration'] === '2_years') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="two_years">
                                        Two Years
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="add_membership" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Membership
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Clear
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        All fields are required. By default, 6 months membership is selected.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
