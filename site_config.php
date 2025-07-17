<?php
// Site Configuration
// Set to true for lightweight version, false for full version
define('LITE_MODE', true);

// File size optimization settings
define('COMPRESS_CSS', true);
define('COMPRESS_JS', true);
define('OPTIMIZE_IMAGES', true);

// Performance settings
define('CACHE_ENABLED', true);
define('GZIP_COMPRESSION', true);

// Feature toggles for lite mode
define('LITE_FEATURES', [
    'google_translate' => true,
    'basic_stats' => true,
    'simple_navigation' => true,
    'minimal_styling' => true,
    'reduced_animations' => true,
    'compressed_assets' => true
]);

// Redirect functions for lite mode
function get_dashboard_url($user_type) {
    if (LITE_MODE) {
        switch ($user_type) {
            case 'admin':
                return 'admin_dashboard_lite.php';
            case 'seller':
                return 'seller_dashboard_lite.php';
            case 'buyer':
                return 'buyer_dashboard.php';
            default:
                return 'index_lite.php';
        }
    } else {
        switch ($user_type) {
            case 'admin':
                return 'admin_dashboard.php';
            case 'seller':
                return 'seller_dashboard.php';
            case 'buyer':
                return 'buyer_dashboard.php';
            default:
                return 'index.php';
        }
    }
}

function get_login_url() {
    return LITE_MODE ? 'login_lite.php' : 'login.php';
}

function get_index_url() {
    return LITE_MODE ? 'index_lite.php' : 'index.php';
}

function get_css_file() {
    return LITE_MODE ? 'assets/css/style.min.css' : 'assets/css/style.css';
}

// File size information
function get_file_sizes() {
    $files = [
        'index.php' => filesize('index.php'),
        'index_lite.php' => filesize('index_lite.php'),
        'login.php' => filesize('login.php'),
        'login_lite.php' => filesize('login_lite.php'),
        'seller_dashboard.php' => filesize('seller_dashboard.php'),
        'seller_dashboard_lite.php' => filesize('seller_dashboard_lite.php'),
        'admin_dashboard.php' => filesize('admin_dashboard.php'),
        'admin_dashboard_lite.php' => filesize('admin_dashboard_lite.php'),
        'style.css' => filesize('assets/css/style.css'),
        'style.min.css' => filesize('assets/css/style.min.css')
    ];
    
    return $files;
}

// Calculate total size reduction
function calculate_size_reduction() {
    $sizes = get_file_sizes();
    
    $full_size = $sizes['index.php'] + $sizes['login.php'] + $sizes['seller_dashboard.php'] + 
                 $sizes['admin_dashboard.php'] + $sizes['style.css'];
    
    $lite_size = $sizes['index_lite.php'] + $sizes['login_lite.php'] + $sizes['seller_dashboard_lite.php'] + 
                 $sizes['admin_dashboard_lite.php'] + $sizes['style.min.css'];
    
    $reduction = $full_size - $lite_size;
    $percentage = ($reduction / $full_size) * 100;
    
    return [
        'full_size' => $full_size,
        'lite_size' => $lite_size,
        'reduction' => $reduction,
        'percentage' => round($percentage, 2)
    ];
}

// Format file size
function format_file_size($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
