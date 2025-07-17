<?php
/**
 * Google Translate Integration for Product Translation
 * This file handles automatic translation of product information
 */

class GoogleTranslateAPI {
    private $api_key;
    private $base_url = 'https://translation.googleapis.com/language/translate/v2';
    
    public function __construct($api_key = null) {
        // For demo purposes, we'll simulate translations
        // In production, you would use a real Google Translate API key
        $this->api_key = $api_key ?: 'demo_key';
    }
    
    /**
     * Translate text to target language
     */
    public function translate($text, $target_language, $source_language = 'en') {
        // For demo purposes, we'll return simulated translations
        // In production, this would make actual API calls to Google Translate
        
        if ($target_language === $source_language) {
            return $text;
        }
        
        // Simulate translation based on common product terms
        $translations = $this->getSimulatedTranslations($text, $target_language);
        
        return $translations ?: $text;
    }
    
    /**
     * Translate multiple texts at once
     */
    public function translateBatch($texts, $target_language, $source_language = 'en') {
        $translations = [];
        foreach ($texts as $text) {
            $translations[] = $this->translate($text, $target_language, $source_language);
        }
        return $translations;
    }
    
    /**
     * Get supported languages
     */
    public function getSupportedLanguages() {
        return [
            'en' => 'English',
            'fr' => 'Français',
            'rw' => 'Kinyarwanda',
            'sw' => 'Kiswahili',
            'ar' => 'العربية',
            'es' => 'Español',
            'pt' => 'Português',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
            'hi' => 'हिन्दी',
            'ur' => 'اردو'
        ];
    }
    
