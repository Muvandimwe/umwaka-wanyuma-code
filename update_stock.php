<?php
session_start();
include_once '../includes/db_config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$product_id = $input['product_id'] ?? null;
$quantity = $input['quantity'] ?? 1;
$action = $input['action'] ?? 'purchase'; // purchase, check_stock

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

try {
    switch ($action) {
        case 'check_stock':
            // Get current stock
            $current_stock = get_product_stock($pdo, $product_id);
            echo json_encode([
                'success' => true, 
                'stock' => $current_stock,
                'available' => $current_stock > 0
            ]);
            break;
            
        case 'purchase':
            // Check if enough stock is available
            $current_stock = get_product_stock($pdo, $product_id);
            
            if ($current_stock < $quantity) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Insufficient stock available',
                    'available_stock' => $current_stock
                ]);
                exit();
            }
            
            // Get product details for order
            $product = get_product_by_id($pdo, $product_id);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit();
            }
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Update stock
                $stock_updated = update_product_stock($pdo, $product_id, $quantity);
                
                if (!$stock_updated) {
                    throw new Exception('Failed to update stock');
                }
                
                // Create order
                $total_price = $product['price'] * $quantity;
                $sql = "INSERT INTO orders (buyer_id, product_id, seller_id, quantity, total_price, status) 
                        VALUES (?, ?, ?, ?, ?, 'pending')";
                $stmt = $pdo->prepare($sql);
                $order_created = $stmt->execute([
                    $_SESSION['user_id'], 
                    $product_id, 
                    $product['seller_id'], 
                    $quantity, 
                    $total_price
                ]);
                
                if (!$order_created) {
                    throw new Exception('Failed to create order');
                }
                
                $order_id = $pdo->lastInsertId();
                
                // Commit transaction
                $pdo->commit();
                
                // Get updated stock
                $new_stock = get_product_stock($pdo, $product_id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'order_id' => $order_id,
                    'new_stock' => $new_stock,
                    'quantity_purchased' => $quantity,
                    'total_price' => $total_price,
                    'redirect_url' => "order_confirmation.php?order_id=$order_id"
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
