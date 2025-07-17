<?php
/**
 * Comprehensive Translation System
 * Provides complete translation capabilities for English, French, and Kinyarwanda
 */

class ComprehensiveTranslator {
    private $pdo;
    private $current_language;
    private $translation_cache = [];
    
    // Complete translation dictionaries
    private $translations = [
        'en' => [
            // Product Categories
            'Electronics' => 'Electronics',
            'Clothing' => 'Clothing',
            'Books' => 'Books',
            'Food' => 'Food',
            'Shoes' => 'Shoes',
            'Bags' => 'Bags',
            'Phones' => 'Phones',
            'Computers' => 'Computers',
            'Furniture' => 'Furniture',
            'Toys' => 'Toys',
            'Sports' => 'Sports',
            'Beauty' => 'Beauty',
            'Health' => 'Health',
            'Home' => 'Home',
            'Garden' => 'Garden',
            'Kitchen' => 'Kitchen',
            'Bathroom' => 'Bathroom',
            'Office' => 'Office',
            'School' => 'School',
            'Car' => 'Car',
            'Motorcycle' => 'Motorcycle',
            'Bicycle' => 'Bicycle',
            'Tools' => 'Tools',
            'Music' => 'Music',
            'Movies' => 'Movies',
            'Games' => 'Games',
            
            // Product Attributes
            'New' => 'New',
            'Used' => 'Used',
            'Available' => 'Available',
            'Out of Stock' => 'Out of Stock',
            'Price' => 'Price',
            'Quality' => 'Quality',
            'Brand' => 'Brand',
            'Size' => 'Size',
            'Color' => 'Color',
            'Material' => 'Material',
            'Weight' => 'Weight',
            'Description' => 'Description',
            'Features' => 'Features',
            'Specifications' => 'Specifications',
            'Reviews' => 'Reviews',
            'Rating' => 'Rating',
            
            // Business Terms
            'Seller' => 'Seller',
            'Buyer' => 'Buyer',
            'Order' => 'Order',
            'Payment' => 'Payment',
            'Delivery' => 'Delivery',
            'Shipping' => 'Shipping',
            'Return' => 'Return',
            'Warranty' => 'Warranty',
            'Support' => 'Support',
            'Contact' => 'Contact',
            'Address' => 'Address',
            'Phone' => 'Phone',
            'Email' => 'Email',
            'Website' => 'Website',
            'Location' => 'Location',
            'City' => 'City',
            'Country' => 'Country',
            'Region' => 'Region',
            'District' => 'District',
            
            // Common Products
            'Smartphone' => 'Smartphone',
            'Laptop' => 'Laptop',
            'Tablet' => 'Tablet',
            'Headphones' => 'Headphones',
            'Speaker' => 'Speaker',
            'Camera' => 'Camera',
            'Watch' => 'Watch',
            'Jewelry' => 'Jewelry',
            'Perfume' => 'Perfume',
            'Cosmetics' => 'Cosmetics',
            'Shampoo' => 'Shampoo',
            'Soap' => 'Soap',
            'Toothpaste' => 'Toothpaste',
            'Medicine' => 'Medicine',
            'Vitamins' => 'Vitamins',
            'Supplements' => 'Supplements',
            'Bread' => 'Bread',
            'Rice' => 'Rice',
            'Beans' => 'Beans',
            'Meat' => 'Meat',
            'Fish' => 'Fish',
            'Vegetables' => 'Vegetables',
            'Fruits' => 'Fruits',
            'Milk' => 'Milk',
            'Cheese' => 'Cheese',
            'Eggs' => 'Eggs',
            'Coffee' => 'Coffee',
            'Tea' => 'Tea',
            'Water' => 'Water',
            'Juice' => 'Juice',
            'Soda' => 'Soda',
            'Beer' => 'Beer',
            'Wine' => 'Wine',
            
            // Clothing Items
            'T-Shirt' => 'T-Shirt',
            'Shirt' => 'Shirt',
            'Pants' => 'Pants',
            'Jeans' => 'Jeans',
            'Dress' => 'Dress',
            'Skirt' => 'Skirt',
            'Jacket' => 'Jacket',
            'Coat' => 'Coat',
            'Sweater' => 'Sweater',
            'Hoodie' => 'Hoodie',
            'Underwear' => 'Underwear',
            'Socks' => 'Socks',
            'Hat' => 'Hat',
            'Cap' => 'Cap',
            'Scarf' => 'Scarf',
            'Gloves' => 'Gloves',
            'Belt' => 'Belt',
            'Tie' => 'Tie',
            'Suit' => 'Suit',
            'Uniform' => 'Uniform',
            
            // Action Words
            'Buy' => 'Buy',
            'Sell' => 'Sell',
            'Search' => 'Search',
            'Filter' => 'Filter',
            'Sort' => 'Sort',
            'Add' => 'Add',
            'Remove' => 'Remove',
            'Edit' => 'Edit',
            'Delete' => 'Delete',
            'Save' => 'Save',
            'Cancel' => 'Cancel',
            'Submit' => 'Submit',
            'Login' => 'Login',
            'Logout' => 'Logout',
            'Register' => 'Register',
            'Update' => 'Update',
            'Upload' => 'Upload',
            'Download' => 'Download',
            'Share' => 'Share',
            'Like' => 'Like',
            'Comment' => 'Comment',
            'Follow' => 'Follow',
            'Subscribe' => 'Subscribe',
            
            // Descriptive Words
            'Good' => 'Good',
            'Bad' => 'Bad',
            'Best' => 'Best',
            'Worst' => 'Worst',
            'Better' => 'Better',
            'Excellent' => 'Excellent',
            'Perfect' => 'Perfect',
            'Amazing' => 'Amazing',
            'Beautiful' => 'Beautiful',
            'Ugly' => 'Ugly',
            'Fast' => 'Fast',
            'Slow' => 'Slow',
            'Big' => 'Big',
            'Small' => 'Small',
            'Large' => 'Large',
            'Medium' => 'Medium',
            'Tiny' => 'Tiny',
            'Huge' => 'Huge',
            'Long' => 'Long',
            'Short' => 'Short',
            'Wide' => 'Wide',
            'Narrow' => 'Narrow',
            'Thick' => 'Thick',
            'Thin' => 'Thin',
            'Heavy' => 'Heavy',
            'Light' => 'Light',
            'Strong' => 'Strong',
            'Weak' => 'Weak',
            'Hard' => 'Hard',
            'Soft' => 'Soft',
            'Hot' => 'Hot',
            'Cold' => 'Cold',
            'Warm' => 'Warm',
            'Cool' => 'Cool',
            'Dry' => 'Dry',
            'Wet' => 'Wet',
            'Clean' => 'Clean',
            'Dirty' => 'Dirty',
            'Fresh' => 'Fresh',
            'Old' => 'Old',
            'Young' => 'Young',
            'Modern' => 'Modern',
            'Traditional' => 'Traditional',
            'Popular' => 'Popular',
            'Rare' => 'Rare',
            'Common' => 'Common',
            'Special' => 'Special',
            'Normal' => 'Normal',
            'Unique' => 'Unique',
            'Original' => 'Original',
            'Copy' => 'Copy',
            'Real' => 'Real',
            'Fake' => 'Fake',
            'True' => 'True',
            'False' => 'False',
            'Right' => 'Right',
            'Wrong' => 'Wrong',
            'Correct' => 'Correct',
            'Incorrect' => 'Incorrect',
            'Easy' => 'Easy',
            'Difficult' => 'Difficult',
            'Simple' => 'Simple',
            'Complex' => 'Complex',
            'Clear' => 'Clear',
            'Unclear' => 'Unclear',
            'Visible' => 'Visible',
            'Hidden' => 'Hidden',
            'Open' => 'Open',
            'Closed' => 'Closed',
            'Full' => 'Full',
            'Empty' => 'Empty',
            'Complete' => 'Complete',
            'Incomplete' => 'Incomplete',
            'Finished' => 'Finished',
            'Unfinished' => 'Unfinished',
            'Ready' => 'Ready',
            'Not Ready' => 'Not Ready',
            'Active' => 'Active',
            'Inactive' => 'Inactive',
            'Online' => 'Online',
            'Offline' => 'Offline',
            'Connected' => 'Connected',
            'Disconnected' => 'Disconnected',
            'Safe' => 'Safe',
            'Dangerous' => 'Dangerous',
            'Secure' => 'Secure',
            'Insecure' => 'Insecure',
            'Public' => 'Public',
            'Private' => 'Private',
            'Free' => 'Free',
            'Paid' => 'Paid',
            'Cheap' => 'Cheap',
            'Expensive' => 'Expensive',
            'Affordable' => 'Affordable',
            'Luxury' => 'Luxury',
            'Premium' => 'Premium',
            'Standard' => 'Standard',
            'Basic' => 'Basic',
            'Advanced' => 'Advanced',
            'Professional' => 'Professional',
            'Amateur' => 'Amateur',
            'Expert' => 'Expert',
            'Beginner' => 'Beginner',
            'Intermediate' => 'Intermediate',
            'High' => 'High',
            'Low' => 'Low',
            'Top' => 'Top',
            'Bottom' => 'Bottom',
            'Front' => 'Front',
            'Back' => 'Back',
            'Left' => 'Left',
            'Right' => 'Right',
            'Center' => 'Center',
            'Side' => 'Side',
            'Inside' => 'Inside',
            'Outside' => 'Outside',
            'Above' => 'Above',
            'Below' => 'Below',
            'Over' => 'Over',
            'Under' => 'Under',
            'Near' => 'Near',
            'Far' => 'Far',
            'Close' => 'Close',
            'Distant' => 'Distant',
            'Here' => 'Here',
            'There' => 'There',
            'Everywhere' => 'Everywhere',
            'Nowhere' => 'Nowhere',
            'Somewhere' => 'Somewhere',
            'Anywhere' => 'Anywhere'
        ]
    ];
    
