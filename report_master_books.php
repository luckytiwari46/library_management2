<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Master List of Books';
include 'includes/header.php';

// Pagination setup
$limit = 10; // 10 records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get total number of books
try {
    $stmt_total = $pdo->query("SELECT COUNT(*) as total FROM books_movies WHERE type='Book'");
    $total_books = $stmt_total->fetch()['total'];
    $total_pages = ceil($total_books / $limit);
} catch (Exception $e) {
    $error = "Error fetching total books: " . $e->getMessage();
}

// Get books for current page
$books = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM books_movies WHERE type='Book' ORDER BY name LIMIT ?, ?");
    $stmt->bindValue(1, $start, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading books: " . $e->getMessage();
}

// Include sidebar
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="p-4" style="width:80%">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-book"></i> Master List of Books</h2>
            <a href="reports.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Master List of Books</h5>
                <a href="add_book.php" class="btn btn-success"><i class="fas fa-plus"></i> Add Book</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name of Book</th>
                                <th>Author Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Procurement Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['serial_no']); ?></td>
                                    <td><?php echo htmlspecialchars($book['name']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($book['category']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($book['status'] === 'Available'): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($book['status']); ?></span>
                                        <?php elseif ($book['status'] === 'Issued'): ?>
                                            <span class="badge bg-warning"><?php echo htmlspecialchars($book['status']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?php echo htmlspecialchars($book['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rs. <?php echo number_format($book['cost'], 2); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($book['procurement_date'])); ?></td>
                                    <td>
                                        <a href="update_book.php?book_id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="mt-3">
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button class="btn btn-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    let table = document.querySelector('table');
    let wb = XLSX.utils.table_to_book(table, {sheet: "Master List of Books"});
    XLSX.writeFile(wb, "master_list_books.xlsx");
}
</script>

<?php include 'includes/footer.php'; ?>
