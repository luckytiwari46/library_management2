<?php
require_once '../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

try {
    // Query to get available books/movies
    $stmt = $pdo->query("SELECT id, title, author, category, type FROM books_movies WHERE status = 'Available' ORDER BY id");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return as JSON
    echo json_encode($books);
} catch (Exception $e) {
    // Return error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>