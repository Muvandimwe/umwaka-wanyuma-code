<?php
session_start();
include_once '../includes/db_config.php';
include_once '../includes/language.php';

header('Content-Type: application/json');

try {
    $search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($search_term)) {
        echo json_encode([
            'success' => false,
            'message' => 'Search term is required'
        ]);
        exit;
    }
    
    // Search in products and product translations
    $sql = "
        SELECT DISTINCT p.*, u.username as seller_name 
        FROM products p 
        JOIN users u ON p.seller_id = u.id 
        LEFT JOIN product_translations pt ON p.id = pt.product_id 
        WHERE p.status = 'active' 
        AND (
            p.name LIKE ? 
            OR p.description LIKE ? 
            OR p.category LIKE ?
            OR pt.name LIKE ?
            OR pt.description LIKE ?
        )
        ORDER BY p.featured DESC, p.created_at DESC 
        LIMIT 50
    ";
    
    $search_pattern = '%' . $search_term . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $search_pattern,
        $search_pattern,
        $search_pattern,
        $search_pattern,
        $search_pattern
    ]);
    
    $products = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'search_term' => $search_term
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error searching products: ' . $e->getMessage()
    ]);
}
?>
