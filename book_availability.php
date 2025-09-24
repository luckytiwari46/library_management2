<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Book Availability';
include 'includes/header.php';

$search_results = [];
$error = '';
$success = '';

$records_per_page = 10; // Show 10 books per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$search_sql = "WHERE 1=1";
$params = [];

// Handle search form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $book_name = trim($_POST['book_name']);
    $author = trim($_POST['author']);

    if (empty($book_name) && empty($author)) {
        $error = 'Please enter either book name or author name to search';
    } else {
        if (!empty($book_name)) {
            $search_sql .= " AND name LIKE ?";
            $params[] = "%$book_name%";
        }
        if (!empty($author)) {
            $search_sql .= " AND author LIKE ?";
            $params[] = "%$author%";
        }
        $_SESSION['book_search_sql'] = $search_sql;
        $_SESSION['book_search_params'] = $params;
        $page = 1; // reset to first page
    }
} elseif (isset($_SESSION['book_search_sql'])) {
    $search_sql = $_SESSION['book_search_sql'];
    $params = $_SESSION['book_search_params'];
}

// Fetch total books count for pagination
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM books_movies $search_sql");
    $count_stmt->execute($params);
    $total_books = $count_stmt->fetch()['total'];

    $total_pages = ceil($total_books / $records_per_page);

    // Fetch books for current page
    $stmt = $pdo->prepare("SELECT * FROM books_movies $search_sql ORDER BY name LIMIT $offset, $records_per_page");
    $stmt->execute($params);
    $search_results = $stmt->fetchAll();

    if (empty($search_results) && $total_books > 0) {
        $error = 'No books found on this page';
    }

} catch (Exception $e) {
    $error = "Search error: " . $e->getMessage();
}

?>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-search"></i> Book Availability</h2>
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

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-search"></i> Search Books</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="book_name" class="form-label">Enter Book Name</label>
                            <input type="text" class="form-control" id="book_name" name="book_name" 
                                   value="<?php echo $_POST['book_name'] ?? ''; ?>"
                                   placeholder="Enter book name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Enter Author</label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="<?php echo $_POST['author'] ?? ''; ?>"
                                   placeholder="Enter author name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <?php if (!empty($search_results)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Search Results</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Book Name</th>
                                    <th>Author Name</th>
                                    <th>Serial Number</th>
                                    <th>Available</th>
                                    <th>Select to issue the book</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $book): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['name']); ?></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo htmlspecialchars($book['serial_no']); ?></td>
                                        <td>
                                            <?php if ($book['status'] === 'Available'): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($book['status'] === 'Available'): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="select_book" 
                                                           value="<?php echo htmlspecialchars($book['serial_no']); ?>"
                                                           id="book_<?php echo $book['id']; ?>">
                                                    <label class="form-check-label" for="book_<?php echo $book['id']; ?>">
                                                        Select
                                                    </label>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Not Available</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination mt-3">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="book_issue.php" class="btn btn-success">
                            <i class="fas fa-hand-holding"></i> Proceed to Issue Book
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
