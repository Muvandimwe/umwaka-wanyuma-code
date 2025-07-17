<?php
/**
 * Dynamic Language System
 * Automatically translates all database content when user changes language
 */

class DynamicLanguageSystem {
    private $pdo;
    private $current_language;
    private $translation_cache = [];
    
    public function __construct($pdo, $language = 'en') {
        $this->pdo = $pdo;
        $this->current_language = $language;
    }
    
    /**
     * Change system language and update session
     */
    public function changeLanguage($new_language) {
        if (in_array($new_language, ['en', 'rw', 'fr'])) {
            $_SESSION['language'] = $new_language;
            $this->current_language = $new_language;
            
            // Update user preference in database if logged in
            if (isset($_SESSION['user_id'])) {
                $this->updateUserLanguagePreference($_SESSION['user_id'], $new_language);
            }
            
            return true;
        }
        return false;
    }
    
    /**
     * Get translated products for current language
     */
    public function getTranslatedProducts($limit = null, $category = null) {
        $language = $this->current_language;
        
        // Base query
        $sql = "SELECT p.*, 
                       CASE 
                           WHEN ? = 'en' THEN p.name
                           WHEN pt.name IS NOT NULL THEN pt.name
                           ELSE p.name
                       END as display_name,
                       CASE 
                           WHEN ? = 'en' THEN p.description
                           WHEN pt.description IS NOT NULL THEN pt.description
                           ELSE p.description
                       END as display_description,
                       CASE 
                           WHEN ? = 'en' THEN p.category
                           WHEN pt.category IS NOT NULL THEN pt.category
                           ELSE p.category
                       END as display_category,
                       p.price as display_price,
                       p.stock_quantity,
                       p.image_url,
                       p.created_at,
                       u.full_name as seller_name
                FROM products p 
                LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                LEFT JOIN users u ON p.seller_id = u.id
                WHERE p.status = 'active'";
        
        $params = [$language, $language, $language, $language];
        
        if ($category) {
            $sql .= " AND (p.category = ? OR pt.category = ?)";
            $params[] = $category;
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
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Auto-translate missing content
            foreach ($products as &$product) {
                if ($language !== 'en') {
                    // Check if translation is needed
                    if ($product['display_name'] === $product['name']) {
                        $this->autoTranslateProduct($product, $language);
                    }
                    
                    // Format price according to language
                    $product['formatted_price'] = $this->formatPrice($product['price'], $language);
                    
                    // Format date according to language
                    $product['formatted_date'] = $this->formatDate($product['created_at'], $language);
                }
            }
            
            return $products;
        } catch (PDOException $e) {
            error_log("Dynamic language system error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Auto-translate product content
     */
    private function autoTranslateProduct(&$product, $language) {
        try {
            // Translate product name
            $translated_name = $this->translateText($product['name'], $language);
            $translated_description = $this->translateText($product['description'], $language);
            $translated_category = $this->translateText($product['category'], $language);
            
            // Update display values
            $product['display_name'] = $translated_name;
            $product['display_description'] = $translated_description;
            $product['display_category'] = $translated_category;
            
            // Save translation to database for future use
            $this->saveProductTranslation($product['id'], $language, $translated_name, $translated_description, $translated_category);
            
        } catch (Exception $e) {
            error_log("Auto-translation error: " . $e->getMessage());
        }
    }
    
    /**
     * Translate text using Google Translate API simulation
     */
    private function translateText($text, $target_language) {
        if (empty($text) || $target_language === 'en') {
            return $text;
        }
        
        // Check cache first
        $cache_key = md5($text . $target_language);
        if (isset($this->translation_cache[$cache_key])) {
            return $this->translation_cache[$cache_key];
        }
        
        // Check database cache
        $cached_translation = $this->getCachedTranslation($text, $target_language);
        if ($cached_translation) {
            $this->translation_cache[$cache_key] = $cached_translation;
            return $cached_translation;
        }
        
        // Perform translation (simplified mapping for demo)
        $translated_text = $this->performTranslation($text, $target_language);
        
        // Cache the result
        $this->translation_cache[$cache_key] = $translated_text;
        $this->saveCachedTranslation($text, $target_language, $translated_text);
        
        return $translated_text;
    }
    
    /**
     * Perform actual translation (simplified for demo)
     */
    private function performTranslation($text, $target_language) {
        // Common product translations
        $translations = [
            'rw' => [
                'Electronics' => 'Ibikoresho by\'ikoranabuhanga',
                'Clothing' => 'Imyambaro',
                'Books' => 'Ibitabo',
                'Food' => 'Ibiryo',
                'Shoes' => 'Inkweto',
                'Bags' => 'Imifuka',
                'Phones' => 'Telefoni',
                'Computers' => 'Mudasobwa',
                'Furniture' => 'Ibikoresho by\'inyubako',
                'Toys' => 'Ibikinisho',
                'Sports' => 'Siporo',
                'Beauty' => 'Ubwiza',
                'Health' => 'Ubuzima',
                'Home' => 'Inzu',
                'Garden' => 'Ubusitani',
                'Car' => 'Imodoka',
                'Motorcycle' => 'Pikipiki',
                'Bicycle' => 'Igare',
                'Tools' => 'Ibikoresho',
                'Music' => 'Umuziki',
                'Movies' => 'Amashusho',
                'Games' => 'Imikino',
                'Office' => 'Ibiro',
                'School' => 'Ishuri',
                'Kitchen' => 'Igikoni',
                'Bathroom' => 'Ubwiyeyo',
                'Bedroom' => 'Icyumba cyo kuraramo',
                'Living Room' => 'Icyumba cyo kwicara',
                'Dining Room' => 'Icyumba cyo kurira',
                'New' => 'Gishya',
                'Used' => 'Yakoreshejwe',
                'Available' => 'Biraboneka',
                'Out of Stock' => 'Byarangiye',
                'Price' => 'Igiciro',
                'Quality' => 'Ubwiza',
                'Brand' => 'Ikimenyetso',
                'Size' => 'Ubunini',
                'Color' => 'Ibara',
                'Material' => 'Ibikoresho',
                'Weight' => 'Uburemere',
                'Length' => 'Uburebure',
                'Width' => 'Ubugari',
                'Height' => 'Uburebure',
                'Description' => 'Ibisobanuro',
                'Features' => 'Ibintu bidasanzwe',
                'Specifications' => 'Amakuru arambuye',
                'Reviews' => 'Ibitekerezo',
                'Rating' => 'Amanota',
                'Seller' => 'Umucuruzi',
                'Buyer' => 'Umuguzi',
                'Order' => 'Gutumiza',
                'Payment' => 'Kwishyura',
                'Delivery' => 'Gutanga',
                'Shipping' => 'Kohereza',
                'Return' => 'Gusubiza',
                'Warranty' => 'Ubwishingizi',
                'Support' => 'Ubufasha',
                'Contact' => 'Kuvugana',
                'Address' => 'Aderesi',
                'Phone' => 'Telefoni',
                'Email' => 'Imeli',
                'Website' => 'Urubuga',
                'Social Media' => 'Imbuga nkoranyambaga',
                'Facebook' => 'Facebook',
                'Twitter' => 'Twitter',
                'Instagram' => 'Instagram',
                'YouTube' => 'YouTube',
                'WhatsApp' => 'WhatsApp',
                'Telegram' => 'Telegram',
                'Location' => 'Ahantu',
                'City' => 'Umujyi',
                'Country' => 'Igihugu',
                'Region' => 'Akarere',
                'District' => 'Akarere',
                'Sector' => 'Umurenge',
                'Cell' => 'Akagari',
                'Village' => 'Umudugudu'
            ],
            'fr' => [
                'Electronics' => 'Électronique',
                'Clothing' => 'Vêtements',
                'Books' => 'Livres',
                'Food' => 'Nourriture',
                'Shoes' => 'Chaussures',
                'Bags' => 'Sacs',
                'Phones' => 'Téléphones',
                'Computers' => 'Ordinateurs',
                'Furniture' => 'Meubles',
                'Toys' => 'Jouets',
                'Sports' => 'Sports',
                'Beauty' => 'Beauté',
                'Health' => 'Santé',
                'Home' => 'Maison',
                'Garden' => 'Jardin',
                'Car' => 'Voiture',
                'Motorcycle' => 'Moto',
                'Bicycle' => 'Vélo',
                'Tools' => 'Outils',
                'Music' => 'Musique',
                'Movies' => 'Films',
                'Games' => 'Jeux',
                'Office' => 'Bureau',
                'School' => 'École',
                'Kitchen' => 'Cuisine',
                'Bathroom' => 'Salle de bain',
                'Bedroom' => 'Chambre',
                'Living Room' => 'Salon',
                'Dining Room' => 'Salle à manger',
                'New' => 'Nouveau',
                'Used' => 'Utilisé',
                'Available' => 'Disponible',
                'Out of Stock' => 'Rupture de stock',
                'Price' => 'Prix',
                'Quality' => 'Qualité',
                'Brand' => 'Marque',
                'Size' => 'Taille',
                'Color' => 'Couleur',
                'Material' => 'Matériau',
                'Weight' => 'Poids',
                'Length' => 'Longueur',
                'Width' => 'Largeur',
                'Height' => 'Hauteur',
                'Description' => 'Description',
                'Features' => 'Caractéristiques',
                'Specifications' => 'Spécifications',
                'Reviews' => 'Avis',
                'Rating' => 'Note',
                'Seller' => 'Vendeur',
                'Buyer' => 'Acheteur',
                'Order' => 'Commande',
                'Payment' => 'Paiement',
                'Delivery' => 'Livraison',
                'Shipping' => 'Expédition',
                'Return' => 'Retour',
                'Warranty' => 'Garantie',
                'Support' => 'Support',
                'Contact' => 'Contact',
                'Address' => 'Adresse',
                'Phone' => 'Téléphone',
                'Email' => 'Email',
                'Website' => 'Site web',
                'Social Media' => 'Réseaux sociaux',
                'Facebook' => 'Facebook',
                'Twitter' => 'Twitter',
                'Instagram' => 'Instagram',
                'YouTube' => 'YouTube',
                'WhatsApp' => 'WhatsApp',
                'Telegram' => 'Telegram',
                'Location' => 'Emplacement',
                'City' => 'Ville',
                'Country' => 'Pays',
                'Region' => 'Région',
                'District' => 'District',
                'Sector' => 'Secteur',
                'Cell' => 'Cellule',
                'Village' => 'Village'
            ]
        ];
        
        // Check if we have a direct translation
        if (isset($translations[$target_language][$text])) {
            return $translations[$target_language][$text];
        }
        
        // For longer text, try to translate word by word
        $words = explode(' ', $text);
        $translated_words = [];
        
        foreach ($words as $word) {
            $word = trim($word, '.,!?;:');
            if (isset($translations[$target_language][$word])) {
                $translated_words[] = $translations[$target_language][$word];
            } else {
                $translated_words[] = $word; // Keep original if no translation
            }
        }
        
        return implode(' ', $translated_words);
    }
    
    /**
     * Format price according to language preferences
     */
    private function formatPrice($price, $language) {
        switch ($language) {
            case 'rw':
                return number_format($price, 0, ',', '.') . ' FRW';
            case 'fr':
                return number_format($price, 2, ',', ' ') . ' FRW';
            default:
                return 'FRW ' . number_format($price, 2);
        }
    }
    
    /**
     * Format date according to language preferences
     */
    private function formatDate($date, $language) {
        $timestamp = strtotime($date);
        
        switch ($language) {
            case 'rw':
                return date('d/m/Y', $timestamp);
            case 'fr':
                return date('d/m/Y', $timestamp);
            default:
                return date('M j, Y', $timestamp);
        }
    }
    
    /**
     * Save product translation to database
     */
    private function saveProductTranslation($product_id, $language, $name, $description, $category) {
        try {
            $sql = "INSERT INTO product_translations (product_id, language_code, name, description, category)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    name = VALUES(name), 
                    description = VALUES(description),
                    category = VALUES(category),
                    updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$product_id, $language, $name, $description, $category]);
        } catch (PDOException $e) {
            error_log("Save product translation error: " . $e->getMessage());
        }
    }
    
    /**
     * Get cached translation from database
     */
    private function getCachedTranslation($text, $language) {
        try {
            $sql = "SELECT translated_text FROM translation_cache 
                    WHERE original_text = ? AND language_code = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$text, $language]);
            $result = $stmt->fetch();
            
            return $result ? $result['translated_text'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Save cached translation to database
     */
    private function saveCachedTranslation($original, $language, $translated) {
        try {
            $sql = "INSERT INTO translation_cache (original_text, language_code, translated_text)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    translated_text = VALUES(translated_text),
                    updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original, $language, $translated]);
        } catch (PDOException $e) {
            error_log("Save cached translation error: " . $e->getMessage());
        }
    }
    
