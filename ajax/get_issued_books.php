<?php
require_once '../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

try {
    // Query to get issued books with member information
    $stmt = $pdo->query("
        SELECT bi.id, bm.title, m.name as member_name, bi.issue_date, bi.due_date 
        FROM book_issues bi
        JOIN books_movies bm ON bi.book_id = bm.id
        JOIN memberships m ON bi.member_id = m.id
        WHERE bi.status = 'Active'
        ORDER BY bi.due_date
    ");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return as JSON
    echo json_encode($books);
} catch (Exception $e) {
    // Return error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>