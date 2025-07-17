<?php
/**
 * Universal Back Navigation Component
 * Provides consistent back navigation across all pages
 */

// Get current page and determine appropriate back destination
$current_page = basename($_SERVER['PHP_SELF']);
$back_url = 'index.php'; // Default back destination
$back_text = $translations[$lang]['back'] ?? 'Back';

// Determine smart back navigation based on current page
switch ($current_page) {
    case 'login.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'register.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'buyer_dashboard.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'seller_dashboard.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'admin_dashboard.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'product_details.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_products'] ?? 'Back to Products';
        break;
        
    case 'purchase.php':
        $back_url = isset($_GET['product_id']) ? 'product_details.php?id=' . $_GET['product_id'] : 'index.php';
        $back_text = $translations[$lang]['back_to_product'] ?? 'Back to Product';
        break;
        
    case 'contact.php':
        $back_url = 'index.php';
        $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        break;
        
    case 'profile.php':
    case 'buyer_profile.php':
    case 'seller_profile.php':
        if (isset($_SESSION['user_type'])) {
            $back_url = $_SESSION['user_type'] === 'buyer' ? 'buyer_dashboard.php' : 'seller_dashboard.php';
            $back_text = $translations[$lang]['back_to_dashboard'] ?? 'Back to Dashboard';
        } else {
            $back_url = 'index.php';
            $back_text = $translations[$lang]['back_to_home'] ?? 'Back to Home';
        }
        break;
        
    case 'add_product.php':
    case 'edit_product.php':
        $back_url = 'seller_dashboard.php';
        $back_text = $translations[$lang]['back_to_dashboard'] ?? 'Back to Dashboard';
        break;
        
    case 'admin_users.php':
    case 'admin_products.php':
    case 'admin_orders.php':
    case 'admin_reports.php':
        $back_url = 'admin_dashboard.php';
        $back_text = $translations[$lang]['back_to_admin'] ?? 'Back to Admin';
        break;
        
    default:
        // For any other page, check if user is logged in
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'buyer') {
                $back_url = 'buyer_dashboard.php';
                $back_text = $translations[$lang]['back_to_dashboard'] ?? 'Back to Dashboard';
            } elseif ($_SESSION['user_type'] === 'seller') {
                $back_url = 'seller_dashboard.php';
                $back_text = $translations[$lang]['back_to_dashboard'] ?? 'Back to Dashboard';
            } elseif ($_SESSION['user_type'] === 'admin') {
                $back_url = 'admin_dashboard.php';
                $back_text = $translations[$lang]['back_to_admin'] ?? 'Back to Admin';
            }
        }
        break;
}

// Check if we should show back button (don't show on index.php)
$show_back_button = ($current_page !== 'index.php');
?>

<?php if ($show_back_button): ?>
<div class="universal-back-navigation">
    <a href="<?php echo htmlspecialchars($back_url); ?>" class="back-nav-btn" title="<?php echo htmlspecialchars($back_text); ?>">
        <i class="fas fa-arrow-left"></i>
        <span class="back-text"><?php echo htmlspecialchars($back_text); ?></span>
    </a>
</div>

<style>
.universal-back-navigation {
    position: fixed;
    top: 80px;
    left: 20px;
    z-index: 1000;
    transition: all 0.3s ease;
}

.back-nav-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: rgba(35, 47, 62, 0.95);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    min-width: 120px;
    justify-content: center;
}

.back-nav-btn:hover {
    background: rgba(35, 47, 62, 1);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    text-decoration: none;
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
}

.back-nav-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.back-nav-btn i {
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.back-nav-btn:hover i {
    transform: translateX(-3px);
}

.back-text {
    white-space: nowrap;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .universal-back-navigation {
        top: 70px;
        left: 15px;
    }
    
    .back-nav-btn {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        min-width: 100px;
    }
    
    .back-text {
        display: none;
    }
    
    .back-nav-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        justify-content: center;
        min-width: auto;
    }
}

/* Tablet responsive */
@media (max-width: 1024px) and (min-width: 769px) {
    .universal-back-navigation {
        top: 75px;
        left: 18px;
    }
    
    .back-nav-btn {
        padding: 0.7rem 1.1rem;
        font-size: 0.85rem;
    }
}

/* Hide on very small screens to avoid overlap */
@media (max-width: 480px) {
    .universal-back-navigation {
        top: 65px;
        left: 10px;
    }
    
    .back-nav-btn {
        width: 40px;
        height: 40px;
        padding: 0.5rem;
    }
}

/* Adjust for pages with different header heights */
.page-with-tall-header .universal-back-navigation {
    top: 100px;
}

/* Print styles - hide back button when printing */
@media print {
    .universal-back-navigation {
        display: none !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .back-nav-btn {
        background: #000;
        border-color: #fff;
        color: #fff;
    }
    
    .back-nav-btn:hover {
        background: #333;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .back-nav-btn,
    .back-nav-btn i {
        transition: none;
    }
    
    .back-nav-btn:hover {
        transform: none;
    }
    
    .back-nav-btn:hover i {
        transform: none;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .back-nav-btn {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }
    
    .back-nav-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
    }
}
</style>

<script>
// Enhanced back navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    const backBtn = document.querySelector('.back-nav-btn');
    
    if (backBtn) {
        // Add keyboard support
        backBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.location.href = this.href;
            }
        });
        
        // Add smooth navigation with loading state
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading state
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="back-text">Going back...</span>';
            this.style.pointerEvents = 'none';
            
            // Navigate after short delay for visual feedback
            setTimeout(() => {
                window.location.href = this.href;
            }, 300);
        });
        
        // Show/hide based on scroll position (optional enhancement)
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const backNav = document.querySelector('.universal-back-navigation');
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down - hide
                backNav.style.transform = 'translateX(-100%)';
                backNav.style.opacity = '0.7';
            } else {
                // Scrolling up - show
                backNav.style.transform = 'translateX(0)';
                backNav.style.opacity = '1';
            }
            
            lastScrollTop = scrollTop;
        });
    }
});

// Browser back button integration
window.addEventListener('popstate', function(event) {
    // Handle browser back button if needed
    console.log('Browser back button pressed');
});
</script>
<?php endif; ?>
