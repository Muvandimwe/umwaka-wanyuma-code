<?php
/**
 * Global Translation Helper
 * Provides system-wide database translation functionality
 */

// Global database translator instance
global $global_db_translator;

/**
 * Initialize global translator
 */
function initializeGlobalTranslator($pdo, $language) {
    global $global_db_translator;
    
    if (!isset($global_db_translator)) {
        require_once 'database_translator.php';
        $global_db_translator = new DatabaseTranslator($pdo, $language);
    }
    
    return $global_db_translator;
}

/**
 * Get translated field value
 */
function getTranslatedField($record, $field, $fallback = '') {
    $translated_field = 'translated_' . $field;
    
    if (isset($record[$translated_field]) && !empty($record[$translated_field])) {
        return $record[$translated_field];
    }
    
    if (isset($record[$field]) && !empty($record[$field])) {
        return $record[$field];
    }
    
    return $fallback;
}

// translateProducts() function is now provided by includes/product_functions.php

/**
 * Translate orders for any page
 */
function translateOrders($orders, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateOrders($orders);
}

/**
 * Translate categories for any page
 */
function translateCategories($categories, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateCategories($categories);
}

/**
 * Translate users for any page
 */
function translateUsers($users, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateUsers($users);
}

/**
 * Translate any database results
 */
function translateDatabaseResults($results, $table_name, $fields, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateDatabaseResults($results, $table_name, $fields);
}

/**
 * Translate single record
 */
function translateRecord($record, $table_name, $fields, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateRecord($record, $table_name, $fields);
}

/**
 * Translate status values
 */
function translateStatus($status, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateStatus($status);
}

/**
 * Quick translate text
 */
function quickTranslate($text, $pdo, $language, $table = null, $field = null, $id = null) {
    $translator = initializeGlobalTranslator($pdo, $language);
    return $translator->translateText($text, $language, $table, $field, $id);
}

/**
 * Auto-translate query results
 * This function automatically translates database query results
 */
function autoTranslateQuery($sql, $params, $pdo, $language, $table_name, $fields_to_translate = []) {
    try {
        // Execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Translate the results
        return translateDatabaseResults($results, $table_name, $fields_to_translate, $pdo, $language);
        
    } catch (PDOException $e) {
        error_log("Error in autoTranslateQuery: " . $e->getMessage());
        return [];
    }
}

/**
 * Enhanced product query with translation
 */
function getTranslatedProducts($where_clause = "WHERE status = 'active'", $order_clause = "ORDER BY created_at DESC", $limit = null, $pdo = null, $language = null) {
    global $lang;

    if (!$pdo) {
        global $pdo;
    }

    $language = $language ?: $lang;
    
    $sql = "SELECT p.*, u.full_name as seller_name FROM products p 
            LEFT JOIN users u ON p.seller_id = u.id 
            $where_clause $order_clause";
    
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    return autoTranslateQuery($sql, [], $pdo, $language, 'products', ['name', 'description', 'category']);
}

/**
 * Enhanced order query with translation
 */
function getTranslatedOrders($where_clause = "", $order_clause = "ORDER BY created_at DESC", $limit = null, $pdo = null, $language = null) {
    global $lang;

    if (!$pdo) {
        global $pdo;
    }

    $language = $language ?: $lang;
    
    $sql = "SELECT o.*, p.name as product_name, u.full_name as buyer_name 
            FROM orders o 
            LEFT JOIN products p ON o.product_id = p.id 
            LEFT JOIN users u ON o.buyer_id = u.id 
            $where_clause $order_clause";
    
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    return autoTranslateQuery($sql, [], $pdo, $language, 'orders', ['product_name', 'status', 'notes']);
}

/**
 * Enhanced user query with translation
 */
function getTranslatedUsers($where_clause = "", $order_clause = "ORDER BY created_at DESC", $limit = null, $pdo = null, $language = null) {
    global $lang;

    if (!$pdo) {
        global $pdo;
    }

    $language = $language ?: $lang;
    
    $sql = "SELECT * FROM users $where_clause $order_clause";
    
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    return autoTranslateQuery($sql, [], $pdo, $language, 'users', ['full_name', 'business_name', 'bio']);
}

/**
 * Translate dashboard statistics
 */
function translateDashboardStats($stats, $pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    
    // Translate status labels in statistics
    if (isset($stats['order_statuses'])) {
        foreach ($stats['order_statuses'] as &$status_stat) {
            $status_stat['translated_status'] = $translator->translateStatus($status_stat['status']);
        }
    }
    
    return $stats;
}

/**
 * Get system-wide translation statistics
 */
function getSystemTranslationStats($pdo) {
    $translator = initializeGlobalTranslator($pdo, 'en'); // Language doesn't matter for stats
    return $translator->getTranslationStats();
}

/**
 * Clear translation cache for specific table/record
 */
function clearTranslationCache($table_name = null, $record_id = null, $db_connection = null) {
    if (!$db_connection) {
        global $pdo;
        $db_connection = $pdo;
    }

    $translator = initializeGlobalTranslator($db_connection, 'en');
    return $translator->clearCache($table_name, $record_id);
}

/**
 * Batch translate multiple tables
 */
function batchTranslateSystem($pdo, $language) {
    $translator = initializeGlobalTranslator($pdo, $language);
    
    $results = [
        'products' => 0,
        'orders' => 0,
        'users' => 0,
        'categories' => 0
    ];
    
    try {
        // Pre-translate popular products
        $products = $pdo->query("SELECT * FROM products WHERE status = 'active' LIMIT 50")->fetchAll();
        $translator->translateProducts($products);
        $results['products'] = count($products);
        
        // Pre-translate recent orders
        $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 50")->fetchAll();
        $translator->translateOrders($orders);
        $results['orders'] = count($orders);
        
        // Pre-translate active users
        $users = $pdo->query("SELECT * FROM users WHERE user_type IN ('seller', 'buyer') LIMIT 50")->fetchAll();
        $translator->translateUsers($users);
        $results['users'] = count($users);
        
    } catch (Exception $e) {
        error_log("Error in batch translation: " . $e->getMessage());
    }
    
    return $results;
}
?>
