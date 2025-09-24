-- Library Management System Database Setup
CREATE DATABASE IF NOT EXISTS library_management;
USE library_managements;

-- Users table (for admin and regular users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'user') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books/Movies table
CREATE TABLE IF NOT EXISTS books_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_no VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category ENUM('Science', 'Economics', 'Fiction', 'Children', 'Personal Development') NOT NULL,
    type ENUM('Book', 'Movie') NOT NULL,
    status ENUM('Available', 'Issued', 'Lost') DEFAULT 'Available',
    cost DECIMAL(10,2),
    procurement_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Memberships table
CREATE TABLE IF NOT EXISTS memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membership_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    contact_number VARCHAR(15),
    contact_address TEXT,
    aadhar_card_no VARCHAR(12),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    amount_pending DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Book/Movie Issues table
CREATE TABLE IF NOT EXISTS book_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_no VARCHAR(20) NOT NULL,
    membership_id VARCHAR(20) NOT NULL,
    issue_date DATE NOT NULL,
    expected_return_date DATE NOT NULL,
    actual_return_date DATE NULL,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    fine_paid BOOLEAN DEFAULT FALSE,
    remarks TEXT,
    status ENUM('Active', 'Returned', 'Overdue') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (serial_no) REFERENCES books_movies(serial_no),
    FOREIGN KEY (membership_id) REFERENCES memberships(membership_id)
);

-- Issue requests table
CREATE TABLE IF NOT EXISTS issue_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membership_id VARCHAR(20) NOT NULL,
    book_name VARCHAR(200) NOT NULL,
    requested_date DATE NOT NULL,
    request_fulfilled_date DATE NULL,
    status ENUM('Pending', 'Fulfilled', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (membership_id) REFERENCES memberships(membership_id)
);

-- Insert default admin user
INSERT INTO users (username, password, user_type, full_name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'admin@library.com');

-- Insert sample books
INSERT INTO books_movies (serial_no, name, author, category, type, status, cost, procurement_date) VALUES
('SC(B)000001', 'Introduction to Physics', 'Albert Einstein', 'Science', 'Book', 'Available', 500.00, '2024-01-15'),
('SC(B)000002', 'Chemistry Basics', 'Marie Curie', 'Science', 'Book', 'Available', 450.00, '2024-01-20'),
('EC(B)000001', 'Economic Principles', 'Adam Smith', 'Economics', 'Book', 'Available', 600.00, '2024-02-01'),
('EC(B)000002', 'Market Analysis', 'John Keynes', 'Economics', 'Book', 'Available', 550.00, '2024-02-05'),
('FC(B)000001', 'The Great Novel', 'Jane Austen', 'Fiction', 'Book', 'Available', 400.00, '2024-02-10'),
('CH(B)000001', 'Children Stories', 'Aesop', 'Children', 'Book', 'Available', 300.00, '2024-02-15'),
('PD(B)000001', 'Self Development', 'Tony Robbins', 'Personal Development', 'Book', 'Available', 650.00, '2024-02-20');

-- Insert sample movies
INSERT INTO books_movies (serial_no, name, author, category, type, status, cost, procurement_date) VALUES
('SC(M)000001', 'Cosmos Documentary', 'Carl Sagan', 'Science', 'Movie', 'Available', 800.00, '2024-01-25'),
('EC(M)000001', 'Economic Crisis Explained', 'Michael Moore', 'Economics', 'Movie', 'Available', 700.00, '2024-02-08'),
('FC(M)000001', 'The Great Adventure', 'Steven Spielberg', 'Fiction', 'Movie', 'Available', 900.00, '2024-02-12'),
('CH(M)000001', 'Kids Entertainment', 'Walt Disney', 'Children', 'Movie', 'Available', 600.00, '2024-02-18'),
('PD(M)000001', 'Motivational Series', 'Oprah Winfrey', 'Personal Development', 'Movie', 'Available', 750.00, '2024-02-25');

-- Insert sample memberships
INSERT INTO memberships (membership_id, first_name, last_name, contact_number, contact_address, aadhar_card_no, start_date, end_date, status) VALUES
('MEM001', 'John', 'Doe', '9876543210', '123 Main Street, City', '123456789012', '2024-01-01', '2024-07-01', 'Active'),
('MEM002', 'Jane', 'Smith', '9876543211', '456 Oak Avenue, Town', '123456789013', '2024-01-15', '2025-01-15', 'Active'),
('MEM003', 'Bob', 'Johnson', '9876543212', '789 Pine Road, Village', '123456789014', '2024-02-01', '2024-08-01', 'Active');