    /**
     * Update user language preference
     */
    private function updateUserLanguagePreference($user_id, $language) {
        try {
            $sql = "UPDATE users SET preferred_language = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$language, $user_id]);
        } catch (PDOException $e) {
            error_log("Update user language preference error: " . $e->getMessage());
        }
    }
    
    /**
     * Get all available categories in current language
     */
    public function getTranslatedCategories() {
        $language = $this->current_language;
        
        try {
            $sql = "SELECT DISTINCT 
                           CASE 
                               WHEN ? = 'en' THEN p.category
                               WHEN pt.category IS NOT NULL THEN pt.category
                               ELSE p.category
                           END as category_name,
                           p.category as original_category,
                           COUNT(*) as product_count
                    FROM products p 
                    LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
                    WHERE p.status = 'active'
                    GROUP BY p.category
                    ORDER BY category_name";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$language, $language]);
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Auto-translate missing category translations
            foreach ($categories as &$category) {
                if ($language !== 'en' && $category['category_name'] === $category['original_category']) {
                    $translated_category = $this->translateText($category['original_category'], $language);
                    $category['category_name'] = $translated_category;
                }
            }
            
            return $categories;
        } catch (PDOException $e) {
            error_log("Get translated categories error: " . $e->getMessage());
            return [];
        }
    }
}
?>
