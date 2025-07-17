<?php
/**
 * Database Content Translator
 * Automatically translates database content to selected language
 */

class DatabaseTranslator {
    private $pdo;
    private $current_language;
    private $google_translator;
    
    public function __construct($pdo, $language = 'en') {
        $this->pdo = $pdo;
        $this->current_language = $language;
        
        // Initialize Google Translator if available
        if (class_exists('GoogleTranslator')) {
            $this->google_translator = new GoogleTranslator($pdo, $language);
        }
        
        // Create translation cache table if it doesn't exist
        $this->createTranslationCacheTable();
    }
    
    /**
     * Create translation cache table
     */
    private function createTranslationCacheTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS translation_cache (
                id INT AUTO_INCREMENT PRIMARY KEY,
                original_text TEXT NOT NULL,
                translated_text TEXT NOT NULL,
                source_language VARCHAR(5) DEFAULT 'en',
                target_language VARCHAR(5) NOT NULL,
                table_name VARCHAR(100),
                field_name VARCHAR(100),
                record_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_translation (original_text(100), target_language),
                INDEX idx_record (table_name, field_name, record_id, target_language)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating translation cache table: " . $e->getMessage());
        }
    }
    
    /**
     * Translate text with caching
     */
    public function translateText($text, $target_language = null, $table_name = null, $field_name = null, $record_id = null) {
        if (empty($text)) return $text;
        
        $target_language = $target_language ?: $this->current_language;
        
        // If target language is English, return original text
        if ($target_language === 'en') {
            return $text;
        }
        
        // Check cache first
        $cached = $this->getCachedTranslation($text, $target_language, $table_name, $field_name, $record_id);
        if ($cached) {
            return $cached;
        }
        
        // Translate using Google Translator or fallback
        $translated = $this->performTranslation($text, $target_language);
        
        // Cache the translation
        $this->cacheTranslation($text, $translated, 'en', $target_language, $table_name, $field_name, $record_id);
        
        return $translated;
    }
    
    /**
     * Get cached translation
     */
    private function getCachedTranslation($text, $target_language, $table_name = null, $field_name = null, $record_id = null) {
        try {
            $sql = "SELECT translated_text FROM translation_cache 
                    WHERE original_text = ? AND target_language = ?";
            $params = [$text, $target_language];
            
            // Add specific record matching if provided
            if ($table_name && $field_name && $record_id) {
                $sql .= " AND table_name = ? AND field_name = ? AND record_id = ?";
                $params = array_merge($params, [$table_name, $field_name, $record_id]);
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_COLUMN);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error getting cached translation: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Perform actual translation
     */
    private function performTranslation($text, $target_language) {
        // Try Google Translator first
        if ($this->google_translator) {
            try {
                return $this->google_translator->translateText($text, $target_language);
            } catch (Exception $e) {
                error_log("Google Translator error: " . $e->getMessage());
            }
        }
        
        // Fallback to manual translation dictionary
        return $this->getFallbackTranslation($text, $target_language);
    }
    
    /**
     * Cache translation
     */
    private function cacheTranslation($original, $translated, $source_lang, $target_lang, $table_name = null, $field_name = null, $record_id = null) {
        try {
            $sql = "INSERT INTO translation_cache 
                    (original_text, translated_text, source_language, target_language, table_name, field_name, record_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original, $translated, $source_lang, $target_lang, $table_name, $field_name, $record_id]);
        } catch (PDOException $e) {
            error_log("Error caching translation: " . $e->getMessage());
        }
    }
    
    /**
     * Translate products array
     */
    public function translateProducts($products) {
        if (empty($products) || $this->current_language === 'en') {
            return $products;
        }
        
        foreach ($products as &$product) {
            $product['translated_name'] = $this->translateText(
                $product['name'], 
                $this->current_language, 
                'products', 
                'name', 
                $product['id']
            );
            
            $product['translated_description'] = $this->translateText(
                $product['description'], 
                $this->current_language, 
                'products', 
                'description', 
                $product['id']
            );
            
            // Translate category if exists
            if (isset($product['category'])) {
                $product['translated_category'] = $this->translateText(
                    $product['category'], 
                    $this->current_language, 
                    'products', 
                    'category', 
                    $product['id']
                );
            }
        }
        
        return $products;
    }
    
    /**
     * Translate categories array
     */
    public function translateCategories($categories) {
        if (empty($categories) || $this->current_language === 'en') {
            return $categories;
        }
        
        foreach ($categories as &$category) {
            $category['translated_name'] = $this->translateText(
                $category['name'], 
                $this->current_language, 
                'categories', 
                'name', 
                $category['id']
            );
            
            if (isset($category['description'])) {
                $category['translated_description'] = $this->translateText(
                    $category['description'], 
                    $this->current_language, 
                    'categories', 
                    'description', 
                    $category['id']
                );
            }
        }
        
        return $categories;
    }
    
    /**
     * Translate users array (names, descriptions, etc.)
     */
    public function translateUsers($users) {
        if (empty($users) || $this->current_language === 'en') {
            return $users;
        }
        
        foreach ($users as &$user) {
            // Translate business name if exists
            if (isset($user['business_name']) && !empty($user['business_name'])) {
                $user['translated_business_name'] = $this->translateText(
                    $user['business_name'], 
                    $this->current_language, 
                    'users', 
                    'business_name', 
                    $user['id']
                );
            }
            
            // Translate bio/description if exists
            if (isset($user['bio']) && !empty($user['bio'])) {
                $user['translated_bio'] = $this->translateText(
                    $user['bio'], 
                    $this->current_language, 
                    'users', 
                    'bio', 
                    $user['id']
                );
            }
        }
        
        return $users;
    }
    
    /**
     * Fallback translation dictionary
     */
    private function getFallbackTranslation($text, $target_language) {
        $translations = [
            'fr' => [
                'Electronics' => 'Électronique',
                'Clothing' => 'Vêtements',
                'Books' => 'Livres',
                'Home & Garden' => 'Maison et Jardin',
                'Sports' => 'Sports',
                'Toys' => 'Jouets',
                'Beauty' => 'Beauté',
                'Automotive' => 'Automobile',
                'Food' => 'Nourriture',
                'Health' => 'Santé'
            ],
            'rw' => [
                'Electronics' => 'Ibikoresho by\'ikoranabuhanga',
                'Clothing' => 'Imyambaro',
                'Books' => 'Ibitabo',
                'Home & Garden' => 'Inzu n\'ubusitani',
                'Sports' => 'Siporo',
                'Toys' => 'Ibikinisho',
                'Beauty' => 'Ubwiza',
                'Automotive' => 'Ibinyabiziga',
                'Food' => 'Ibiryo',
                'Health' => 'Ubuzima'
            ]
        ];
        
        return $translations[$target_language][$text] ?? $text;
    }
    
    /**
     * Clear translation cache for specific record
     */
    public function clearCache($table_name = null, $record_id = null) {
        try {
            if ($table_name && $record_id) {
                $sql = "DELETE FROM translation_cache WHERE table_name = ? AND record_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$table_name, $record_id]);
            } else {
                $sql = "DELETE FROM translation_cache WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
                $this->pdo->exec($sql);
            }
        } catch (PDOException $e) {
            error_log("Error clearing translation cache: " . $e->getMessage());
        }
    }
    
    /**
     * Translate orders array
     */
    public function translateOrders($orders) {
        if (empty($orders) || $this->current_language === 'en') {
            return $orders;
        }

        foreach ($orders as &$order) {
            // Translate product name if exists
            if (isset($order['product_name']) && !empty($order['product_name'])) {
                $order['translated_product_name'] = $this->translateText(
                    $order['product_name'],
                    $this->current_language,
                    'orders',
                    'product_name',
                    $order['id']
                );
            }

            // Translate status
            if (isset($order['status']) && !empty($order['status'])) {
                $order['translated_status'] = $this->translateText(
                    $order['status'],
                    $this->current_language,
                    'orders',
                    'status',
                    $order['id']
                );
            }

            // Translate notes if exists
            if (isset($order['notes']) && !empty($order['notes'])) {
                $order['translated_notes'] = $this->translateText(
                    $order['notes'],
                    $this->current_language,
                    'orders',
                    'notes',
                    $order['id']
                );
            }
        }

        return $orders;
    }

    /**
     * Translate any database result array
     */
    public function translateDatabaseResults($results, $table_name, $fields_to_translate = []) {
        if (empty($results) || $this->current_language === 'en') {
            return $results;
        }

        // Default fields to translate if none specified
        if (empty($fields_to_translate)) {
            $fields_to_translate = ['name', 'title', 'description', 'content', 'message', 'subject'];
        }

        foreach ($results as &$result) {
            foreach ($fields_to_translate as $field) {
                if (isset($result[$field]) && !empty($result[$field])) {
                    $translated_field = 'translated_' . $field;
                    $result[$translated_field] = $this->translateText(
                        $result[$field],
                        $this->current_language,
                        $table_name,
                        $field,
                        $result['id'] ?? null
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Translate single database record
     */
    public function translateRecord($record, $table_name, $fields_to_translate = []) {
        if (empty($record) || $this->current_language === 'en') {
            return $record;
        }

        // Default fields to translate if none specified
        if (empty($fields_to_translate)) {
            $fields_to_translate = ['name', 'title', 'description', 'content', 'message', 'subject'];
        }

        foreach ($fields_to_translate as $field) {
            if (isset($record[$field]) && !empty($record[$field])) {
                $translated_field = 'translated_' . $field;
                $record[$translated_field] = $this->translateText(
                    $record[$field],
                    $this->current_language,
                    $table_name,
                    $field,
                    $record['id'] ?? null
                );
            }
        }

        return $record;
    }

    /**
     * Get translation statistics
     */
    public function getTranslationStats() {
        try {
            $sql = "SELECT target_language, COUNT(*) as count FROM translation_cache GROUP BY target_language";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting translation stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Translate common status values
     */
    public function translateStatus($status) {
        $status_translations = [
            'fr' => [
                'active' => 'actif',
                'inactive' => 'inactif',
                'pending' => 'en attente',
                'completed' => 'terminé',
                'cancelled' => 'annulé',
                'processing' => 'en cours',
                'shipped' => 'expédié',
                'delivered' => 'livré',
                'approved' => 'approuvé',
                'rejected' => 'rejeté'
            ],
            'rw' => [
                'active' => 'gikora',
                'inactive' => 'kidakora',
                'pending' => 'gitegereje',
                'completed' => 'byarangiye',
                'cancelled' => 'byahagaritswe',
                'processing' => 'biratunganywa',
                'shipped' => 'byoherejwe',
                'delivered' => 'byageze',
                'approved' => 'byemewe',
                'rejected' => 'byanze'
            ]
        ];

        if ($this->current_language === 'en') {
            return $status;
        }

        return $status_translations[$this->current_language][strtolower($status)] ?? $status;
    }
}
?>