    public function __construct($pdo, $language = 'en') {
        $this->pdo = $pdo;
        $this->current_language = $language;
        $this->initializeTranslations();
    }
    
    /**
     * Initialize all translation dictionaries
     */
    private function initializeTranslations() {
        // French translations
        $this->translations['fr'] = [
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

            // Product Attributes
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
            'Description' => 'Description',
            'Features' => 'Caractéristiques',
            'Specifications' => 'Spécifications',
            'Reviews' => 'Avis',
            'Rating' => 'Note',

            // Business Terms
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
            'Location' => 'Emplacement',
            'City' => 'Ville',
            'Country' => 'Pays',
            'Region' => 'Région',
            'District' => 'District',

            // Common Products
            'Smartphone' => 'Smartphone',
            'Laptop' => 'Ordinateur portable',
            'Tablet' => 'Tablette',
            'Headphones' => 'Écouteurs',
            'Speaker' => 'Haut-parleur',
            'Camera' => 'Appareil photo',
            'Watch' => 'Montre',
            'Jewelry' => 'Bijoux',
            'Perfume' => 'Parfum',
            'Cosmetics' => 'Cosmétiques',
            'Shampoo' => 'Shampooing',
            'Soap' => 'Savon',
            'Toothpaste' => 'Dentifrice',
            'Medicine' => 'Médicament',
            'Vitamins' => 'Vitamines',
            'Supplements' => 'Suppléments',
            'Bread' => 'Pain',
            'Rice' => 'Riz',
            'Beans' => 'Haricots',
            'Meat' => 'Viande',
            'Fish' => 'Poisson',
            'Vegetables' => 'Légumes',
            'Fruits' => 'Fruits',
            'Milk' => 'Lait',
            'Cheese' => 'Fromage',
            'Eggs' => 'Œufs',
            'Coffee' => 'Café',
            'Tea' => 'Thé',
            'Water' => 'Eau',
            'Juice' => 'Jus',
            'Soda' => 'Soda',
            'Beer' => 'Bière',
            'Wine' => 'Vin',

            // Clothing Items
            'T-Shirt' => 'T-shirt',
            'Shirt' => 'Chemise',
            'Pants' => 'Pantalon',
            'Jeans' => 'Jean',
            'Dress' => 'Robe',
            'Skirt' => 'Jupe',
            'Jacket' => 'Veste',
            'Coat' => 'Manteau',
            'Sweater' => 'Pull',
            'Hoodie' => 'Sweat à capuche',
            'Underwear' => 'Sous-vêtements',
            'Socks' => 'Chaussettes',
            'Hat' => 'Chapeau',
            'Cap' => 'Casquette',
            'Scarf' => 'Écharpe',
            'Gloves' => 'Gants',
            'Belt' => 'Ceinture',
            'Tie' => 'Cravate',
            'Suit' => 'Costume',
            'Uniform' => 'Uniforme',

            // Action Words
            'Buy' => 'Acheter',
            'Sell' => 'Vendre',
            'Search' => 'Rechercher',
            'Filter' => 'Filtrer',
            'Sort' => 'Trier',
            'Add' => 'Ajouter',
            'Remove' => 'Supprimer',
            'Edit' => 'Modifier',
            'Delete' => 'Supprimer',
            'Save' => 'Enregistrer',
            'Cancel' => 'Annuler',
            'Submit' => 'Soumettre',
            'Login' => 'Connexion',
            'Logout' => 'Déconnexion',
            'Register' => 'S\'inscrire',
            'Update' => 'Mettre à jour',
            'Upload' => 'Télécharger',
            'Download' => 'Télécharger',
            'Share' => 'Partager',
            'Like' => 'Aimer',
            'Comment' => 'Commenter',
            'Follow' => 'Suivre',
            'Subscribe' => 'S\'abonner'
        ];
        
        // Kinyarwanda translations
        $this->translations['rw'] = [
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
            'Games' => 'Imikino'
        ];
    }
    
