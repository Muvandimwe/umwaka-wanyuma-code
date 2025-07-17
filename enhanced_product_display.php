<?php
/**
 * Enhanced Product Display System with Full Translation Support
 */

include_once 'enhanced_translation.php';
include_once 'multilingual_interface.php';

class EnhancedProductDisplay {
    private $translation_system;
    private $multilingual_interface;
    private $pdo;
    
    public function __construct($pdo, $translation_system, $multilingual_interface) {
        $this->pdo = $pdo;
        $this->translation_system = $translation_system;
        $this->multilingual_interface = $multilingual_interface;
    }
    
    /**
     * Display product card with full translation
     */
    public function displayProductCard($product, $show_seller_info = true) {
        $current_language = $this->translation_system->getCurrentLanguage();
        
        // Get translated product data
        $translated_product = $this->translation_system->getTranslatedProduct($product['id'], $current_language);
        
        if (!$translated_product) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="enhanced-product-card <?php echo $this->multilingual_interface->getLanguageClasses(); ?>" 
             data-product-id="<?php echo $product['id']; ?>"
             data-language="<?php echo $current_language; ?>">
            
            <!-- Product Image -->
            <div class="product-image-container">
                <?php if (!empty($product['image'])): ?>
                    <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($translated_product['translated_name']); ?>"
                         class="product-image">
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <i class="fas fa-image"></i>
                        <span><?php echo $this->multilingual_interface->translate('no_image', 'No Image'); ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Language indicator -->
                <div class="language-indicator">
                    <span class="flag"><?php echo $this->translation_system->getSupportedLanguages()[$current_language]['flag']; ?></span>
                </div>
                
                <!-- Stock status -->
                <div class="stock-status <?php echo $product['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <i class="fas fa-check-circle"></i>
                        <?php echo $this->multilingual_interface->translate('in_stock', 'In Stock'); ?>
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i>
                        <?php echo $this->multilingual_interface->translate('out_of_stock', 'Out of Stock'); ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-info">
                <!-- Category -->
                <div class="product-category">
                    <i class="fas fa-tag"></i>
                    <span><?php echo htmlspecialchars($translated_product['translated_category']); ?></span>
                </div>
                
                <!-- Product Name -->
                <h3 class="product-name">
                    <?php echo htmlspecialchars($translated_product['translated_name']); ?>
                </h3>
                
                <!-- Product Description -->
                <p class="product-description">
                    <?php echo htmlspecialchars(substr($translated_product['translated_description'], 0, 100)); ?>
                    <?php if (strlen($translated_product['translated_description']) > 100): ?>
                        <span class="read-more">...</span>
                    <?php endif; ?>
                </p>
                
                <!-- Price and Stock -->
                <div class="product-pricing">
                    <div class="price">
                        <span class="currency-label"><?php echo $this->multilingual_interface->translate('price', 'Price'); ?>:</span>
                        <span class="amount"><?php echo $this->multilingual_interface->formatCurrency($product['price']); ?></span>
                    </div>
                    
                    <div class="stock-info">
                        <span class="stock-label"><?php echo $this->multilingual_interface->translate('available', 'Available'); ?>:</span>
                        <span class="stock-amount"><?php echo $this->multilingual_interface->formatNumber($product['stock_quantity']); ?></span>
                    </div>
                </div>
                
                <!-- Seller Info -->
                <?php if ($show_seller_info): ?>
                    <div class="seller-info">
                        <i class="fas fa-user"></i>
                        <span class="seller-label"><?php echo $this->multilingual_interface->translate('seller', 'Seller'); ?>:</span>
                        <span class="seller-name"><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Date Added -->
                <div class="date-added">
                    <i class="fas fa-calendar"></i>
                    <span class="date-label"><?php echo $this->multilingual_interface->translate('added', 'Added'); ?>:</span>
                    <span class="date-value"><?php echo $this->multilingual_interface->formatDate($product['created_at']); ?></span>
                </div>
                
                <!-- Action Buttons -->
                <div class="product-actions">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <button class="btn btn-primary buy-now-btn" 
                                onclick="buyProduct(<?php echo $product['id']; ?>)"
                                data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <?php echo $this->multilingual_interface->translate('buy_now', 'Buy Now'); ?>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-ban"></i>
                            <?php echo $this->multilingual_interface->translate('out_of_stock', 'Out of Stock'); ?>
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline view-details-btn" 
                            onclick="viewProductDetails(<?php echo $product['id']; ?>)">
                        <i class="fas fa-eye"></i>
                        <?php echo $this->multilingual_interface->translate('view_details', 'View Details'); ?>
                    </button>
                </div>
                
                <!-- Translation Notice -->
                <?php if ($current_language !== 'en'): ?>
                    <div class="translation-notice">
                        <i class="fas fa-language"></i>
                        <small><?php echo $this->multilingual_interface->translate('auto_translated', 'Automatically translated'); ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .enhanced-product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .enhanced-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .product-image-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .enhanced-product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-image-placeholder {
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        
        .product-image-placeholder i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .language-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .stock-status {
            position: absolute;
            bottom: 10px;
            left: 10px;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .stock-status.in-stock {
            background: #28a745;
            color: white;
        }
        
        .stock-status.out-of-stock {
            background: #dc3545;
            color: white;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-category {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .product-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .product-pricing {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .price, .stock-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .currency-label, .stock-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .amount {
            font-size: 1.1rem;
            font-weight: 700;
            color: #28a745;
        }
        
        .stock-amount {
            font-size: 1rem;
            font-weight: 600;
            color: #007bff;
        }
        
        .seller-info, .date-added {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .product-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        
        .product-actions .btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .translation-notice {
            margin-top: 1rem;
            padding: 0.5rem;
            background: #e3f2fd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #1976d2;
        }
        
        /* RTL Support */
        .enhanced-product-card.rtl {
            direction: rtl;
            text-align: right;
        }
        
        .enhanced-product-card.rtl .language-indicator {
            right: auto;
            left: 10px;
        }
        
        .enhanced-product-card.rtl .stock-status {
            left: auto;
            right: 10px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .product-pricing {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .product-actions .btn {
                width: 100%;
            }
        }
        
        /* Special font support for certain languages */
        .enhanced-product-card.special-font {
            font-family: 'Noto Sans', 'Arial Unicode MS', sans-serif;
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Display product grid with pagination and filtering
     */
    public function displayProductGrid($products, $current_page = 1, $total_pages = 1, $category_filter = null) {
        $current_language = $this->translation_system->getCurrentLanguage();
        
        ob_start();
        ?>
        <div class="enhanced-product-grid-container <?php echo $this->multilingual_interface->getLanguageClasses(); ?>">
            
            <!-- Grid Header -->
            <div class="grid-header">
                <div class="grid-info">
                    <h2><?php echo $this->multilingual_interface->translate('products', 'Products'); ?></h2>
                    <span class="product-count">
                        <?php echo $this->multilingual_interface->formatNumber(count($products)); ?> 
                        <?php echo $this->multilingual_interface->translate('products_found', 'products found'); ?>
                    </span>
                </div>
                
                <div class="grid-controls">
                    <!-- Sort Options -->
                    <select class="sort-select" onchange="sortProducts(this.value)">
                        <option value="newest"><?php echo $this->multilingual_interface->translate('sort_newest', 'Newest First'); ?></option>
                        <option value="price_low"><?php echo $this->multilingual_interface->translate('sort_price_low', 'Price: Low to High'); ?></option>
                        <option value="price_high"><?php echo $this->multilingual_interface->translate('sort_price_high', 'Price: High to Low'); ?></option>
                        <option value="name"><?php echo $this->multilingual_interface->translate('sort_name', 'Name A-Z'); ?></option>
                    </select>
                    
                    <!-- View Toggle -->
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" title="<?php echo $this->multilingual_interface->translate('grid_view', 'Grid View'); ?>">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="list" title="<?php echo $this->multilingual_interface->translate('list_view', 'List View'); ?>">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="products-grid" id="productsGrid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3><?php echo $this->multilingual_interface->translate('no_products', 'No Products Found'); ?></h3>
                        <p><?php echo $this->multilingual_interface->translate('no_products_desc', 'Try adjusting your search or filter criteria'); ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php echo $this->displayProductCard($product); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <nav class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=<?php echo $current_page - 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>" 
                               class="page-btn prev">
                                <i class="fas fa-chevron-left"></i>
                                <?php echo $this->multilingual_interface->translate('previous', 'Previous'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>" 
                               class="page-btn <?php echo $i === $current_page ? 'active' : ''; ?>">
                                <?php echo $this->multilingual_interface->formatNumber($i); ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?page=<?php echo $current_page + 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>" 
                               class="page-btn next">
                                <?php echo $this->multilingual_interface->translate('next', 'Next'); ?>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        .enhanced-product-grid-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .grid-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .grid-info h2 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        
        .product-count {
            color: #666;
            font-size: 0.9rem;
        }
        
        .grid-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .sort-select {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            font-size: 0.9rem;
        }
        
        .view-toggle {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .view-btn {
            padding: 0.5rem 0.75rem;
            border: none;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .view-btn.active {
            background: #007bff;
            color: white;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .products-grid.list-view {
            grid-template-columns: 1fr;
        }
        
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        
        .no-products i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
        }
        
        .pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .page-btn {
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.2s ease;
        }
        
        .page-btn:hover {
            background: #f8f9fa;
            text-decoration: none;
            color: #333;
        }
        
        .page-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* RTL Support */
        .enhanced-product-grid-container.rtl {
            direction: rtl;
        }
        
        .enhanced-product-grid-container.rtl .grid-header {
            flex-direction: row-reverse;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .enhanced-product-grid-container {
                padding: 1rem;
            }
            
            .grid-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .grid-controls {
                justify-content: space-between;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
        </style>
        
        <script>
        function sortProducts(sortBy) {
            // Add sorting functionality
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            window.location.href = url.toString();
        }

        function buyProduct(productId) {
            window.location.href = `purchase.php?product_id=${productId}`;
        }

        function viewProductDetails(productId) {
            window.location.href = `product_details.php?id=${productId}`;
        }

        // View toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const grid = document.getElementById('productsGrid');
                    if (this.dataset.view === 'list') {
                        grid.classList.add('list-view');
                    } else {
                        grid.classList.remove('list-view');
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

// Initialize global enhanced product display
global $enhanced_product_display, $translation_system, $multilingual_interface;
if (isset($translation_system) && isset($multilingual_interface)) {
    $enhanced_product_display = new EnhancedProductDisplay($pdo, $translation_system, $multilingual_interface);
}
?>
