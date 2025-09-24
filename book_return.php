<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Return Book';
include 'includes/header.php';

$error = '';
$success = '';

// Get all issued books for dropdown
$issued_books = [];
try {
    $stmt = $pdo->query("
        SELECT bi.*, bm.name as book_name, bm.author, m.first_name, m.last_name 
        FROM book_issues bi 
        JOIN books_movies bm ON bi.serial_no = bm.serial_no 
        JOIN memberships m ON bi.membership_id = m.membership_id 
        WHERE bi.status = 'Active' 
        ORDER BY bi.issue_date DESC
    ");
    $issued_books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading issued books: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $issue_id = $_POST['issue_id'];
    $actual_return_date = $_POST['actual_return_date'];
    $remarks = trim($_POST['remarks']);
    
    // Validation
    if (empty($issue_id) || empty($actual_return_date)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            // Get issue details
            $stmt = $pdo->prepare("
                SELECT bi.*, bm.name as book_name, bm.author, m.first_name, m.last_name 
                FROM book_issues bi 
                JOIN books_movies bm ON bi.serial_no = bm.serial_no 
                JOIN memberships m ON bi.membership_id = m.membership_id 
                WHERE bi.id = ?
            ");
            $stmt->execute([$issue_id]);
            $issue = $stmt->fetch();
            
            if (!$issue) {
                $error = 'Issue record not found';
            } else {
                // Calculate fine if overdue
                $expected_return = new DateTime($issue['expected_return_date']);
                $actual_return = new DateTime($actual_return_date);
                $fine_amount = 0;
                
                if ($actual_return > $expected_return) {
                    $days_overdue = $expected_return->diff($actual_return)->days;
                    $fine_amount = $days_overdue * 10; // Rs. 10 per day overdue
                }
                
                // Start transaction
                $pdo->beginTransaction();
                
                // Update book issue record
                $stmt = $pdo->prepare("
                    UPDATE book_issues 
                    SET actual_return_date = ?, fine_amount = ?, remarks = ?, status = ? 
                    WHERE id = ?
                ");
                $status = $fine_amount > 0 ? 'Overdue' : 'Returned';
                $stmt->execute([$actual_return_date, $fine_amount, $remarks, $status, $issue_id]);
                
                // Update book status to available
                $stmt = $pdo->prepare("UPDATE books_movies SET status = 'Available' WHERE serial_no = ?");
                $stmt->execute([$issue['serial_no']]);
                
                $pdo->commit();
                
                if ($fine_amount > 0) {
                    $success = "Book returned successfully. Fine amount: Rs. $fine_amount. Please proceed to Pay Fine page.";
                    // Redirect to pay fine page after 3 seconds
                    echo '<script>setTimeout(function(){ window.location.href = "pay_fine.php"; }, 3000);</script>';
                } else {
                    $success = 'Book returned successfully with no fine.';
                }
                
                // Clear form
                $_POST = [];
            }
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error returning book: " . $e->getMessage();
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
            <a class="nav-link active" href="book_return.php">
                <i class="fas fa-undo"></i> Return Book
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
            <h2><i class="fas fa-undo"></i> Return Book</h2>
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

        <!-- Return Book Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-undo"></i> Return Book</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_id" class="form-label">Enter Book Name <span class="text-danger">*</span></label>
                            <select class="form-control" id="issue_id" name="issue_id" required>
                                <option value="">Select Book to Return</option>
                                <?php foreach ($issued_books as $book): ?>
                                    <option value="<?php echo $book['id']; ?>"
                                            data-book-name="<?php echo htmlspecialchars($book['book_name']); ?>"
                                            data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                            data-serial-no="<?php echo htmlspecialchars($book['serial_no']); ?>"
                                            data-issue-date="<?php echo $book['issue_date']; ?>"
                                            data-return-date="<?php echo $book['expected_return_date']; ?>"
                                            <?php echo (isset($_POST['issue_id']) && $_POST['issue_id'] == $book['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($book['book_name'] . ' - ' . $book['first_name'] . ' ' . $book['last_name']); ?>
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
                            <label for="serial_no" class="form-label">Serial No</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" readonly>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">Issue Date</label>
                            <input type="text" class="form-control" id="issue_date" name="issue_date" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="return_date" class="form-label">Expected Return Date</label>
                            <input type="text" class="form-control" id="return_date" name="return_date" readonly>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="actual_return_date" class="form-label">Actual Return Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="actual_return_date" name="actual_return_date" 
                                   value="<?php echo isset($_POST['actual_return_date']) ? $_POST['actual_return_date'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Non Mandatory"><?php echo isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="return_book" class="btn btn-warning">
                                <i class="fas fa-undo"></i> Return Book
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
    // Auto-populate fields when book is selected
    $('#issue_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var bookName = selectedOption.data('book-name');
        var author = selectedOption.data('author');
        var serialNo = selectedOption.data('serial-no');
        var issueDate = selectedOption.data('issue-date');
        var returnDate = selectedOption.data('return-date');
        
        $('#author').val(author);
        $('#serial_no').val(serialNo);
        $('#issue_date').val(issueDate);
        $('#return_date').val(returnDate);
        
        // Set default actual return date to today
        $('#actual_return_date').val(new Date().toISOString().split('T')[0]);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
