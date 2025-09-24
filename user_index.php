<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's borrowed books
$stmt = $conn->prepare("SELECT b.title, b.author, i.issue_date, i.return_date, i.status 
                        FROM issued_books i 
                        JOIN books b ON i.book_id = b.id 
                        WHERE i.user_id = ? 
                        ORDER BY i.issue_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$borrowed_books = $result->fetch_all(MYSQLI_ASSOC);

// Get user's book requests
$stmt = $conn->prepare("SELECT b.title, b.author, r.request_date, r.status 
                        FROM book_requests r 
                        JOIN books b ON r.book_id = b.id 
                        WHERE r.user_id = ? 
                        ORDER BY r.request_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$book_requests = $result->fetch_all(MYSQLI_ASSOC);

// Get recommended books (example: most popular books)
$stmt = $conn->prepare("SELECT b.id, b.title, b.author, b.category, COUNT(i.id) as borrow_count 
                        FROM books b 
                        LEFT JOIN issued_books i ON b.id = i.book_id 
                        WHERE b.status = 'available' 
                        GROUP BY b.id 
                        ORDER BY borrow_count DESC 
                        LIMIT 4");
$stmt->execute();
$result = $stmt->get_result();
$recommended_books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --text-color: #2b2d42;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            color: white;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 250px;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid var(--accent-color);
        }
        
        .menu-item i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        .welcome-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome-text h1 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .welcome-text p {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0;
        }
        
        .action-buttons .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            margin-left: 10px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 15px 20px;
            border: none;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .book-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .book-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .book-cover {
            width: 60px;
            height: 80px;
            background: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            margin-right: 15px;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .book-info h5 {
            margin: 0 0 5px 0;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .book-info p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 5px;
        }
        
        .status-issued {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .status-returned {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .status-overdue {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .status-pending {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }
        
        .recommended-books {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        
        .book-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
        }
        
        .book-card-cover {
            height: 150px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .book-card-body {
            padding: 15px;
        }
        
        .book-card-body h5 {
            margin: 0 0 5px 0;
            font-weight: 600;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .book-card-body p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .book-card-footer {
            padding: 10px 15px;
            background: var(--light-color);
            display: flex;
            justify-content: space-between;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar-header h3, .menu-item span {
                display: none;
            }
            
            .menu-item i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                text-align: center;
            }
            
            .action-buttons {
                margin-top: 20px;
            }
            
            .action-buttons .btn {
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Library System</h3>
        </div>
        <div class="sidebar-menu">
            <a href="user_index.php" class="menu-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="browse_books.php" class="menu-item">
                <i class="fas fa-book"></i>
                <span>Browse Books</span>
            </a>
            <a href="my_books.php" class="menu-item">
                <i class="fas fa-bookmark"></i>
                <span>My Books</span>
            </a>
            <a href="book_request.php" class="menu-item">
                <i class="fas fa-paper-plane"></i>
                <span>Request Book</span>
            </a>
            <a href="profile.php" class="menu-item">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-text">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Explore books, manage your borrowings, and discover new reads.</p>
            </div>
            <div class="action-buttons">
                <a href="browse_books.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Find Books
                </a>
                <a href="book_request.php" class="btn btn-outline-primary">
                    <i class="fas fa-paper-plane"></i> Request Book
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Borrowed Books -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book-reader me-2"></i> My Borrowed Books
                    </div>
                    <div class="card-body">
                        <?php if (count($borrowed_books) > 0): ?>
                            <?php foreach ($borrowed_books as $book): ?>
                                <div class="book-item">
                                    <div class="book-cover">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="book-info">
                                        <h5><?php echo htmlspecialchars($book['title']); ?></h5>
                                        <p>By <?php echo htmlspecialchars($book['author']); ?></p>
                                        <p>Borrowed: <?php echo date('M d, Y', strtotime($book['issue_date'])); ?></p>
                                        <?php
                                        $status_class = '';
                                        switch ($book['status']) {
                                            case 'issued':
                                                $status_class = 'status-issued';
                                                break;
                                            case 'returned':
                                                $status_class = 'status-returned';
                                                break;
                                            case 'overdue':
                                                $status_class = 'status-overdue';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($book['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <p>You haven't borrowed any books yet.</p>
                                <a href="browse_books.php" class="btn btn-primary btn-sm">Browse Books</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Book Requests -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-paper-plane me-2"></i> My Book Requests
                    </div>
                    <div class="card-body">
                        <?php if (count($book_requests) > 0): ?>
                            <?php foreach ($book_requests as $request): ?>
                                <div class="book-item">
                                    <div class="book-cover">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="book-info">
                                        <h5><?php echo htmlspecialchars($request['title']); ?></h5>
                                        <p>By <?php echo htmlspecialchars($request['author']); ?></p>
                                        <p>Requested: <?php echo date('M d, Y', strtotime($request['request_date'])); ?></p>
                                        <span class="status-badge status-pending">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <p>You don't have any pending book requests.</p>
                                <a href="book_request.php" class="btn btn-primary btn-sm">Request a Book</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Books -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star me-2"></i> Recommended for You
            </div>
            <div class="card-body">
                <div class="recommended-books">
                    <?php if (count($recommended_books) > 0): ?>
                        <?php foreach ($recommended_books as $book): ?>
                            <div class="book-card">
                                <div class="book-card-cover">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="book-card-body">
                                    <h5 title="<?php echo htmlspecialchars($book['title']); ?>">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h5>
                                    <p><?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="badge bg-light text-dark"><?php echo htmlspecialchars($book['category']); ?></p>
                                </div>
                                <div class="book-card-footer">
                                    <a href="book_details.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                    <a href="request_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">Borrow</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p>No recommended books available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>