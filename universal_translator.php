<?php
/**
 * Universal Database Translation System
 * Automatically translates ALL database content system-wide
 */

class UniversalTranslator {
    private $pdo;
    private $language;
    private $db_translator;
    
    // Tables and fields that should be translated
    private $translation_config = [
        'products' => ['name', 'description', 'category', 'brand', 'specifications'],
        'orders' => ['product_name', 'status', 'notes', 'delivery_notes'],
        'users' => ['full_name', 'business_name', 'bio', 'description', 'address'],
        'categories' => ['name', 'description'],
        'reviews' => ['title', 'comment', 'review_text'],
        'messages' => ['subject', 'message', 'content'],
        'notifications' => ['title', 'message', 'content'],
        'reports' => ['title', 'description', 'content'],
        'settings' => ['value', 'description'],
        'pages' => ['title', 'content', 'description'],
        'news' => ['title', 'content', 'summary'],
        'faqs' => ['question', 'answer'],
        'testimonials' => ['name', 'message', 'company']
    ];
    
    public function __construct($pdo, $language) {
        $this->pdo = $pdo;
        $this->language = $language;
        
        // Initialize database translator
        require_once 'database_translator.php';
        $this->db_translator = new DatabaseTranslator($pdo, $language);
    }
    
    /**
     * Automatically translate any database result
     */
    public function autoTranslate($data, $table_name = null) {
        if (empty($data) || $this->language === 'en') {
            return $data;
        }
        
        // Handle single record vs array of records
        $is_single_record = !isset($data[0]);
        $records = $is_single_record ? [$data] : $data;
        
        foreach ($records as &$record) {
            $record = $this->translateRecord($record, $table_name);
        }
        
        return $is_single_record ? $records[0] : $records;
    }
    
    /**
     * Translate a single record
     */
    private function translateRecord($record, $table_name = null) {
        if (empty($record)) return $record;
        
        // Auto-detect table name if not provided
        if (!$table_name) {
            $table_name = $this->detectTableName($record);
        }
        
        // Get fields to translate for this table
        $fields_to_translate = $this->getFieldsToTranslate($table_name, $record);
        
        // Translate each field
        foreach ($fields_to_translate as $field) {
            if (isset($record[$field]) && !empty($record[$field])) {
                $translated_field = 'translated_' . $field;
                $record[$translated_field] = $this->db_translator->translateText(
                    $record[$field],
                    $this->language,
                    $table_name,
                    $field,
                    $record['id'] ?? null
                );
            }
        }
        
        return $record;
    }
    
    /**
     * Detect table name from record structure
     */
    private function detectTableName($record) {
        // Common patterns to detect table names
        if (isset($record['product_id']) || isset($record['seller_id'])) {
            return 'products';
        }
        if (isset($record['order_id']) || isset($record['buyer_id'])) {
            return 'orders';
        }
        if (isset($record['user_type']) || isset($record['email'])) {
            return 'users';
        }
        if (isset($record['category_id']) || isset($record['parent_id'])) {
            return 'categories';
        }
        
        return 'general'; // Default table name
    }
    
    /**
     * Get fields to translate for a table
     */
    private function getFieldsToTranslate($table_name, $record) {
        // Get configured fields for this table
        $configured_fields = $this->translation_config[$table_name] ?? [];
        
        // Add any text fields found in the record
        $text_fields = [];
        foreach ($record as $field => $value) {
            if (is_string($value) && strlen($value) > 3 && !in_array($field, ['id', 'email', 'phone', 'password', 'created_at', 'updated_at'])) {
                if (preg_match('/^[a-zA-Z\s\-_.,!?]+$/', substr($value, 0, 50))) {
                    $text_fields[] = $field;
                }
            }
        }
        
        // Combine configured and detected fields
        return array_unique(array_merge($configured_fields, $text_fields));
    }
    
