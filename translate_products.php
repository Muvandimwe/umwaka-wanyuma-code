<?php
session_start();
include_once '../includes/db_config.php';

header('Content-Type: application/json');

// Get the target language
$target_language = $_GET['lang'] ?? $_SESSION['language'] ?? 'en';

// Validate language code
$supported_languages = ['en', 'fr', 'rw', 'sw', 'ar', 'es', 'pt', 'de', 'it', 'zh', 'ja', 'ko', 'hi', 'ur'];
if (!in_array($target_language, $supported_languages)) {
    $target_language = 'en';
}

try {
    // Get all products with translations
    $products = get_products($pdo, null, null, $target_language);
    
    // Format products for JSON response
    $formatted_products = [];
    foreach ($products as $product) {
        $formatted_products[] = [
            'id' => $product['id'],
            'name' => $product['translated_name'],
            'description' => $product['translated_description'],
            'price' => $product['price'],
            'stock_quantity' => $product['stock_quantity'],
            'category' => $product['category'],
            'image' => $product['image'],
            'seller_name' => $product['seller_full_name'],
            'featured' => $product['featured'],
            'created_at' => $product['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'language' => $target_language,
        'products' => $formatted_products,
        'total_products' => count($formatted_products)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
