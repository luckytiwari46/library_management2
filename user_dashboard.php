<?php
require_once 'includes/auth.php';
requireLogin();

$page_title = 'User Dashboard';
include 'includes/header.php';

// Get statistics
$stats = [];
try {
    // Total books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM books_movies");
    $stats['total_books_movies'] = $stmt->fetch()['total'];

    // Available books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM books_movies WHERE status = 'Available'");
    $stats['available'] = $stmt->fetch()['total'];

    // Issued books/movies
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_issues WHERE status = 'Active'");
    $stats['issued'] = $stmt->fetch()['total'];

    // Active members
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM memberships WHERE status = 'Active'");
    $stats['active_members'] = $stmt->fetch()['total'];

} catch (Exception $e) {
    $error = "Error loading statistics: " . $e->getMessage();
}
?>

<div class="row">
    <div class="col-md-2">
        <div class="sidebar">
            <nav class="nav flex-column">
                <a class="nav-link active btn btn-sidebar mb-2" href="user_dashboard.php">
                    <i class="fas fa-home"></i> Home Page
                </a>
                <a class="nav-link btn btn-sidebar mb-2" href="reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a class="nav-link btn btn-sidebar mb-2" href="transactions.php">
                    <i class="fas fa-exchange-alt"></i> Transactions
                </a>
                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <a class="nav-link btn btn-sidebar mb-2" href="admin_dashboard.php">
                        <i class="fas fa-cog"></i> Admin Home Page
                    </a>
                <?php endif; ?>
            </nav>
        </div>
        <style>
            .btn-sidebar {
                text-align: left;
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                transition: all 0.3s;
                color: #333;
            }
            .btn-sidebar:hover {
                background-color: #e9ecef;
                border-color: #ced4da;
                transform: translateX(5px);
            }
            .btn-sidebar.active {
                background-color: #007bff;
                color: white;
                border-color: #007bff;
            }
        </style>
    </div>

    <div class="col-md-10">
        <div class="main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt"></i> User Dashboard</h2>
                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                <?php endif; ?>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center" id="totalBooksCard" style="cursor:pointer;">
                        <div class="card-body">
                            <i class="fas fa-book fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Total Books/Movies</h5>
                            <h3 class="text-primary"><?php echo $stats['total_books_movies'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center" id="availableCard" style="cursor:pointer;">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Available</h5>
                            <h3 class="text-success"><?php echo $stats['available'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center" id="issuedCard" style="cursor:pointer;">
                        <div class="card-body">
                            <i class="fas fa-hand-holding fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">Issued</h5>
                            <h3 class="text-warning"><?php echo $stats['issued'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-info mb-2"></i>
                            <h5 class="card-title">Active Members</h5>
                            <h3 class="text-info"><?php echo $stats['active_members'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Section -->
            <div id="booksSection" class="card mb-4" style="display:none;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Total Books/Movies</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <?php if ($_SESSION['user_type'] === 'admin'): ?>
                                        <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="booksTableBody">
                                <!-- AJAX loaded books -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Available Section -->
            <div id="availableSection" class="card mb-4" style="display:none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Available Books/Movies</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody id="availableTableBody">
                                <!-- AJAX loaded available books -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Issued Section -->
            <div id="issuedSection" class="card mb-4" style="display:none;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-hand-holding"></i> Issued Books/Movies</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody id="issuedTableBody">
                                <!-- AJAX loaded issued books -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Load books via AJAX
function loadBooks() {
    document.getElementById('availableSection').style.display = 'none';
    document.getElementById('issuedSection').style.display = 'none';
    document.getElementById('booksSection').style.display = 'block';

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax/get_books.php', true);
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                const books = JSON.parse(this.responseText);
                let output = '';
                books.forEach(function(book) {
                    let action = '';
                    <?php if ($_SESSION['user_type'] === 'admin'): ?>
                        action = `<a href="update_book.php?id=${book.id}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Update
                                  </a>`;
                    <?php endif; ?>
                    output += `
                        <tr>
                            <td>${book.id}</td>
                            <td>${book.title}</td>
                            <td>${book.author}</td>
                            <td>${book.category}</td>
                            <td>${book.type}</td>
                            <td>${book.status}</td>
                            ${action ? '<td>'+action+'</td>' : ''}
                        </tr>
                    `;
                });
                document.getElementById('booksTableBody').innerHTML = output;
            } catch(e) {
                document.getElementById('booksTableBody').innerHTML = '<tr><td colspan="7">Error loading data</td></tr>';
            }
        }
    };
    xhr.send();
}

function loadAvailableBooks() {
    document.getElementById('booksSection').style.display = 'none';
    document.getElementById('issuedSection').style.display = 'none';
    document.getElementById('availableSection').style.display = 'block';

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax/get_available_books.php', true);
    xhr.onload = function() {
        if(this.status === 200){
            try{
                const books = JSON.parse(this.responseText);
                let output = '';
                books.forEach(function(book){
                    output += `
                        <tr>
                            <td>${book.id}</td>
                            <td>${book.title}</td>
                            <td>${book.author}</td>
                            <td>${book.category}</td>
                            <td>${book.type}</td>
                        </tr>
                    `;
                });
                document.getElementById('availableTableBody').innerHTML = output;
            } catch(e){
                document.getElementById('availableTableBody').innerHTML = '<tr><td colspan="5">Error loading data</td></tr>';
            }
        }
    };
    xhr.send();
}

function loadIssuedBooks() {
    document.getElementById('booksSection').style.display = 'none';
    document.getElementById('availableSection').style.display = 'none';
    document.getElementById('issuedSection').style.display = 'block';

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax/get_issued_books.php', true);
    xhr.onload = function() {
        if(this.status === 200){
            try{
                const books = JSON.parse(this.responseText);
                let output = '';
                books.forEach(function(book){
                    output += `
                        <tr>
                            <td>${book.id}</td>
                            <td>${book.title}</td>
                            <td>${book.member_name}</td>
                            <td>${book.issue_date}</td>
                            <td>${book.due_date}</td>
                        </tr>
                    `;
                });
                document.getElementById('issuedTableBody').innerHTML = output;
            } catch(e){
                document.getElementById('issuedTableBody').innerHTML = '<tr><td colspan="5">Error loading data</td></tr>';
            }
        }
    };
    xhr.send();
}

// Add click events
document.getElementById('totalBooksCard').addEventListener('click', loadBooks);
document.getElementById('availableCard').addEventListener('click', loadAvailableBooks);
document.getElementById('issuedCard').addEventListener('click', loadIssuedBooks);
</script>

<?php include 'includes/footer.php'; ?>
