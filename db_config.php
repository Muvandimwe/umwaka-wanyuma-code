<?php
// Database configuration
$host = 'localhost';
$dbname = 'local_commerce_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If database doesn't exist, create it
    try {
        $pdo_temp = new PDO("mysql:host=$host;charset=utf8", $username, $password);
        $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        
        // Now connect to the created database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Create tables
        createTables($pdo);
        
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function createTables($pdo) {
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        city VARCHAR(50),
        country VARCHAR(50),
        user_type ENUM('seller', 'buyer') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        seller_id INT NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock_quantity INT DEFAULT 0,
        category VARCHAR(100),
        image VARCHAR(255),
        featured BOOLEAN DEFAULT FALSE,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Add stock_quantity column if it doesn't exist (for existing databases)
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN stock_quantity INT DEFAULT 0");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }

    // Add category column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(100)");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }

    // Add featured column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN featured BOOLEAN DEFAULT FALSE");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }
    
    // Orders table (enhanced)
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT NOT NULL,
        product_id INT NOT NULL,
        seller_id INT NOT NULL,
        quantity INT DEFAULT 1,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'payment_pending', 'confirmed', 'payment_received', 'preparing', 'shipped', 'delivered', 'completed', 'cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50),
        payment_details TEXT,
        delivery_address TEXT,
        delivery_notes TEXT,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Add new columns to existing orders table
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50)");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_details TEXT");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_address TEXT");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_notes TEXT");
    } catch (PDOException $e) {
        // Column already exists
    }

    // Update status enum to include new statuses
    try {
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'payment_pending', 'confirmed', 'payment_received', 'payment_disputed', 'preparing', 'shipped', 'delivered', 'completed', 'cancelled') DEFAULT 'pending'");
    } catch (PDOException $e) {
        // Already updated
    }

    // Add payment tracking columns
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_confirmed_at TIMESTAMP NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_confirmed_amount DECIMAL(10,2) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_notes TEXT NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    // Add multilingual product columns
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN name_en VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN name_rw VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN name_fr VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN description_en TEXT NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN description_rw TEXT NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN description_fr TEXT NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category_en VARCHAR(100) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category_rw VARCHAR(100) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category_fr VARCHAR(100) NULL");
    } catch (PDOException $e) {
        // Column already exists
    }

    // Add profile photo column to users table
    try {
        // Check if column exists first
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_photo'");
        $column_exists = $stmt->fetch();

        if (!$column_exists) {
            $pdo->exec("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) NULL");
        }
    } catch (PDOException $e) {
        // Column already exists or other error
    }
    
    // Product translations table (for multi-language support)
    $sql = "CREATE TABLE IF NOT EXISTS product_translations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        language_code VARCHAR(5) NOT NULL,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_product_language (product_id, language_code)
    )";
    $pdo->exec($sql);

    // Ensure category column exists in product_translations table
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM product_translations LIKE 'category'");
        $column_exists = $stmt->fetch();

        if (!$column_exists) {
            $pdo->exec("ALTER TABLE product_translations ADD COLUMN category VARCHAR(100) NULL");
        }
    } catch (PDOException $e) {
        // Column already exists or table doesn't exist yet
    }

    // Ensure updated_at column exists in product_translations table
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM product_translations LIKE 'updated_at'");
        $column_exists = $stmt->fetch();

        if (!$column_exists) {
            $pdo->exec("ALTER TABLE product_translations ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }
    } catch (PDOException $e) {
        // Column already exists or table doesn't exist yet
    }

    // Translation cache table for performance
    $pdo->exec("CREATE TABLE IF NOT EXISTS translation_cache (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_text TEXT NOT NULL,
        language_code VARCHAR(5) NOT NULL,
        translated_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_text_language (original_text(255), language_code)
    )");

    // Category translations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS category_translations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_category VARCHAR(100) NOT NULL,
        language_code VARCHAR(5) NOT NULL,
        translated_category VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_category_language (original_category, language_code)
    )");

    // User language preferences
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_language_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        preferred_language VARCHAR(5) NOT NULL DEFAULT 'en',
        date_format VARCHAR(20) DEFAULT 'Y-m-d',
        currency_format VARCHAR(20) DEFAULT 'USD',
        timezone VARCHAR(50) DEFAULT 'UTC',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_preference (user_id)
    )");
    
    // Create uploads directory if it doesn't exist
    if (!file_exists('../uploads')) {
        mkdir('../uploads', 0777, true);
    }
    if (!file_exists('../uploads/products')) {
        mkdir('../uploads/products', 0777, true);
    }
}

