<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Pay Fine';
include 'includes/header.php';

$error = '';
$success = '';

// Get all books with pending fines
$fine_books = [];
try {
    $stmt = $pdo->query("
        SELECT bi.*, bm.name as book_name, bm.author, m.first_name, m.last_name 
        FROM book_issues bi 
        JOIN books_movies bm ON bi.serial_no = bm.serial_no 
        JOIN memberships m ON bi.membership_id = m.membership_id 
        WHERE bi.status IN ('Overdue', 'Returned') AND bi.fine_amount > 0 AND bi.fine_paid = 0
        ORDER BY bi.actual_return_date DESC
    ");
    $fine_books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading fine records: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_fine'])) {
    $issue_id = $_POST['issue_id'];
    $fine_paid = isset($_POST['fine_paid']) ? 1 : 0;
    $remarks = trim($_POST['remarks']);
    
    // Validation
    if (empty($issue_id)) {
        $error = 'Please select a book';
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
                // If there's a fine, it must be paid
                if ($issue['fine_amount'] > 0 && !$fine_paid) {
                    $error = 'Fine must be paid before completing the transaction';
                } else {
                    // Start transaction
                    $pdo->beginTransaction();
                    
                    // Update fine payment status
                    $stmt = $pdo->prepare("
                        UPDATE book_issues 
                        SET fine_paid = ?, remarks = ?, status = 'Returned' 
                        WHERE id = ?
                    ");
                    $stmt->execute([$fine_paid, $remarks, $issue_id]);
                    
                    // Update membership pending amount
                    if ($fine_paid) {
                        $stmt = $pdo->prepare("
                            UPDATE memberships 
                            SET amount_pending = amount_pending - ? 
                            WHERE membership_id = ?
                        ");
                        $stmt->execute([$issue['fine_amount'], $issue['membership_id']]);
                    }
                    
                    $pdo->commit();
                    
                    $success = 'Fine payment processed successfully. Transaction completed.';
                    
                    // Clear form
                    $_POST = [];
                    
                    // Refresh the page to show updated data
                    echo '<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>';
                }
            }
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error processing fine payment: " . $e->getMessage();
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
            <a class="nav-link active" href="pay_fine.php">
                <i class="fas fa-credit-card"></i> Pay Fine
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
            <h2><i class="fas fa-credit-card"></i> Pay Fine</h2>
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

        <?php if (empty($fine_books)): ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> No pending fines found.
            </div>
        <?php else: ?>
            <!-- Fine Payment Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Pay Fine</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="issue_id" class="form-label">Enter Book Name <span class="text-danger">*</span></label>
                                <select class="form-control" id="issue_id" name="issue_id" required>
                                    <option value="">Select Book with Fine</option>
                                    <?php foreach ($fine_books as $book): ?>
                                        <option value="<?php echo $book['id']; ?>"
                                                data-book-name="<?php echo htmlspecialchars($book['book_name']); ?>"
                                                data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                                data-serial-no="<?php echo htmlspecialchars($book['serial_no']); ?>"
                                                data-issue-date="<?php echo $book['issue_date']; ?>"
                                                data-return-date="<?php echo $book['expected_return_date']; ?>"
                                                data-actual-return-date="<?php echo $book['actual_return_date']; ?>"
                                                data-fine-amount="<?php echo $book['fine_amount']; ?>"
                                                <?php echo (isset($_POST['issue_id']) && $_POST['issue_id'] == $book['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($book['book_name'] . ' - Fine: Rs. ' . $book['fine_amount']); ?>
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
                                <label for="actual_return_date" class="form-label">Actual Return Date</label>
                                <input type="text" class="form-control" id="actual_return_date" name="actual_return_date" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fine_amount" class="form-label">Fine Calculated</label>
                                <input type="text" class="form-control" id="fine_amount" name="fine_amount" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fine Paid</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fine_paid" name="fine_paid" 
                                           <?php echo (isset($_POST['fine_paid'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="fine_paid">
                                        Fine has been paid
                                    </label>
                                </div>
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
                                <button type="submit" name="pay_fine" class="btn btn-info">
                                    <i class="fas fa-credit-card"></i> Complete Transaction
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
        <?php endif; ?>
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
        var actualReturnDate = selectedOption.data('actual-return-date');
        var fineAmount = selectedOption.data('fine-amount');
        
        $('#author').val(author);
        $('#serial_no').val(serialNo);
        $('#issue_date').val(issueDate);
        $('#return_date').val(returnDate);
        $('#actual_return_date').val(actualReturnDate);
        $('#fine_amount').val('Rs. ' + fineAmount);
    });
    
    // If no fine amount, automatically check the fine paid checkbox
    $('#fine_amount').on('input', function() {
        var fineAmount = $(this).val();
        if (fineAmount === 'Rs. 0' || fineAmount === '0') {
            $('#fine_paid').prop('checked', true);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
