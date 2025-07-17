<?php
/**
 * Google Translate Integration System
 * Provides real-time translation using Google Translate API
 */

class GoogleTranslator {
    private $pdo;
    private $current_language;
    private $translation_cache = [];
    private $api_key;
    
    // Language codes mapping
    private $language_codes = [
        'en' => 'en',    // English
        'fr' => 'fr',    // French
        'rw' => 'rw'     // Kinyarwanda
    ];
    
    public function __construct($pdo, $language = 'en', $api_key = null) {
        $this->pdo = $pdo;
        $this->current_language = $language;
        $this->api_key = $api_key;
        $this->initializeTranslationCache();
    }
    
    /**
     * Initialize translation cache table
     */
    private function initializeTranslationCache() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS google_translation_cache (
                id INT AUTO_INCREMENT PRIMARY KEY,
                original_text TEXT NOT NULL,
                source_language VARCHAR(5) NOT NULL,
                target_language VARCHAR(5) NOT NULL,
                translated_text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_translation (original_text(255), source_language, target_language)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating translation cache table: " . $e->getMessage());
        }
    }
    
    /**
     * Translate text using Google Translate API with fallback
     */
    public function translateText($text, $target_language = null, $source_language = 'en') {
        if (empty($text)) {
            return $text;
        }
        
        $target_language = $target_language ?: $this->current_language;
        
        // If source and target are the same, return original
        if ($source_language === $target_language) {
            return $text;
        }
        
        // Check cache first
        $cached_translation = $this->getCachedTranslation($text, $source_language, $target_language);
        if ($cached_translation) {
            return $cached_translation;
        }
        
        // Try Google Translate API
        $translated_text = $this->callGoogleTranslateAPI($text, $source_language, $target_language);
        
        // If API fails, use fallback translation
        if (!$translated_text) {
            $translated_text = $this->getFallbackTranslation($text, $target_language);
        }
        
        // Cache the result
        $this->saveCachedTranslation($text, $source_language, $target_language, $translated_text);
        
        return $translated_text;
    }
    
    /**
     * Call Google Translate API
     */
    private function callGoogleTranslateAPI($text, $source_lang, $target_lang) {
        // For demo purposes, we'll simulate Google Translate API
        // In production, you would use actual Google Translate API
        
        if (!$this->api_key) {
            return $this->simulateGoogleTranslate($text, $source_lang, $target_lang);
        }
        
        try {
            $url = "https://translation.googleapis.com/language/translate/v2";
            $data = [
                'key' => $this->api_key,
                'q' => $text,
                'source' => $source_lang,
                'target' => $target_lang,
                'format' => 'text'
            ];
            
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                ]
            ];
            
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            if ($result !== false) {
                $response = json_decode($result, true);
                if (isset($response['data']['translations'][0]['translatedText'])) {
                    return $response['data']['translations'][0]['translatedText'];
                }
            }
        } catch (Exception $e) {
            error_log("Google Translate API error: " . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Simulate Google Translate for demo (high-quality translations)
     */
    private function simulateGoogleTranslate($text, $source_lang, $target_lang) {
        // High-quality translation mappings
        $translations = [
            'fr' => [
                // Product Categories
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
                'Kitchen' => 'Cuisine',
                'Bathroom' => 'Salle de bain',
                'Office' => 'Bureau',
                'School' => 'École',
                'Car' => 'Voiture',
                'Motorcycle' => 'Moto',
                'Bicycle' => 'Vélo',
                'Tools' => 'Outils',
                'Music' => 'Musique',
                'Movies' => 'Films',
                'Games' => 'Jeux',
                
                // Common Products
                'Smartphone Samsung Galaxy' => 'Smartphone Samsung Galaxy',
                'Latest Samsung Galaxy smartphone' => 'Dernier smartphone Samsung Galaxy',
                'High-performance Dell laptop' => 'Ordinateur portable Dell haute performance',
                'Comfortable Nike running shoes' => 'Chaussures de course Nike confortables',
                'Premium quality cotton t-shirt' => 'T-shirt en coton de qualité premium',
                'High-quality wireless headphones' => 'Écouteurs sans fil de haute qualité',
                'Elegant leather handbag' => 'Sac à main en cuir élégant',
                'Automatic coffee maker' => 'Cafetière automatique',
                'Smart fitness tracker' => 'Tracker de fitness intelligent',
                'Professional non-stick cooking pan set' => 'Set de casseroles antiadhésives professionnelles',
                'Ergonomic office chair' => 'Chaise de bureau ergonomique',
                'Portable Bluetooth speaker' => 'Haut-parleur Bluetooth portable',
                'Warm winter jacket' => 'Veste d\'hiver chaude',
                
                // Product Descriptions
                'with advanced camera and long battery life' => 'avec caméra avancée et longue durée de vie de la batterie',
                'Perfect for daily use and professional photography' => 'Parfait pour un usage quotidien et la photographie professionnelle',
                'ideal for work and entertainment' => 'idéal pour le travail et le divertissement',
                'Features fast processor and ample storage space' => 'Dispose d\'un processeur rapide et d\'un espace de stockage suffisant',
                'designed for athletes and fitness enthusiasts' => 'conçu pour les athlètes et les passionnés de fitness',
                'Lightweight and durable construction' => 'Construction légère et durable',
                'available in multiple colors' => 'disponible en plusieurs couleurs',
                'Soft fabric and comfortable fit for everyday wear' => 'Tissu doux et coupe confortable pour un port quotidien',
                'with noise cancellation' => 'avec suppression du bruit',
                'Perfect for music lovers and professionals' => 'Parfait pour les mélomanes et les professionnels',
                'perfect for work and special occasions' => 'parfait pour le travail et les occasions spéciales',
                'Spacious interior with multiple compartments' => 'Intérieur spacieux avec plusieurs compartiments',
                'for brewing perfect coffee at home' => 'pour préparer un café parfait à la maison',
                'Easy to use with programmable settings' => 'Facile à utiliser avec des paramètres programmables',
                'to monitor your health and activity' => 'pour surveiller votre santé et votre activité',
                'Waterproof design with heart rate monitoring' => 'Design étanche avec surveillance du rythme cardiaque',
                'for all your culinary needs' => 'pour tous vos besoins culinaires',
                'Heat-resistant and easy to clean' => 'Résistant à la chaleur et facile à nettoyer',
                'with lumbar support' => 'avec support lombaire',
                'Adjustable height and comfortable padding' => 'Hauteur réglable et rembourrage confortable',
                'for long work hours' => 'pour de longues heures de travail',
                'with excellent sound quality' => 'avec une excellente qualité sonore',
                'Perfect for parties and outdoor activities' => 'Parfait pour les fêtes et les activités de plein air',
                'with waterproof material' => 'avec matériau imperméable',
                'Stylish design suitable for cold weather protection' => 'Design élégant adapté à la protection par temps froid'
            ],
            
            'rw' => [
                // Product Categories
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
                'Kitchen' => 'Igikoni',
                'Bathroom' => 'Ubwiyeyo',
                'Office' => 'Ibiro',
                'School' => 'Ishuri',
                'Car' => 'Imodoka',
                'Motorcycle' => 'Pikipiki',
                'Bicycle' => 'Igare',
                'Tools' => 'Ibikoresho',
                'Music' => 'Umuziki',
                'Movies' => 'Amashusho',
                'Games' => 'Imikino',
                
                // Common Products
                'Smartphone Samsung Galaxy' => 'Telefoni Samsung Galaxy',
                'Latest Samsung Galaxy smartphone' => 'Telefoni ya Samsung Galaxy igezweho',
                'High-performance Dell laptop' => 'Mudasobwa wa Dell ukora neza',
                'Comfortable Nike running shoes' => 'Inkweto za Nike zo kwiruka zororoshye',
                'Premium quality cotton t-shirt' => 'Ikoti ya cotton y\'ubwiza bwo hejuru',
                'High-quality wireless headphones' => 'Amagutwi adafite insinga y\'ubwiza bwo hejuru',
                'Elegant leather handbag' => 'Umufuka w\'uruhu mwiza',
                'Automatic coffee maker' => 'Igikoresho cyo gukora ikawa',
                'Smart fitness tracker' => 'Igikoresho cyo gukurikirana ubuzima',
                'Professional non-stick cooking pan set' => 'Ibikoresho byo guteka by\'abanyamwuga',
                'Ergonomic office chair' => 'Intebe y\'ibiro yoroshye',
                'Portable Bluetooth speaker' => 'Igikoresho cyo kumva amajwi gishobora kwimurwa',
                'Warm winter jacket' => 'Ikoti y\'itumba ishyuha',
                
                // Product Descriptions
                'with advanced camera and long battery life' => 'ifite kamera igezweho n\'amashanyarazi amara igihe kirekire',
                'Perfect for daily use and professional photography' => 'Byiza byo gukoresha buri munsi no gufotora nk\'umwuga',
                'ideal for work and entertainment' => 'byiza ku kazi no kwishimisha',
                'Features fast processor and ample storage space' => 'Bifite processor yihuta n\'ahantu hanini ho kubika',
                'designed for athletes and fitness enthusiasts' => 'byakozwe ku bakinnyi n\'abakunda siporo',
                'Lightweight and durable construction' => 'Byoroshye kandi birambye',
                'available in multiple colors' => 'biraboneka mu mabara menshi',
                'Soft fabric and comfortable fit for everyday wear' => 'Impuzu yoroshye kandi ihuza neza byo kwambara buri munsi',
                'with noise cancellation' => 'bifite ubushobozi bwo guhagarika urusaku',
                'Perfect for music lovers and professionals' => 'Byiza ku bakunda umuziki n\'abanyamwuga',
                'perfect for work and special occasions' => 'byiza ku kazi no mu birori byihariye',
                'Spacious interior with multiple compartments' => 'Imbere nini ifite uduce twinshi',
                'for brewing perfect coffee at home' => 'byo gukora ikawa nziza mu rugo',
                'Easy to use with programmable settings' => 'Byoroshye gukoresha bifite amategeko ashobora guhindurwa',
                'to monitor your health and activity' => 'byo gukurikirana ubuzima bwawe n\'ibikorwa byawe',
                'Waterproof design with heart rate monitoring' => 'Byakozwe bidafite amazi bifite gukurikirana ubwoba bw\'umutima',
                'for all your culinary needs' => 'ku byose ukeneye mu guteka',
                'Heat-resistant and easy to clean' => 'Bidatwika ubushyuhe kandi byoroshye gusukura',
                'with lumbar support' => 'bifite gushyigikira umugongo',
                'Adjustable height and comfortable padding' => 'Uburebure bushobora guhindurwa kandi bufite padding yoroshye',
                'for long work hours' => 'ku masaha maremare yo gukora',
                'with excellent sound quality' => 'bifite ijwi ryiza cyane',
                'Perfect for parties and outdoor activities' => 'Byiza ku birori no mu bikorwa byo hanze',
                'with waterproof material' => 'bifite ibikoresho bidafite amazi',
                'Stylish design suitable for cold weather protection' => 'Igishushanyo cyiza gikwiye kurinda ikirere gikonje'
            ]
        ];
        
        // Check for direct translation
        if (isset($translations[$target_lang][$text])) {
            return $translations[$target_lang][$text];
        }
        
        // Word-by-word translation for complex phrases
        $words = explode(' ', $text);
        $translated_words = [];
        
        foreach ($words as $word) {
            $clean_word = trim($word, '.,!?;:()[]{}"\'-');
            if (isset($translations[$target_lang][$clean_word])) {
                $translated_words[] = $translations[$target_lang][$clean_word];
            } else {
                $translated_words[] = $word; // Keep original if no translation
            }
        }
        
        return implode(' ', $translated_words);
    }
    
    /**
     * Get fallback translation (basic dictionary)
     */
    private function getFallbackTranslation($text, $target_language) {
        // Basic fallback translations
        $fallback = [
            'fr' => [
                'Product' => 'Produit',
                'Category' => 'Catégorie',
                'Price' => 'Prix',
                'Description' => 'Description',
                'Available' => 'Disponible',
                'New' => 'Nouveau',
                'Used' => 'Utilisé'
            ],
            'rw' => [
                'Product' => 'Igicuruzwa',
                'Category' => 'Icyiciro',
                'Price' => 'Igiciro',
                'Description' => 'Ibisobanuro',
                'Available' => 'Biraboneka',
                'New' => 'Gishya',
                'Used' => 'Yakoreshejwe'
            ]
        ];
        
        return $fallback[$target_language][$text] ?? $text;
    }
    
    /**
     * Get cached translation
     */
    private function getCachedTranslation($text, $source_lang, $target_lang) {
        try {
            $sql = "SELECT translated_text FROM google_translation_cache 
                    WHERE original_text = ? AND source_language = ? AND target_language = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$text, $source_lang, $target_lang]);
            $result = $stmt->fetch();
            
            return $result ? $result['translated_text'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Save translation to cache
     */
    private function saveCachedTranslation($original, $source_lang, $target_lang, $translated) {
        try {
            $sql = "INSERT INTO google_translation_cache (original_text, source_language, target_language, translated_text)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    translated_text = VALUES(translated_text),
                    updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$original, $source_lang, $target_lang, $translated]);
        } catch (PDOException $e) {
            error_log("Save Google translation cache error: " . $e->getMessage());
        }
    }
    
    /**
     * Translate product with all fields
     */
    public function translateProduct($product, $target_language = null) {
        $target_language = $target_language ?: $this->current_language;
        
        if ($target_language === 'en') {
            return $product; // Return original for English
        }
        
        $translated_product = $product;
        $translated_product['translated_name'] = $this->translateText($product['name'], $target_language);
        $translated_product['translated_description'] = $this->translateText($product['description'], $target_language);
        $translated_product['translated_category'] = $this->translateText($product['category'], $target_language);
        
        return $translated_product;
    }
    
    /**
     * Translate multiple products
     */
    public function translateProducts($products, $target_language = null) {
        $translated_products = [];
        foreach ($products as $product) {
            $translated_products[] = $this->translateProduct($product, $target_language);
        }
        return $translated_products;
    }
    
    /**
     * Get translation statistics
     */
    public function getTranslationStats() {
        try {
            $sql = "SELECT 
                        target_language,
                        COUNT(*) as translation_count,
                        MAX(updated_at) as last_translation
                    FROM google_translation_cache 
                    GROUP BY target_language";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Clear translation cache
     */
    public function clearTranslationCache($target_language = null) {
        try {
            if ($target_language) {
                $sql = "DELETE FROM google_translation_cache WHERE target_language = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$target_language]);
            } else {
                $sql = "TRUNCATE TABLE google_translation_cache";
                $this->pdo->exec($sql);
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
