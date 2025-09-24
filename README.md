# Library Management System

A comprehensive Library Management System built with PHP, HTML, CSS, JavaScript, jQuery, and Bootstrap. This system provides complete functionality for managing books, movies, memberships, and library transactions.

## Features

### 🔐 Authentication System
- **Admin Login**: Full access to all modules including maintenance
- **User Login**: Access to reports and transactions only
- **Secure Password Handling**: Passwords are hashed and hidden during input

### 📚 Book & Movie Management
- **Availability Search**: Search books/movies by name or author
- **Issue System**: Issue books with automatic validation
- **Return System**: Return books with fine calculation
- **Fine Payment**: Process overdue fine payments

### 👥 Membership Management
- **Add Membership**: Create new memberships (6 months, 1 year, 2 years)
- **Update Membership**: Extend or cancel existing memberships
- **Member Tracking**: Track membership status and pending fines

### 📊 Comprehensive Reports
- **Master Lists**: Books, Movies, and Memberships
- **Active Issues**: Currently issued books/movies
- **Overdue Returns**: Books/movies past return date with fine calculations
- **Issue Requests**: Pending and fulfilled requests
- **Export Functionality**: Print and Excel export options

### 🛠️ Admin Maintenance (Admin Only)
- **User Management**: Add and update system users
- **Book/Movie Management**: Add and update library items
- **Membership Management**: Full membership lifecycle management

### ✅ Validation & Security
- **Form Validations**: Comprehensive client and server-side validation
- **Date Validations**: Issue date cannot be in the past, return date auto-calculated
- **Access Control**: Role-based access to different modules
- **Error Handling**: User-friendly error messages and success notifications

## Installation & Setup

### Prerequisites
- **XAMPP** (Apache, MySQL, PHP)
- **Web Browser** (Chrome, Firefox, Safari, Edge)

### Step 1: Database Setup
1. Start XAMPP and ensure Apache and MySQL are running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `library_management`
4. Import the SQL file:
   ```sql
   -- Run the contents of sql/database_setup.sql
   ```

### Step 2: File Setup
1. Copy all project files to your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\library_management2\
   ```

2. Update database configuration if needed:
   - Edit `config/database.php` if your MySQL credentials are different
   - Default: host='localhost', username='root', password='' (empty)

### Step 3: Access the System
1. Open your web browser
2. Navigate to: `http://localhost/library_management2/`
3. Use the default login credentials:
   - **Admin**: username: `admin`, password: `password`
   - **User**: Create a new user through the admin panel

## Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `password`
- **Access**: Full system access including maintenance

### Creating New Users
1. Login as admin
2. Go to Maintenance → User Management → Add User
3. Create new admin or regular user accounts

## System Navigation

### Main Modules
1. **Dashboard**: Overview with statistics and quick access
2. **Transactions**: Book availability, issue, return, and fine payment
3. **Reports**: All reporting functionality with export options
4. **Maintenance**: Admin-only system management tools

### Navigation Chart
Access the complete navigation chart at: `http://localhost/library_management2/chart.html`

## Key Validation Rules

### Book Issue
- Issue date cannot be earlier than today
- Return date automatically set to 15 days from issue date
- Book name is required
- Author name auto-populated and non-editable

### Book Return
- Serial number is mandatory
- Issue date auto-populated and non-editable
- Return date can be edited
- Automatic fine calculation for overdue returns (Rs. 10/day)

### Fine Payment
- Fine amount calculated automatically
- Fine must be paid before completing return transaction
- Confirmation required for all transactions

### Membership Management
- All fields mandatory for new memberships
- Default duration: 6 months
- Membership extension: 6 months or 1 year
- Membership cancellation sets end date to today

## File Structure

```
library_management2/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php             # Authentication functions
│   ├── header.php           # Common header
│   └── footer.php           # Common footer
├── sql/
│   └── database_setup.sql   # Database schema and sample data
├── index.php                # Login page
├── admin_dashboard.php      # Admin dashboard
├── user_dashboard.php       # User dashboard
├── transactions.php         # Transactions module
├── book_availability.php    # Book search functionality
├── book_issue.php          # Book issue form
├── book_return.php         # Book return form
├── pay_fine.php            # Fine payment system
├── reports.php             # Reports module
├── report_*.php            # Individual report pages
├── maintenance.php         # Maintenance module (admin only)
├── maintenance_*.php       # Maintenance forms
├── chart.html              # Navigation chart
├── logout.php              # Logout functionality
└── README.md               # This file
```

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **UI Framework**: Bootstrap 5.1.3
- **Icons**: Font Awesome 6.0.0
- **Charts**: Chart.js (for future enhancements)
- **Excel Export**: SheetJS

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Security Features

- **Password Hashing**: Using PHP's `password_hash()` function
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: HTML escaping on all user inputs
- **Session Management**: Secure session handling
- **Access Control**: Role-based permissions

## Sample Data

The system comes with sample data including:
- 10 books across different categories (Science, Economics, Fiction, Children, Personal Development)
- 5 movies in various categories
- 3 sample memberships
- 1 default admin account

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running in XAMPP
   - Check database credentials in `config/database.php`
   - Verify database `library_management` exists

2. **Login Issues**
   - Use default admin credentials: admin/password
   - Check if users table has data
   - Verify password hashing is working

3. **File Permission Errors**
   - Ensure web server has read access to all files
   - Check PHP error logs in XAMPP

4. **JavaScript Errors**
   - Ensure all CDN resources are loading
   - Check browser console for errors
   - Verify jQuery is loading before custom scripts

### Support

For technical support or feature requests, please refer to the navigation chart or contact the development team.

## License

This project is developed for educational and demonstration purposes.

---

**Note**: This is a complete Library Management System implementation following all the specified requirements including form validations, user access controls, fine calculations, and comprehensive reporting functionality.
