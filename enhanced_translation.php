<?php
/**
 * Enhanced Multilingual Translation System
 * Provides comprehensive language support for the entire platform
 */

class EnhancedTranslationSystem {
    private $pdo;
    private $supported_languages;
    private $current_language;
    private $google_translate_api;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->supported_languages = [
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'code' => 'en'],
            'rw' => ['name' => 'Kinyarwanda', 'flag' => '🇷🇼', 'code' => 'rw'],
            'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'code' => 'fr'],
            'sw' => ['name' => 'Kiswahili', 'flag' => '🇹🇿', 'code' => 'sw'],
            'ar' => ['name' => 'العربية', 'flag' => '🇸🇦', 'code' => 'ar'],
            'es' => ['name' => 'Español', 'flag' => '🇪🇸', 'code' => 'es'],
            'pt' => ['name' => 'Português', 'flag' => '🇵🇹', 'code' => 'pt'],
            'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪', 'code' => 'de'],
            'it' => ['name' => 'Italiano', 'flag' => '🇮🇹', 'code' => 'it'],
            'zh' => ['name' => '中文', 'flag' => '🇨🇳', 'code' => 'zh'],
            'ja' => ['name' => '日本語', 'flag' => '🇯🇵', 'code' => 'ja'],
            'ko' => ['name' => '한국어', 'flag' => '🇰🇷', 'code' => 'ko'],
            'hi' => ['name' => 'हिन्दी', 'flag' => '🇮🇳', 'code' => 'hi'],
            'ur' => ['name' => 'اردو', 'flag' => '🇵🇰', 'code' => 'ur']
        ];
        $this->current_language = $_SESSION['language'] ?? 'en';
    }
    
    /**
     * Set current language and update session
     */
    public function setLanguage($language_code) {
        if (isset($this->supported_languages[$language_code])) {
            $this->current_language = $language_code;
            $_SESSION['language'] = $language_code;
            
            // Trigger automatic translation of all content
            $this->translateAllContent($language_code);
            return true;
        }
        return false;
    }
    
    /**
     * Get current language
     */
    public function getCurrentLanguage() {
        return $this->current_language;
    }
    
    /**
     * Get all supported languages
     */
    public function getSupportedLanguages() {
        return $this->supported_languages;
    }
    
    /**
     * Translate all platform content to target language
     */
    public function translateAllContent($target_language) {
        if ($target_language === 'en') return; // English is the base language
        
        // Translate products
        $this->translateAllProducts($target_language);
        
        // Translate categories
        $this->translateAllCategories($target_language);
        
        // Translate user-generated content
        $this->translateUserContent($target_language);
        
        // Translate orders and transactions
        $this->translateOrderContent($target_language);
    }
    
    /**
     * Translate all products to target language
     */
    private function translateAllProducts($target_language) {
        // Get products that need translation
        $sql = "SELECT p.id, p.name, p.description, p.category 
                FROM products p 
                LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                WHERE pt.id IS NULL AND p.status = 'active'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$target_language]);
        $products = $stmt->fetchAll();
        
        foreach ($products as $product) {
            $translated_name = $this->translateText($product['name'], $target_language);
            $translated_description = $this->translateText($product['description'], $target_language);
            $translated_category = $this->translateText($product['category'], $target_language);
            
            // Save translation
            $this->saveProductTranslation(
                $product['id'], 
                $target_language, 
                $translated_name, 
                $translated_description,
                $translated_category
            );
        }
    }
    
    /**
     * Translate all categories
     */
    private function translateAllCategories($target_language) {
        $sql = "SELECT DISTINCT category FROM products WHERE status = 'active'";
        $stmt = $this->pdo->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($categories as $category) {
            $translated_category = $this->translateText($category, $target_language);
            $this->saveCategoryTranslation($category, $target_language, $translated_category);
        }
    }
    
    /**
     * Translate user-generated content (reviews, comments, etc.)
     */
    private function translateUserContent($target_language) {
        // This would translate user reviews, comments, etc.
        // Implementation depends on your user content structure
    }
    
    /**
     * Translate order-related content
     */
    private function translateOrderContent($target_language) {
        // This would translate order statuses, payment methods, etc.
        // Implementation depends on your order structure
    }
    
    /**
     * Core text translation function
     */
    public function translateText($text, $target_language, $source_language = 'en') {
        if (empty($text) || $target_language === $source_language) {
            return $text;
        }
        
        // Check if translation already exists in cache
        $cached_translation = $this->getCachedTranslation($text, $target_language);
        if ($cached_translation) {
            return $cached_translation;
        }
        
        // Use predefined translations first
        $predefined_translation = $this->getPredefinedTranslation($text, $target_language);
        if ($predefined_translation) {
            $this->cacheTranslation($text, $target_language, $predefined_translation);
            return $predefined_translation;
        }
        
        // Use Google Translate as fallback
        $google_translation = $this->googleTranslate($text, $target_language, $source_language);
        if ($google_translation && $google_translation !== $text) {
            $this->cacheTranslation($text, $target_language, $google_translation);
            return $google_translation;
        }
        
        return $text; // Return original if no translation available
    }
    
    /**
     * Get predefined translations for common terms
     */
    private function getPredefinedTranslation($text, $target_language) {
        $predefined_translations = [
            'rw' => [
                // E-commerce terms
                'Price' => 'Igiciro',
                'Stock' => 'Ibicuruzwa',
                'Available' => 'Biraboneka',
                'Out of Stock' => 'Byarangiye',
                'Add to Cart' => 'Shyira mu gitebo',
                'Buy Now' => 'Gura Ubu',
                'Order' => 'Gutumiza',
                'Payment' => 'Kwishyura',
                'Delivery' => 'Gutanga',
                'Customer' => 'Umukiriya',
                'Seller' => 'Umucuruzi',
                'Buyer' => 'Umuguzi',
                'Product' => 'Igicuruzwa',
                'Category' => 'Icyiciro',
                'Search' => 'Gushakisha',
                'Filter' => 'Gushungura',
                'Sort' => 'Gutondeka',
                'Date' => 'Itariki',
                'Time' => 'Igihe',
                'Today' => 'Uyu munsi',
                'Yesterday' => 'Ejo',
                'Tomorrow' => 'Ejo hazaza',
                'Week' => 'Icyumweru',
                'Month' => 'Ukwezi',
                'Year' => 'Umwaka',
                
                // Product categories
                'Electronics' => 'Ibikoresho by\'amashanyarazi',
                'Clothing' => 'Imyambaro',
                'Books' => 'Ibitabo',
                'Sports' => 'Siporo',
                'Agriculture' => 'Ubuhinzi',
                'Food' => 'Ibiryo',
                'Health' => 'Ubuzima',
                'Beauty' => 'Ubwiza',
                'Home' => 'Inzu',
                'Garden' => 'Ubusitani',
                'Automotive' => 'Ibinyabiziga',
                'Toys' => 'Ibikinisho',
                
                // Common products
                'Phone' => 'Telefoni',
                'Computer' => 'Mudasobwa',
                'Laptop' => 'Mudasobwa igendanwa',
                'Tablet' => 'Tableti',
                'Camera' => 'Kamera',
                'Television' => 'Televiziyo',
                'Radio' => 'Radiyo',
                'Shoes' => 'Inkweto',
                'Shirt' => 'Ishati',
                'Dress' => 'Umwenda',
                'Pants' => 'Ipantaro',
                'Hat' => 'Ingofero',
                'Bag' => 'Umufuka',
                'Watch' => 'Isaha',
                'Book' => 'Igitabo',
                'Pen' => 'Ikibindi',
                'Paper' => 'Impapuro',
                'Car' => 'Imodoka',
                'Bicycle' => 'Igare',
                'Motorcycle' => 'Pikipiki'
            ],
            'fr' => [
                // E-commerce terms
                'Price' => 'Prix',
                'Stock' => 'Stock',
                'Available' => 'Disponible',
                'Out of Stock' => 'Rupture de stock',
                'Add to Cart' => 'Ajouter au panier',
                'Buy Now' => 'Acheter maintenant',
                'Order' => 'Commande',
                'Payment' => 'Paiement',
                'Delivery' => 'Livraison',
                'Customer' => 'Client',
                'Seller' => 'Vendeur',
                'Buyer' => 'Acheteur',
                'Product' => 'Produit',
                'Category' => 'Catégorie',
                'Search' => 'Rechercher',
                'Filter' => 'Filtrer',
                'Sort' => 'Trier',
                'Date' => 'Date',
                'Time' => 'Heure',
                'Today' => 'Aujourd\'hui',
                'Yesterday' => 'Hier',
                'Tomorrow' => 'Demain',
                'Week' => 'Semaine',
                'Month' => 'Mois',
                'Year' => 'Année',
                
                // Product categories
                'Electronics' => 'Électronique',
                'Clothing' => 'Vêtements',
                'Books' => 'Livres',
                'Sports' => 'Sports',
                'Agriculture' => 'Agriculture',
                'Food' => 'Alimentation',
                'Health' => 'Santé',
                'Beauty' => 'Beauté',
                'Home' => 'Maison',
                'Garden' => 'Jardin',
                'Automotive' => 'Automobile',
                'Toys' => 'Jouets',
                
                // Common products
                'Phone' => 'Téléphone',
                'Computer' => 'Ordinateur',
                'Laptop' => 'Ordinateur portable',
                'Tablet' => 'Tablette',
                'Camera' => 'Appareil photo',
                'Television' => 'Télévision',
                'Radio' => 'Radio',
                'Shoes' => 'Chaussures',
                'Shirt' => 'Chemise',
                'Dress' => 'Robe',
                'Pants' => 'Pantalon',
                'Hat' => 'Chapeau',
                'Bag' => 'Sac',
                'Watch' => 'Montre',
                'Book' => 'Livre',
                'Pen' => 'Stylo',
                'Paper' => 'Papier',
                'Car' => 'Voiture',
                'Bicycle' => 'Vélo',
                'Motorcycle' => 'Moto'
            ]
        ];
        
        return $predefined_translations[$target_language][$text] ?? null;
    }
    
    /**
     * Google Translate integration
     */
    private function googleTranslate($text, $target_language, $source_language = 'en') {
        // This would integrate with actual Google Translate API
        // For now, return null to use predefined translations
        return null;
    }
    
    /**
     * Cache translation for future use
     */
    private function cacheTranslation($original_text, $language, $translated_text) {
        try {
            $sql = "INSERT INTO translation_cache (original_text, language_code, translated_text, created_at) 
                    VALUES (?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE translated_text = VALUES(translated_text), updated_at = NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original_text, $language, $translated_text]);
        } catch (Exception $e) {
            // Log error but don't break functionality
            error_log("Translation cache error: " . $e->getMessage());
        }
    }
    
    /**
     * Get cached translation
     */
    private function getCachedTranslation($original_text, $language) {
        try {
            $sql = "SELECT translated_text FROM translation_cache 
                    WHERE original_text = ? AND language_code = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original_text, $language]);
            $result = $stmt->fetch();
            return $result ? $result['translated_text'] : null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Save product translation
     */
    private function saveProductTranslation($product_id, $language, $name, $description, $category = null) {
        try {
            $sql = "INSERT INTO product_translations (product_id, language_code, name, description, category, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE 
                    name = VALUES(name), 
                    description = VALUES(description), 
                    category = VALUES(category),
                    updated_at = NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$product_id, $language, $name, $description, $category]);
        } catch (Exception $e) {
            error_log("Product translation save error: " . $e->getMessage());
        }
    }
    
    /**
     * Save category translation
     */
    private function saveCategoryTranslation($original_category, $language, $translated_category) {
        try {
            $sql = "INSERT INTO category_translations (original_category, language_code, translated_category, created_at) 
                    VALUES (?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE 
                    translated_category = VALUES(translated_category),
                    updated_at = NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original_category, $language, $translated_category]);
        } catch (Exception $e) {
            error_log("Category translation save error: " . $e->getMessage());
        }
    }
    
    /**
     * Get translated product
     */
    public function getTranslatedProduct($product_id, $language = null) {
        $language = $language ?: $this->current_language;

        if ($language === 'en') {
            // Return original product for English
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                $product['translated_name'] = $product['name'];
                $product['translated_description'] = $product['description'];
                $product['translated_category'] = $product['category'];
            }
            return $product;
        }

        // Check if category column exists in product_translations table
        $has_category_column = $this->checkColumnExists('product_translations', 'category');

        try {
            if ($has_category_column) {
                // Get product with translation including category
                $sql = "SELECT p.*,
                               COALESCE(pt.name, p.name) as translated_name,
                               COALESCE(pt.description, p.description) as translated_description,
                               COALESCE(pt.category, p.category) as translated_category
                        FROM products p
                        LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                        WHERE p.id = ?";
            } else {
                // Get product with translation without category
                $sql = "SELECT p.*,
                               COALESCE(pt.name, p.name) as translated_name,
                               COALESCE(pt.description, p.description) as translated_description,
                               p.category as translated_category
                        FROM products p
                        LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                        WHERE p.id = ?";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$language, $product_id]);
            $product = $stmt->fetch();

            if ($product && $product['translated_name'] === $product['name']) {
                // No translation exists, create one
                $translated_name = $this->translateText($product['name'], $language);
                $translated_description = $this->translateText($product['description'], $language);
                $translated_category = $this->translateText($product['category'], $language);

                $this->saveProductTranslation($product_id, $language, $translated_name, $translated_description, $translated_category);

                $product['translated_name'] = $translated_name;
                $product['translated_description'] = $translated_description;
                $product['translated_category'] = $translated_category;
            }

            return $product;

        } catch (PDOException $e) {
            // Fallback to basic product query
            error_log("Get translated product error: " . $e->getMessage());
            $sql = "SELECT *, name as translated_name, description as translated_description, category as translated_category FROM products WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$product_id]);
            return $stmt->fetch();
        }
    }
    
    /**
     * Get all translated products with automatic translation
     */
    public function getAllTranslatedProducts($limit = null, $category = null, $language = null) {
        $language = $language ?: $this->current_language;

        // Check if category column exists in product_translations table
        $has_category_column = $this->checkColumnExists('product_translations', 'category');

        if ($has_category_column) {
            $sql = "SELECT p.*,
                           COALESCE(pt.name, p.name) as translated_name,
                           COALESCE(pt.description, p.description) as translated_description,
                           COALESCE(pt.category, p.category) as translated_category
                    FROM products p
                    LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                    WHERE p.status = 'active'";
        } else {
            $sql = "SELECT p.*,
                           COALESCE(pt.name, p.name) as translated_name,
                           COALESCE(pt.description, p.description) as translated_description,
                           p.category as translated_category
                    FROM products p
                    LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                    WHERE p.status = 'active'";
        }

        $params = [$language];

        if ($category) {
            if ($has_category_column) {
                $sql .= " AND (p.category = ? OR pt.category = ?)";
                $params[] = $category;
                $params[] = $category;
            } else {
                $sql .= " AND p.category = ?";
                $params[] = $category;
            }
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            // Auto-translate missing translations
            foreach ($products as &$product) {
                if ($language !== 'en' && $product['translated_name'] === $product['name']) {
                    // No translation exists, create one
                    $translated_name = $this->translateText($product['name'], $language);
                    $translated_description = $this->translateText($product['description'], $language);
                    $translated_category = $this->translateText($product['category'], $language);

                    // Save the translation for future use
                    $this->saveProductTranslation($product['id'], $language, $translated_name, $translated_description, $translated_category);

                    // Update the current product data
                    $product['translated_name'] = $translated_name;
                    $product['translated_description'] = $translated_description;
                    $product['translated_category'] = $translated_category;
                }
            }

            return $products;
        } catch (PDOException $e) {
            // Fallback to basic query if there's an error
            error_log("Enhanced translation error: " . $e->getMessage());
            return $this->getFallbackProducts($limit, $category);
        }
    }
    
    /**
     * Check if a column exists in a table
     */
    private function checkColumnExists($table, $column) {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Fallback method to get products without translations
     */
    private function getFallbackProducts($limit = null, $category = null) {
        $sql = "SELECT p.*,
                       p.name as translated_name,
                       p.description as translated_description,
                       p.category as translated_category
                FROM products p
                WHERE p.status = 'active'";

        $params = [];

        if ($category) {
            $sql .= " AND p.category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Fallback products error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Format date according to language preferences
     */
    public function formatDate($date, $language = null) {
        $language = $language ?: $this->current_language;
        $timestamp = is_string($date) ? strtotime($date) : $date;
        
        switch ($language) {
            case 'rw':
                // Kinyarwanda date format
                return date('d/m/Y', $timestamp);
            case 'fr':
                // French date format
                return date('d/m/Y', $timestamp);
            case 'ar':
                // Arabic date format (right-to-left)
                return date('Y/m/d', $timestamp);
            default:
                // English and other languages
                return date('m/d/Y', $timestamp);
        }
    }
    
    /**
     * Format currency according to language preferences
     */
    public function formatCurrency($amount, $language = null) {
        $language = $language ?: $this->current_language;
        
        switch ($language) {
            case 'rw':
                return number_format($amount, 0) . ' RWF';
            case 'fr':
                return number_format($amount, 0, ',', ' ') . ' RWF';
            default:
                return 'RWF ' . number_format($amount, 0);
        }
    }
}

// Initialize global translation system
global $translation_system;
$translation_system = new EnhancedTranslationSystem($pdo);
?>