// Order messages table for buyer-seller communication
$sql = "CREATE TABLE IF NOT EXISTS order_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('general', 'delivery', 'payment', 'status_update') DEFAULT 'general',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
)";
$pdo->exec($sql);

// Notifications table
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('new_order', 'status_update', 'new_message', 'payment_received') NOT NULL,
    message TEXT NOT NULL,
    order_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";
$pdo->exec($sql);

// System settings table
$sql = "CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$pdo->exec($sql);

// Insert default system settings
$default_settings = [
    ['site_name', 'International Commerce', 'string', 'Name of the website'],
    ['site_description', 'Your trusted e-commerce platform', 'string', 'Site description for SEO'],
    ['admin_email', 'admin@internationalcommerce.com', 'string', 'Primary admin email'],
    ['currency', 'FRW', 'string', 'Default currency'],
    ['timezone', 'Africa/Kigali', 'string', 'Default timezone'],
    ['items_per_page', '12', 'number', 'Products per page'],
    ['max_upload_size', '5', 'number', 'Max upload size in MB'],
    ['allow_registration', '1', 'boolean', 'Allow user registration'],
    ['require_email_verification', '0', 'boolean', 'Require email verification'],
    ['maintenance_mode', '0', 'boolean', 'Maintenance mode status'],
    ['google_translate_enabled', '1', 'boolean', 'Google Translate enabled'],
    ['auto_approve_products', '0', 'boolean', 'Auto-approve new products'],
    ['low_stock_threshold', '5', 'number', 'Low stock warning threshold'],
    ['order_auto_complete_days', '7', 'number', 'Days to auto-complete orders'],
    ['backup_frequency', 'weekly', 'string', 'Backup frequency'],
    ['email_notifications', '1', 'boolean', 'Email notifications enabled'],
    ['sms_notifications', '0', 'boolean', 'SMS notifications enabled']
];

foreach ($default_settings as $setting) {
    $sql = "INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($setting);
}

// Helper functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function generate_unique_filename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

function upload_image($file, $upload_dir = 'uploads/products/') {
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.'];
    }
    
    $new_filename = generate_unique_filename($file['name']);
    $upload_path = $upload_dir . $new_filename;
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => $upload_path];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get system setting value
function get_setting($key, $default = null) {
    global $pdo;
    $sql = "SELECT setting_value FROM system_settings WHERE setting_key = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Update system setting
function update_setting($key, $value) {
    global $pdo;
    $sql = "INSERT INTO system_settings (setting_key, setting_value, updated_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$key, $value, $value]);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function require_seller() {
    require_login();
    if ($_SESSION['user_type'] !== 'seller') {
        header('Location: index.php');
        exit();
    }
}

function require_buyer() {
    require_login();
    if ($_SESSION['user_type'] !== 'buyer') {
        header('Location: index.php');
        exit();
    }
}

function require_admin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: admin_login.php');
        exit();
    }
}

function get_user_by_id($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function get_products($pdo, $limit = null, $seller_id = null, $language = 'en') {
    $sql = "SELECT p.*, u.username as seller_name, u.full_name as seller_full_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.status = 'active'";

    $params = [];

    if ($seller_id) {
        $sql .= " AND p.seller_id = ?";
        $params[] = $seller_id;
    }

    $sql .= " ORDER BY p.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Add translated names and descriptions
    foreach ($products as &$product) {
        $product['translated_name'] = get_translated_product_name($product, $language);
        $product['translated_description'] = get_translated_product_description($product, $language);
        $product['translated_category'] = get_translated_product_category($product, $language);
    }

    return $products;
}

function get_product_by_id($pdo, $product_id, $language = 'en') {
    $sql = "SELECT p.*, u.username as seller_name, u.full_name as seller_full_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $product['translated_name'] = get_translated_product_name($product, $language);
        $product['translated_description'] = get_translated_product_description($product, $language);
        $product['translated_category'] = get_translated_product_category($product, $language);
    }

    return $product;
}

