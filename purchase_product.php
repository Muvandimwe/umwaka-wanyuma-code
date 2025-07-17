<?php
session_start();
include_once '../includes/db_config.php';
include_once '../includes/language.php';

header('Content-Type: application/json');

// Check if user is logged in and is a buyer
if (!is_logged_in() || $_SESSION['user_type'] !== 'buyer') {
    echo json_encode(['success' => false, 'message' => 'Please log in as a buyer to make purchases.']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || $input['action'] !== 'purchase') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$product_id = intval($input['product_id'] ?? 0);
$quantity = intval($input['quantity'] ?? 1);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit();
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0.']);
    exit();
}

try {
    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found or not available.']);
        exit();
    }
    
    // Check stock availability
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode([
            'success' => false, 
            'message' => "Not enough stock available. Available: {$product['stock_quantity']}, Requested: {$quantity}"
        ]);
        exit();
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Calculate total price
    $total_price = $product['price'] * $quantity;
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (buyer_id, product_id, seller_id, quantity, total_price, status, order_date) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $product_id,
        $product['seller_id'],
        $quantity,
        $total_price
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    // Update product stock
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    $stmt->execute([$quantity, $product_id]);
    
    // Get new stock quantity
    $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $new_stock = $stmt->fetch()['stock_quantity'];
    
    // Create notification for seller
    $notification_message = "New order received! Product: {$product['name']}, Quantity: {$quantity}, Total: " . number_format($total_price, 0) . " RWF";
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, message, order_id, created_at) 
        VALUES (?, 'new_order', ?, ?, NOW())
    ");
    $stmt->execute([$product['seller_id'], $notification_message, $order_id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'total_price' => number_format($total_price, 0) . ' RWF',
        'quantity' => $quantity,
        'new_stock' => $new_stock,
        'redirect_url' => "order_confirmation.php?order_id={$order_id}"
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order. Please try again.'
    ]);
}
?>