    /**
     * Simulate translations for demo purposes
     * In production, this would be replaced with actual Google Translate API calls
     */
    private function getSimulatedTranslations($text, $target_language) {
        $common_translations = [
            'fr' => [
                'Electronics' => 'Électronique',
                'Clothing' => 'Vêtements',
                'Books' => 'Livres',
                'Home & Garden' => 'Maison et Jardin',
                'Sports' => 'Sports',
                'Toys' => 'Jouets',
                'Beauty' => 'Beauté',
                'Automotive' => 'Automobile',
                'Phone' => 'Téléphone',
                'Laptop' => 'Ordinateur portable',
                'Shirt' => 'Chemise',
                'Book' => 'Livre',
                'Price' => 'Prix',
                'Stock' => 'Stock',
                'Available' => 'Disponible',
                'Out of Stock' => 'Rupture de stock',
                'Add to Cart' => 'Ajouter au panier',
                'Buy Now' => 'Acheter maintenant'
            ],
            'rw' => [
                'Electronics' => 'Ikoranabuhanga',
                'Clothing' => 'Imyambaro',
                'Books' => 'Ibitabo',
                'Home & Garden' => 'Inzu n\'Ubusitani',
                'Sports' => 'Siporo',
                'Toys' => 'Ibikinisho',
                'Beauty' => 'Ubwiza',
                'Automotive' => 'Ibinyabiziga',
                'Phone' => 'Telefoni',
                'Laptop' => 'Mudasobwa',
                'Shirt' => 'Ishati',
                'Book' => 'Igitabo',
                'Price' => 'Igiciro',
                'Stock' => 'Ububiko',
                'Available' => 'Biraboneka',
                'Out of Stock' => 'Nta bubiko',
                'Add to Cart' => 'Shyira mu gitebo',
                'Buy Now' => 'Gura ubu'
            ],
            'sw' => [
                'Electronics' => 'Elektroniki',
                'Clothing' => 'Nguo',
                'Books' => 'Vitabu',
                'Home & Garden' => 'Nyumba na Bustani',
                'Sports' => 'Michezo',
                'Toys' => 'Vichezeo',
                'Beauty' => 'Uzuri',
                'Automotive' => 'Magari',
                'Phone' => 'Simu',
                'Laptop' => 'Kompyuta',
                'Shirt' => 'Shati',
                'Book' => 'Kitabu',
                'Price' => 'Bei',
                'Stock' => 'Hifadhi',
                'Available' => 'Inapatikana',
                'Out of Stock' => 'Haipatikani',
                'Add to Cart' => 'Ongeza kwenye kikapu',
                'Buy Now' => 'Nunua sasa'
            ],
            'ar' => [
                'Electronics' => 'إلكترونيات',
                'Clothing' => 'ملابس',
                'Books' => 'كتب',
                'Home & Garden' => 'المنزل والحديقة',
                'Sports' => 'رياضة',
                'Toys' => 'ألعاب',
                'Beauty' => 'جمال',
                'Automotive' => 'السيارات',
                'Phone' => 'هاتف',
                'Laptop' => 'حاسوب محمول',
                'Shirt' => 'قميص',
                'Book' => 'كتاب',
                'Price' => 'السعر',
                'Stock' => 'المخزون',
                'Available' => 'متوفر',
                'Out of Stock' => 'نفد المخزون',
                'Add to Cart' => 'أضف إلى السلة',
                'Buy Now' => 'اشتري الآن'
            ],
            'es' => [
                'Electronics' => 'Electrónicos',
                'Clothing' => 'Ropa',
                'Books' => 'Libros',
                'Home & Garden' => 'Hogar y Jardín',
                'Sports' => 'Deportes',
                'Toys' => 'Juguetes',
                'Beauty' => 'Belleza',
                'Automotive' => 'Automotriz',
                'Phone' => 'Teléfono',
                'Laptop' => 'Portátil',
                'Shirt' => 'Camisa',
                'Book' => 'Libro',
                'Price' => 'Precio',
                'Stock' => 'Inventario',
                'Available' => 'Disponible',
                'Out of Stock' => 'Agotado',
                'Add to Cart' => 'Agregar al carrito',
                'Buy Now' => 'Comprar ahora'
            ]
        ];
        
        // Check if we have a direct translation
        if (isset($common_translations[$target_language][$text])) {
            return $common_translations[$target_language][$text];
        }
        
        // For longer texts, try to translate word by word
        $words = explode(' ', $text);
        $translated_words = [];
        
        foreach ($words as $word) {
            if (isset($common_translations[$target_language][$word])) {
                $translated_words[] = $common_translations[$target_language][$word];
            } else {
                $translated_words[] = $word; // Keep original if no translation
            }
        }
        
        return implode(' ', $translated_words);
    }
}

/**
 * Helper function to get translated product data
 */
function get_translated_product($pdo, $product_id, $language = 'en') {
    // First try to get existing translation
    $product = get_product_by_id($pdo, $product_id, $language);
    
    if (!$product) {
        return null;
    }
    
    // If no translation exists and language is not English, create one
    if ($language !== 'en' && $product['translated_name'] === $product['name']) {
        $translator = new GoogleTranslateAPI();
        
        $translated_name = $translator->translate($product['name'], $language);
        $translated_description = $translator->translate($product['description'], $language);
        
        // Save translation to database
        save_product_translation($pdo, $product_id, $language, $translated_name, $translated_description);
        
        // Update the product data
        $product['translated_name'] = $translated_name;
        $product['translated_description'] = $translated_description;
    }
    
    return $product;
}

/**
 * Translate all products for a specific language
 */
function translate_all_products($pdo, $language = 'en') {
    if ($language === 'en') {
        return; // No need to translate English products
    }
    
    $translator = new GoogleTranslateAPI();
    
    // Get all products that don't have translations for this language
    $sql = "SELECT p.id, p.name, p.description 
            FROM products p 
            LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_code = ?
            WHERE pt.id IS NULL";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$language]);
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $translated_name = $translator->translate($product['name'], $language);
        $translated_description = $translator->translate($product['description'], $language);
        
        save_product_translation($pdo, $product['id'], $language, $translated_name, $translated_description);
    }
}
?>