function translate_product_with_google($text, $target_language) {
    // Simple Google Translate API simulation
    // In production, you would use actual Google Translate API
    $translations = [
        'en' => $text, // Original text
        'fr' => $text, // Would be translated to French
        'rw' => $text, // Would be translated to Kinyarwanda
        'sw' => $text, // Would be translated to Swahili
        'ar' => $text, // Would be translated to Arabic
        'es' => $text, // Would be translated to Spanish
        'pt' => $text, // Would be translated to Portuguese
        'de' => $text, // Would be translated to German
        'it' => $text, // Would be translated to Italian
        'zh' => $text, // Would be translated to Chinese
        'ja' => $text, // Would be translated to Japanese
        'ko' => $text, // Would be translated to Korean
        'hi' => $text, // Would be translated to Hindi
        'ur' => $text  // Would be translated to Urdu
    ];

    return $translations[$target_language] ?? $text;
}

function save_product_translation($pdo, $product_id, $language_code, $name, $description) {
    $sql = "INSERT INTO product_translations (product_id, language_code, name, description)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$product_id, $language_code, $name, $description]);
}

function update_product_stock($pdo, $product_id, $quantity_purchased) {
    $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$quantity_purchased, $product_id, $quantity_purchased]);
}

function get_product_stock($pdo, $product_id) {
    $sql = "SELECT stock_quantity FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $result = $stmt->fetch();
    return $result ? $result['stock_quantity'] : 0;
}

// Enhanced product translation functions
function get_translated_product_name($product, $lang = 'en') {
    $name_column = 'name_' . $lang;
    if (!empty($product[$name_column])) {
        return $product[$name_column];
    }
    // Fallback to original name if translation not available
    return $product['name'] ?? '';
}

function get_translated_product_description($product, $lang = 'en') {
    $desc_column = 'description_' . $lang;
    if (!empty($product[$desc_column])) {
        return $product[$desc_column];
    }
    // Fallback to original description if translation not available
    return $product['description'] ?? '';
}

function get_translated_product_category($product, $lang = 'en') {
    $cat_column = 'category_' . $lang;
    if (!empty($product[$cat_column])) {
        return $product[$cat_column];
    }
    // Fallback to original category if translation not available
    return $product['category'] ?? '';
}

