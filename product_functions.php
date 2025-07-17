<?php
/**
 * Universal Product Functions
 * Provides consistent product retrieval and display across the entire system
 */

/**
 * Get all active products with seller information
 */
function getAllProducts($pdo, $limit = null, $category = null, $search = null) {
    try {
        $sql = "
            SELECT p.*, u.full_name as seller_name, u.business_name as seller_business,
                   u.phone as seller_phone, u.email as seller_email
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.status = 'active'
        ";
        
        $params = [];
        
        // Add category filter
        if ($category) {
            $sql .= " AND p.category = ?";
            $params[] = $category;
        }
        
        // Add search filter
        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?)";
            $search_term = "%{$search}%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        // Add limit
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

/**
 * Get featured products (first 8 products)
 */
function getFeaturedProducts($pdo) {
    return getAllProducts($pdo, 8);
}

/**
 * Get products by category
 */
function getProductsByCategory($pdo, $category, $limit = null) {
    return getAllProducts($pdo, $limit, $category);
}

/**
 * Search products
 */
function searchProducts($pdo, $search_term, $limit = null) {
    return getAllProducts($pdo, $limit, null, $search_term);
}

/**
 * Get product categories
 */
function getProductCategories($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT DISTINCT category 
            FROM products 
            WHERE status = 'active' AND category IS NOT NULL AND category != ''
            ORDER BY category
        ");
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get single product by ID
 */
function getProductById($pdo, $product_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, u.full_name as seller_name, u.business_name as seller_business,
                   u.phone as seller_phone, u.email as seller_email
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.id = ? AND p.status = 'active'
        ");
        
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
        return null;
    }
}

/**
 * Apply translations to products based on language
 */
function translateProducts($products, $language) {
    if (empty($products)) {
        return [];
    }
    
    $translated_products = [];
    
    foreach ($products as $product) {
        // Apply translation based on language
        if ($language === 'fr') {
            $product['translated_name'] = translateToFrench($product['name']);
            $product['translated_description'] = translateToFrench($product['description']);
            $product['translated_category'] = translateToFrench($product['category'] ?? '');
        } elseif ($language === 'rw') {
            $product['translated_name'] = translateToKinyarwanda($product['name']);
            $product['translated_description'] = translateToKinyarwanda($product['description']);
            $product['translated_category'] = translateToKinyarwanda($product['category'] ?? '');
        } else {
            // English (original)
            $product['translated_name'] = $product['name'];
            $product['translated_description'] = $product['description'];
            $product['translated_category'] = $product['category'] ?? '';
        }
        
        $translated_products[] = $product;
    }
    
    return $translated_products;
}

/**
 * Simple French translation function
 */
function translateToFrench($text) {
    $translations = [
        'Electronics' => 'Électronique',
        'Clothing' => 'Vêtements',
        'Books' => 'Livres',
        'Home & Garden' => 'Maison et Jardin',
        'Sports' => 'Sports',
        'Toys' => 'Jouets',
        'Beauty' => 'Beauté',
        'Automotive' => 'Automobile',
        'Food' => 'Nourriture',
        'Health' => 'Santé',
        'Phone' => 'Téléphone',
        'Laptop' => 'Ordinateur portable',
        'Shoes' => 'Chaussures',
        'Shirt' => 'Chemise',
        'Book' => 'Livre',
        'Computer' => 'Ordinateur',
        'Mobile' => 'Mobile',
        'Tablet' => 'Tablette'
    ];
    
    // Check for exact matches first
    if (isset($translations[$text])) {
        return $translations[$text];
    }
    
    // Check for partial matches
    foreach ($translations as $english => $french) {
        if (stripos($text, $english) !== false) {
            return str_ireplace($english, $french, $text);
        }
    }
    
    return $text; // Return original if no translation found
}

/**
 * Simple Kinyarwanda translation function
 */
function translateToKinyarwanda($text) {
    $translations = [
        'Electronics' => 'Ibikoresho by\'ikoranabuhanga',
        'Clothing' => 'Imyambaro',
        'Books' => 'Ibitabo',
        'Home & Garden' => 'Inzu n\'ubusitani',
        'Sports' => 'Siporo',
        'Toys' => 'Ibikinisho',
        'Beauty' => 'Ubwiza',
        'Automotive' => 'Ibinyabiziga',
        'Food' => 'Ibiryo',
        'Health' => 'Ubuzima',
        'Phone' => 'Telefoni',
        'Laptop' => 'Mudasobwa',
        'Shoes' => 'Inkweto',
        'Shirt' => 'Ishati',
        'Book' => 'Igitabo',
        'Computer' => 'Mudasobwa',
        'Mobile' => 'Telefoni',
        'Tablet' => 'Mudasobwa ntoya'
    ];
    
    // Check for exact matches first
    if (isset($translations[$text])) {
        return $translations[$text];
    }
    
    // Check for partial matches
    foreach ($translations as $english => $kinyarwanda) {
        if (stripos($text, $english) !== false) {
            return str_ireplace($english, $kinyarwanda, $text);
        }
    }
    
    return $text; // Return original if no translation found
}

/**
 * Format product price
 */
function formatPrice($price, $currency = 'FRW') {
    return number_format($price) . ' ' . $currency;
}

/**
 * Get product image URL
 */
function getProductImageUrl($image_filename) {
    if ($image_filename && file_exists('uploads/products/' . $image_filename)) {
        return 'uploads/products/' . $image_filename;
    }
    return 'assets/images/no-image.png';
}

/**
 * Check if product is in stock
 */
function isInStock($product) {
    return isset($product['stock_quantity']) && $product['stock_quantity'] > 0;
}

/**
 * Get stock status text
 */
function getStockStatus($product, $language = 'en') {
    $stock_quantity = $product['stock_quantity'] ?? 0;
    
    $status_texts = [
        'en' => [
            'in_stock' => 'In Stock',
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock'
        ],
        'fr' => [
            'in_stock' => 'En Stock',
            'low_stock' => 'Stock Faible',
            'out_of_stock' => 'Rupture de Stock'
        ],
        'rw' => [
            'in_stock' => 'Hari mu bubiko',
            'low_stock' => 'Bubiko buke',
            'out_of_stock' => 'Nta bubiko'
        ]
    ];
    
    $texts = $status_texts[$language] ?? $status_texts['en'];
    
    if ($stock_quantity > 5) {
        return $texts['in_stock'];
    } elseif ($stock_quantity > 0) {
        return $texts['low_stock'];
    } else {
        return $texts['out_of_stock'];
    }
}

/**
 * Get product statistics
 */
function getProductStats($pdo) {
    try {
        $stats = [];
        
        // Total products
        $stmt = $pdo->query("SELECT COUNT(*) FROM products");
        $stats['total_products'] = $stmt->fetchColumn();
        
        // Active products
        $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'");
        $stats['active_products'] = $stmt->fetchColumn();
        
        // Products by category
        $stmt = $pdo->query("
            SELECT category, COUNT(*) as count 
            FROM products 
            WHERE status = 'active' 
            GROUP BY category 
            ORDER BY count DESC
        ");
        $stats['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Error fetching product stats: " . $e->getMessage());
        return [
            'total_products' => 0,
            'active_products' => 0,
            'categories' => []
        ];
    }
}

// isInStock() function already exists above at line 252

// getProductImageUrl() function already exists above at line 242
?>
