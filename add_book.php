<?php
require_once 'includes/auth.php';
requireAdmin();

$page_title = 'Add Book/Movie';
include 'includes/header.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial_no = $_POST['serial_no'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $procurement_date = $_POST['procurement_date'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO books_movies 
            (serial_no, name, author, category, type, status, cost, procurement_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$serial_no, $name, $author, $category, $type, $status, $cost, $procurement_date]);
        $message = "Book/Movie added successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content p-4">
    <h2><i class="fas fa-plus"></i> Add Book/Movie</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" class="mt-4" style="max-width:600px;">
        <div class="mb-3">
            <label for="serial_no" class="form-label">Serial No</label>
            <input type="text" class="form-control" id="serial_no" name="serial_no" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Book/Movie Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" class="form-control" id="author" name="author" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <option value="Science">Science</option>
                <option value="Economics">Economics</option>
                <option value="Fiction">Fiction</option>
                <option value="Children">Children</option>
                <option value="Personal Development">Personal Development</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
                <option value="Book">Book</option>
                <option value="Movie">Movie</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Available">Available</option>
                <option value="Issued">Issued</option>
                <option value="Lost">Lost</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="cost" class="form-label">Cost</label>
            <input type="number" step="0.01" class="form-control" id="cost" name="cost">
        </div>

        <div class="mb-3">
            <label for="procurement_date" class="form-label">Procurement Date</label>
            <input type="date" class="form-control" id="procurement_date" name="procurement_date">
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