// Auto-translate product using predefined translations
function auto_translate_product($product_name, $from_lang = 'en', $to_lang = 'rw') {
    $translations = [
        'en_to_rw' => [
            // Main Categories
            'Agriculture' => 'Ubuhinzi',
            'Sport' => 'Siporo',
            'Shoes' => 'Inkweto',
            'Cloths' => 'Imyenda',
            'Clothing' => 'Imyambaro',
            'Hand crafts' => 'Ubumenyi bw\'amaboko',

            // Related items for each category
            // Agriculture
            'Seeds' => 'Imbuto',
            'Fertilizer' => 'Ifumbire',
            'Tools' => 'Ibikoresho',
            'Crops' => 'Ibihingwa',
            'Vegetables' => 'Imboga',
            'Fruits' => 'Imbuto',

            // Sport
            'Ball' => 'Umupira',
            'Football' => 'Umupira w\'amaguru',
            'Basketball' => 'Umupira w\'intoki',
            'Tennis' => 'Tennis',
            'Running' => 'Kwiruka',
            'Swimming' => 'Koga',

            // Shoes
            'Sandals' => 'Amasandali',
            'Boots' => 'Inkweto ndende',
            'Sneakers' => 'Inkweto zo siporo',
            'Heels' => 'Inkweto z\'abagore',

            // Cloths/Clothing
            'Shirt' => 'Ishati',
            'Dress' => 'Umwenda',
            'Pants' => 'Ipantaro',
            'Jacket' => 'Ikoti',
            'Hat' => 'Ingofero',
            'Socks' => 'Amasokisi',
            'Underwear' => 'Imyenda y\'imbere',
            'Sweater' => 'Umwenda w\'ubushyuhe',

            // Hand crafts
            'Basket' => 'Igikoni',
            'Pottery' => 'Ibumba',
            'Jewelry' => 'Imitako',
            'Artwork' => 'Ubugeni',
            'Sculpture' => 'Igishushanyo',
            'Weaving' => 'Kuruka'
        ],
        'en_to_fr' => [
            // Main Categories
            'Agriculture' => 'Agriculture',
            'Sport' => 'Sport',
            'Shoes' => 'Chaussures',
            'Cloths' => 'Tissus',
            'Clothing' => 'Vêtements',
            'Hand crafts' => 'Artisanat',

            // Related items for each category
            // Agriculture
            'Seeds' => 'Graines',
            'Fertilizer' => 'Engrais',
            'Tools' => 'Outils',
            'Crops' => 'Cultures',
            'Vegetables' => 'Légumes',
            'Fruits' => 'Fruits',

            // Sport
            'Ball' => 'Ballon',
            'Football' => 'Football',
            'Basketball' => 'Basketball',
            'Tennis' => 'Tennis',
            'Running' => 'Course',
            'Swimming' => 'Natation',

            // Shoes
            'Sandals' => 'Sandales',
            'Boots' => 'Bottes',
            'Sneakers' => 'Baskets',
            'Heels' => 'Talons',

            // Cloths/Clothing
            'Shirt' => 'Chemise',
            'Dress' => 'Robe',
            'Pants' => 'Pantalon',
            'Jacket' => 'Veste',
            'Hat' => 'Chapeau',
            'Socks' => 'Chaussettes',
            'Underwear' => 'Sous-vêtements',
            'Sweater' => 'Pull',

            // Hand crafts
            'Basket' => 'Panier',
            'Pottery' => 'Poterie',
            'Jewelry' => 'Bijoux',
            'Artwork' => 'Œuvre d\'art',
            'Sculpture' => 'Sculpture',
            'Weaving' => 'Tissage'
        ]
    ];

    $translation_key = $from_lang . '_to_' . $to_lang;

    if (isset($translations[$translation_key][$product_name])) {
        return $translations[$translation_key][$product_name];
    }

    // Return original if no translation found
    return $product_name;
}

// Update existing products with translations
function update_product_translations($pdo) {
    try {
        // Get all products
        $stmt = $pdo->query("SELECT id, name, description, category FROM products");
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $name_en = $product['name'];
            $desc_en = $product['description'];
            $cat_en = $product['category'];

            // Generate translations
            $name_rw = auto_translate_product($name_en, 'en', 'rw');
            $name_fr = auto_translate_product($name_en, 'en', 'fr');

            $cat_rw = auto_translate_product($cat_en, 'en', 'rw');
            $cat_fr = auto_translate_product($cat_en, 'en', 'fr');

            // Update product with translations
            $update_sql = "UPDATE products SET
                          name_en = ?, name_rw = ?, name_fr = ?,
                          description_en = ?, description_rw = ?, description_fr = ?,
                          category_en = ?, category_rw = ?, category_fr = ?
                          WHERE id = ?";

            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([
                $name_en, $name_rw, $name_fr,
                $desc_en, $desc_en, $desc_en, // Keep description same for now
                $cat_en, $cat_rw, $cat_fr,
                $product['id']
            ]);
        }

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Run translation update (only once)
try {
    $check_translation = $pdo->query("SELECT name_en FROM products LIMIT 1");
    if ($check_translation && $check_translation->fetch()) {
        // Check if translations are empty and update them
        $empty_check = $pdo->query("SELECT COUNT(*) as count FROM products WHERE name_en IS NULL OR name_en = ''");
        $empty_count = $empty_check->fetch()['count'];

        if ($empty_count > 0) {
            update_product_translations($pdo);
        }
    }
} catch (PDOException $e) {
    // Columns don't exist yet or other error
}
?>
