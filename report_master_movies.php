<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Master List of Movies';
include 'includes/header.php';

// Get all movies
$movies = [];
try {
    $stmt = $pdo->query("SELECT * FROM books_movies WHERE type = 'Movie' ORDER BY name");
    $movies = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error loading movies: " . $e->getMessage();
}
?>

<div class="col-md-2">
    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="<?php echo $_SESSION['user_type'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="nav-link" href="reports.php">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a class="nav-link active" href="report_master_movies.php">
                <i class="fas fa-film"></i> Master List of Movies
            </a>
        </nav>
    </div>
</div>

<div class="col-md-10">
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-film"></i> Master List of Movies</h2>
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
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Master List of Movies</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name of Movie</th>
                                <th>Director Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Procurement Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movies as $movie): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($movie['serial_no']); ?></td>
                                    <td><?php echo htmlspecialchars($movie['name']); ?></td>
                                    <td><?php echo htmlspecialchars($movie['author']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($movie['category']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($movie['status'] === 'Available'): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($movie['status']); ?></span>
                                        <?php elseif ($movie['status'] === 'Issued'): ?>
                                            <span class="badge bg-warning"><?php echo htmlspecialchars($movie['status']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?php echo htmlspecialchars($movie['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rs. <?php echo number_format($movie['cost'], 2); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($movie['procurement_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
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

<script>
function exportToExcel() {
    // Simple Excel export functionality
    let table = document.querySelector('table');
    let wb = XLSX.utils.table_to_book(table, {sheet: "Master List of Movies"});
    XLSX.writeFile(wb, "master_list_movies.xlsx");
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php include 'includes/footer.php'; ?>
