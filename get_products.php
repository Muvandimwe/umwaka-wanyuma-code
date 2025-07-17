<?php
/**
 * Real-Time Product API
 * Returns current product count and latest products for real-time updates
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
include_once '../includes/db_config.php';
include_once '../includes/language.php';
include_once '../includes/product_functions.php';

try {
    // Get current language
    $lang = $_SESSION['language'] ?? 'en';

    // Get all products with real-time data (including different statuses for debugging)
    $stmt = $pdo->prepare("
        SELECT p.*, u.full_name as seller_name, u.business_name as seller_business,
               u.phone as seller_phone, u.email as seller_email,
               p.created_at, p.updated_at, p.status as product_status,
               CASE
                   WHEN p.stock_quantity > 10 THEN 'in_stock'
                   WHEN p.stock_quantity > 0 THEN 'low_stock'
                   ELSE 'out_of_stock'
               END as stock_status
        FROM products p
        LEFT JOIN users u ON p.seller_id = u.id
        WHERE p.status IN ('active', 'pending', 'approved')
        ORDER BY p.created_at DESC, p.updated_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Apply translations if function exists
    if (function_exists('translateProducts')) {
        $translated_products = translateProducts($products, $lang);
    } else {
        $translated_products = $products;
    }

    // Get product statistics
    $total_products = count($products);
    $in_stock_products = count(array_filter($products, function($p) { return $p['stock_quantity'] > 0; }));
    $new_products_today = count(array_filter($products, function($p) {
        return strtotime($p['created_at']) > strtotime('-1 day');
    }));

    // Get latest product for real-time detection
    $latest_product = !empty($products) ? $products[0] : null;

    echo json_encode([
        'success' => true,
        'products' => $translated_products,
        'statistics' => [
            'total_products' => $total_products,
            'in_stock_products' => $in_stock_products,
            'new_products_today' => $new_products_today,
            'last_updated' => date('Y-m-d H:i:s')
        ],
        'latest_product' => $latest_product ? [
            'id' => $latest_product['id'],
            'name' => $latest_product['name'],
            'created_at' => $latest_product['created_at'],
            'seller_name' => $latest_product['seller_name']
        ] : null,
        'timestamp' => time()
    ]);

} catch (PDOException $e) {
    error_log("Product API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'products' => [],
        'statistics' => [
            'total_products' => 0,
            'in_stock_products' => 0,
            'new_products_today' => 0,
            'last_updated' => date('Y-m-d H:i:s')
        ],
        'latest_product' => null,
        'timestamp' => time()
    ]);
} catch (Exception $e) {
    error_log("Product API General Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred',
        'products' => [],
        'statistics' => [
            'total_products' => 0,
            'in_stock_products' => 0,
            'new_products_today' => 0,
            'last_updated' => date('Y-m-d H:i:s')
        ],
        'latest_product' => null,
        'timestamp' => time()
    ]);
}
?>