    /**
     * Translate text to target language
     */
    public function translateText($text, $target_language = null) {
        if (empty($text)) {
            return $text;
        }
        
        $target_language = $target_language ?: $this->current_language;
        
        // If target is English, return original
        if ($target_language === 'en') {
            return $text;
        }
        
        // Check cache first
        $cache_key = md5($text . $target_language);
        if (isset($this->translation_cache[$cache_key])) {
            return $this->translation_cache[$cache_key];
        }
        
        // Check database cache
        $cached = $this->getCachedTranslation($text, $target_language);
        if ($cached) {
            $this->translation_cache[$cache_key] = $cached;
            return $cached;
        }
        
        // Perform translation
        $translated = $this->performTranslation($text, $target_language);
        
        // Cache the result
        $this->translation_cache[$cache_key] = $translated;
        $this->saveCachedTranslation($text, $target_language, $translated);
        
        return $translated;
    }
    
    /**
     * Perform actual translation
     */
    private function performTranslation($text, $target_language) {
        // Direct translation lookup
        if (isset($this->translations[$target_language][$text])) {
            return $this->translations[$target_language][$text];
        }
        
        // Word-by-word translation for phrases
        $words = explode(' ', $text);
        $translated_words = [];
        
        foreach ($words as $word) {
            $clean_word = trim($word, '.,!?;:()[]{}"\'-');
            if (isset($this->translations[$target_language][$clean_word])) {
                $translated_words[] = $this->translations[$target_language][$clean_word];
            } else {
                $translated_words[] = $word; // Keep original if no translation
            }
        }
        
        return implode(' ', $translated_words);
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
     * Save translation to database cache
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
            error_log("Save translation cache error: " . $e->getMessage());
        }
    }
    
    /**
     * Get all available translations for a text
     */
    public function getAllTranslations($text) {
        return [
            'en' => $text,
            'fr' => $this->translateText($text, 'fr'),
            'rw' => $this->translateText($text, 'rw')
        ];
    }
    
    /**
     * Translate product data
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
}
?>
