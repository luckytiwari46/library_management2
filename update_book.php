<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Update Book';
include 'includes/header.php';

$message = '';

// Check if book_id is provided
if (!isset($_GET['book_id'])) {
    header("Location: report_master_books.php");
    exit;
}

$book_id = $_GET['book_id'];

// Fetch current book details
try {
    $stmt = $pdo->prepare("SELECT * FROM books_movies WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) {
        $message = "Book not found!";
    }
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial_no = $_POST['serial_no'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $procurement_date = $_POST['procurement_date'];

    try {
        $stmt = $pdo->prepare("
            UPDATE books_movies SET
                serial_no = ?, name = ?, author = ?, category = ?, status = ?, cost = ?, procurement_date = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $serial_no, $name, $author, $category, $status, $cost, $procurement_date, $book_id
        ]);
        $message = "Book updated successfully!";

        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM books_movies WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();
    } catch (Exception $e) {
        $message = "Error updating book: " . $e->getMessage();
    }
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content p-4">
    <h2><i class="fas fa-edit"></i> Update Book</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($book): ?>
        <form method="post" style="max-width:700px;">
            <div class="mb-3">
                <label class="form-label">Serial No</label>
                <input type="text" class="form-control" name="serial_no" value="<?php echo htmlspecialchars($book['serial_no']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Book Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($book['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category" required>
                    <?php 
                    $categories = ['Science','Economics','Fiction','Children','Personal Development'];
                    foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if($book['category']==$cat) echo 'selected'; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <?php 
                    $statuses = ['Available','Issued','Lost'];
                    foreach($statuses as $stat): ?>
                        <option value="<?php echo $stat; ?>" <?php if($book['status']==$stat) echo 'selected'; ?>>
                            <?php echo $stat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Cost (Rs.)</label>
                <input type="number" step="0.01" class="form-control" name="cost" value="<?php echo $book['cost']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Procurement Date</label>
                <input type="date" class="form-control" name="procurement_date" value="<?php echo $book['procurement_date']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Book</button>
            <a href="report_master_books.php" class="btn btn-secondary">Back to Books</a>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