    /**
     * Translate query results automatically
     */
    public function translateQueryResults($sql, $params = [], $table_name = null) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->autoTranslate($results, $table_name);
            
        } catch (PDOException $e) {
            error_log("Error in translateQueryResults: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Enhanced product query with guaranteed translation
     */
    public function getProducts($where = "WHERE status = 'active'", $order = "ORDER BY created_at DESC", $limit = null) {
        $sql = "SELECT p.*, u.full_name as seller_name, u.business_name as seller_business 
                FROM products p 
                LEFT JOIN users u ON p.seller_id = u.id 
                $where $order";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->translateQueryResults($sql, [], 'products');
    }
    
    /**
     * Enhanced order query with guaranteed translation
     */
    public function getOrders($where = "", $order = "ORDER BY created_at DESC", $limit = null) {
        $sql = "SELECT o.*, p.name as product_name, p.description as product_description,
                       u1.full_name as buyer_name, u2.full_name as seller_name
                FROM orders o
                LEFT JOIN products p ON o.product_id = p.id
                LEFT JOIN users u1 ON o.buyer_id = u1.id
                LEFT JOIN users u2 ON o.seller_id = u2.id
                $where $order";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->translateQueryResults($sql, [], 'orders');
    }
    
    /**
     * Enhanced user query with guaranteed translation
     */
    public function getUsers($where = "", $order = "ORDER BY created_at DESC", $limit = null) {
        $sql = "SELECT * FROM users $where $order";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->translateQueryResults($sql, [], 'users');
    }
    
    /**
     * Get categories with translation
     */
    public function getCategories($where = "", $order = "ORDER BY name ASC", $limit = null) {
        $sql = "SELECT * FROM categories $where $order";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->translateQueryResults($sql, [], 'categories');
    }
    
    /**
     * Universal query method - translates any query result
     */
    public function query($sql, $params = [], $table_name = null) {
        return $this->translateQueryResults($sql, $params, $table_name);
    }
    
    /**
     * Translate dashboard statistics
     */
    public function getDashboardStats($user_id = null, $user_type = null) {
        $stats = [];
        
        if ($user_type === 'seller' && $user_id) {
            // Seller statistics
            $stats['products'] = $this->getProducts("WHERE p.seller_id = $user_id");
            $stats['orders'] = $this->getOrders("WHERE o.seller_id = $user_id", "ORDER BY o.created_at DESC", 10);
            
            // Product count
            $result = $this->pdo->query("SELECT COUNT(*) as count FROM products WHERE seller_id = $user_id AND status = 'active'")->fetch();
            $stats['product_count'] = $result['count'];
            
            // Order count
            $result = $this->pdo->query("SELECT COUNT(*) as count FROM orders WHERE seller_id = $user_id")->fetch();
            $stats['order_count'] = $result['count'];
            
            // Revenue
            $result = $this->pdo->query("SELECT SUM(total_price) as revenue FROM orders WHERE seller_id = $user_id AND status != 'cancelled'")->fetch();
            $stats['revenue'] = $result['revenue'] ?? 0;
            
        } elseif ($user_type === 'buyer' && $user_id) {
            // Buyer statistics
            $stats['orders'] = $this->getOrders("WHERE o.buyer_id = $user_id", "ORDER BY o.created_at DESC", 10);
            
            // Order count
            $result = $this->pdo->query("SELECT COUNT(*) as count FROM orders WHERE buyer_id = $user_id")->fetch();
            $stats['order_count'] = $result['count'];
            
        } elseif ($user_type === 'admin') {
            // Admin statistics
            $stats['products'] = $this->getProducts("", "ORDER BY created_at DESC", 10);
            $stats['orders'] = $this->getOrders("", "ORDER BY created_at DESC", 10);
            $stats['users'] = $this->getUsers("WHERE user_type IN ('seller', 'buyer')", "ORDER BY created_at DESC", 10);
            
            // Counts
            $stats['product_count'] = $this->pdo->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'")->fetch()['count'];
            $stats['order_count'] = $this->pdo->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'];
            $stats['user_count'] = $this->pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type IN ('seller', 'buyer')")->fetch()['count'];
        }
        
        return $stats;
    }
    
    /**
     * Batch translate all system data
     */
    public function batchTranslateSystem() {
        $results = [];
        
        try {
            // Translate products
            $products = $this->getProducts("", "ORDER BY created_at DESC", 100);
            $results['products'] = count($products);
            
            // Translate orders
            $orders = $this->getOrders("", "ORDER BY created_at DESC", 100);
            $results['orders'] = count($orders);
            
            // Translate users
            $users = $this->getUsers("WHERE user_type IN ('seller', 'buyer')", "ORDER BY created_at DESC", 100);
            $results['users'] = count($users);
            
            // Translate categories if table exists
            try {
                $categories = $this->getCategories();
                $results['categories'] = count($categories);
            } catch (Exception $e) {
                $results['categories'] = 0;
            }
            
        } catch (Exception $e) {
            error_log("Error in batch translation: " . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Get translation coverage report
     */
    public function getTranslationCoverage() {
        $coverage = [];
        
        foreach ($this->translation_config as $table => $fields) {
            try {
                $sql = "SELECT COUNT(*) as total FROM $table";
                $total = $this->pdo->query($sql)->fetch()['total'];
                
                $translated = 0;
                if ($total > 0) {
                    $sql = "SELECT COUNT(DISTINCT record_id) as translated 
                            FROM translation_cache 
                            WHERE table_name = ? AND target_language = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$table, $this->language]);
                    $translated = $stmt->fetch()['translated'] ?? 0;
                }
                
                $coverage[$table] = [
                    'total' => $total,
                    'translated' => $translated,
                    'percentage' => $total > 0 ? round(($translated / $total) * 100, 2) : 0
                ];
                
            } catch (Exception $e) {
                $coverage[$table] = ['total' => 0, 'translated' => 0, 'percentage' => 0];
            }
        }
        
        return $coverage;
    }
}

// Global universal translator instance
global $universal_translator;

/**
 * Initialize universal translator
 */
function initUniversalTranslator($pdo, $language) {
    global $universal_translator;
    
    if (!isset($universal_translator)) {
        $universal_translator = new UniversalTranslator($pdo, $language);
    }
    
    return $universal_translator;
}

/**
 * Universal translation function - translates any data
 */
function universalTranslate($data, $table_name = null, $pdo = null, $language = null) {
    global $lang;

    if (!$pdo) {
        global $pdo;
    }

    $language = $language ?: $lang;
    
    $translator = initUniversalTranslator($pdo, $language);
    return $translator->autoTranslate($data, $table_name);
}

/**
 * Universal query function - automatically translates results
 */
function universalQuery($sql, $params = [], $table_name = null, $pdo = null, $language = null) {
    global $lang;

    if (!$pdo) {
        global $pdo;
    }

    $language = $language ?: $lang;
    
    $translator = initUniversalTranslator($pdo, $language);
    return $translator->translateQueryResults($sql, $params, $table_name);
}
?>
