<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Book Issue';
include 'includes/header.php';

$error = '';
$success = '';

// Get all books for dropdown
$books = [];
try {
    $stmt = $pdo->query("SELECT serial_no, name, author FROM books_movies WHERE status = 'Available' ORDER BY name");
    $books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading books: " . $e->getMessage();
}

// Get all active memberships
$memberships = [];
try {
    $stmt = $pdo->query("SELECT membership_id, first_name, last_name FROM memberships WHERE status = 'Active' ORDER BY first_name");
    $memberships = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading memberships: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_book'])) {
    $serial_no = trim($_POST['serial_no']);
    $membership_id = trim($_POST['membership_id']);
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];
    $remarks = trim($_POST['remarks']);
    
    // Validation
    if (empty($serial_no) || empty($membership_id) || empty($issue_date) || empty($return_date)) {
        $error = 'Please fill in all required fields';
    } else {
        // Validate dates
        $issue_date_obj = new DateTime($issue_date);
        $return_date_obj = new DateTime($return_date);
        $today = new DateTime();
        
        if ($issue_date_obj < $today) {
            $error = 'Issue Date cannot be lesser than today';
        } elseif ($return_date_obj <= $issue_date_obj) {
            $error = 'Return Date must be after Issue Date';
        } else {
            try {
                // Check if book is available
                $stmt = $pdo->prepare("SELECT status FROM books_movies WHERE serial_no = ?");
                $stmt->execute([$serial_no]);
                $book = $stmt->fetch();
                
                if (!$book) {
                    $error = 'Book not found';
                } elseif ($book['status'] !== 'Available') {
                    $error = 'Book is not available for issue';
                } else {
                    // Issue the book
                    $pdo->beginTransaction();
                    
                    // Insert issue record
                    $stmt = $pdo->prepare("INSERT INTO book_issues (serial_no, membership_id, issue_date, expected_return_date, remarks) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$serial_no, $membership_id, $issue_date, $return_date, $remarks]);
                    
                    // Update book status
                    $stmt = $pdo->prepare("UPDATE books_movies SET status = 'Issued' WHERE serial_no = ?");
                    $stmt->execute([$serial_no]);
                    
                    $pdo->commit();
                    
                    $success = 'Book issued successfully';
                    
                    // Clear form
                    $_POST = [];
                }
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error issuing book: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="col-md-2">
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="<?php echo $_SESSION['user_type'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="nav-link" href="transactions.php">
                <i class="fas fa-exchange-alt"></i> Transactions
            </a>
            <a class="nav-link active" href="book_issue.php">
                <i class="fas fa-hand-holding"></i> Book Issue
            </a>
            <a class="nav-link" href="reports.php">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </nav>
    </div>
</div>

<div class="col-md-10">
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-hand-holding"></i> Book Issue</h2>
            <a href="transactions.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Transactions
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

        <!-- Book Issue Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-hand-holding"></i> Issue Book</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="serial_no" class="form-label">Enter Book Name <span class="text-danger">*</span></label>
                            <select class="form-control" id="serial_no" name="serial_no" required>
                                <option value="">Select Book</option>
                                <?php foreach ($books as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['serial_no']); ?>"
                                            data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                            <?php echo (isset($_POST['serial_no']) && $_POST['serial_no'] === $book['serial_no']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($book['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Enter Author</label>
                            <input type="text" class="form-control" id="author" name="author" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="membership_id" class="form-label">Membership ID <span class="text-danger">*</span></label>
                            <select class="form-control" id="membership_id" name="membership_id" required>
                                <option value="">Select Membership</option>
                                <?php foreach ($memberships as $membership): ?>
                                    <option value="<?php echo htmlspecialchars($membership['membership_id']); ?>"
                                            <?php echo (isset($_POST['membership_id']) && $_POST['membership_id'] === $membership['membership_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($membership['membership_id'] . ' - ' . $membership['first_name'] . ' ' . $membership['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="issue_date" name="issue_date" 
                                   value="<?php echo isset($_POST['issue_date']) ? $_POST['issue_date'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="return_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="return_date" name="return_date" 
                                   value="<?php echo isset($_POST['return_date']) ? $_POST['return_date'] : date('Y-m-d', strtotime('+15 days')); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Non Mandatory"><?php echo isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="issue_book" class="btn btn-success">
                                <i class="fas fa-hand-holding"></i> Issue Book
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
                        Note: If logged in as Admin - home will take to Admin Home Page<br>
                        If logged in as user - home will take to User Home Page
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-populate author when book is selected
    $('#serial_no').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var author = selectedOption.data('author');
        $('#author').val(author);
        
        // Auto-set return date to 15 days from issue date
        var issueDate = $('#issue_date').val();
        if (issueDate) {
            var issueDateObj = new Date(issueDate);
            var returnDateObj = new Date(issueDateObj);
            returnDateObj.setDate(issueDateObj.getDate() + 15);
            $('#return_date').val(returnDateObj.toISOString().split('T')[0]);
        }
    });
    
    // Update return date when issue date changes
    $('#issue_date').on('change', function() {
        var issueDate = $(this).val();
        if (issueDate) {
            var issueDateObj = new Date(issueDate);
            var returnDateObj = new Date(issueDateObj);
            returnDateObj.setDate(issueDateObj.getDate() + 15);
            $('#return_date').val(returnDateObj.toISOString().split('T')[0]);
        }
    });
    
    // Set minimum date for issue date to today
    $('#issue_date').attr('min', new Date().toISOString().split('T')[0]);
});
</script>

<?php include 'includes/footer.php'; ?>
