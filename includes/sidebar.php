<?php
// Get current page for highlighting active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>Library System</h3>
    </div>
    <div class="sidebar-menu">
        <a class="menu-item <?php echo ($current_page === 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Statistics Links -->
        <div class="menu-section">
            <h6>Statistics</h6>
        </div>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_master_books.php') ? 'active' : ''; ?>" href="report_master_books.php">
            <i class="fas fa-book"></i>
            <span>Total Books/Movies</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_master_books.php') ? 'active' : ''; ?>" href="add_book.php">
           <i class="fas fa-plus"></i>
            <span>add Book</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'book_availability.php') ? 'active' : ''; ?>" href="book_availability.php">
            <i class="fas fa-check-circle"></i>
            <span>Available Books</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_active_issues.php') ? 'active' : ''; ?>" href="report_active_issues.php">
            <i class="fas fa-hand-holding"></i>
            <span>Issued Books</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_master_memberships.php') ? 'active' : ''; ?>" href="report_master_memberships.php">
            <i class="fas fa-users"></i>
            <span>Active Members</span>
        </a>
         <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_master_memberships.php') ? 'active' : ''; ?>" href="add_membership.php">
            <i class="fas fa-user-plus"></i>
            <span>Add Members</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_overdue_returns.php') ? 'active' : ''; ?>" href="report_overdue_returns.php">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Overdue Returns</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'report_issue_requests.php') ? 'active' : ''; ?>" href="report_issue_requests.php">
            <i class="fas fa-clock"></i>
            <span>Pending Requests</span>
        </a>
        
        <!-- Main Navigation -->
        <div class="menu-section">
            <h6>Main Menu</h6>
        </div>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'maintenance.php') ? 'active' : ''; ?>" href="maintenance.php">
            <i class="fas fa-tools"></i>
            <span>Maintenance</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'reports.php') ? 'active' : ''; ?>" href="reports.php">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'transactions.php') ? 'active' : ''; ?>" href="transactions.php">
            <i class="fas fa-exchange-alt"></i>
            <span>Transactions</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'user_signups.php') ? 'active' : ''; ?>" href="user_signups.php">
            <i class="fas fa-user-plus"></i>
            <span>User Signups</span>
        </a>
        <a class="menu-item btn btn-sidebar <?php echo ($current_page === 'user_dashboard.php') ? 'active' : ''; ?>" href="user_dashboard.php">
            <i class="fas fa-home"></i>
            <span>User Home</span>
        </a>
        <a class="menu-item btn btn-sidebar btn-danger" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
    /* Sidebar Styles */
    .sidebar {
        background: linear-gradient(180deg, #3a4f7a 0%, #2c3e50 100%);
        min-height: 100vh;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        color: white;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: all 0.3s;
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
        padding: 15px 0;
        overflow-y: auto;
        max-height: calc(100vh - 80px);
    }
    
    .menu-section {
        padding: 10px 20px 5px;
        opacity: 0.7;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .menu-section h6 {
        margin: 0;
        font-weight: 600;
    }
    
    .menu-item {
        padding: 10px 20px;
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    
    .menu-item.active {
        background-color: rgba(255, 255, 255, 0.2);
        border-left: 4px solid #3498db;
        color: white;
    }
    
    .menu-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-left: 4px solid #3498db;
    }
    
    .btn-sidebar {
        text-align: left;
        margin: 2px 10px;
        border-radius: 5px;
        transition: all 0.3s;
        border: none;
        padding: 8px 15px;
    }
    
    .btn-sidebar:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .btn-sidebar.active {
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    
    .menu-item i {
        margin-right: 15px;
        width: 20px;
        text-align: center;
    }
    
    /* Main Content Adjustment */
    .main-content {
        margin-left: 250px;
        padding: 20px;
        transition: all 0.3s;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
        }
        
        .sidebar-header h3, .menu-item span, .menu-section {
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
</style>