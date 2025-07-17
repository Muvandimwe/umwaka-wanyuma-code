<?php
// Ensure language variables are available
if (!isset($lang)) {
    $lang = $_SESSION['language'] ?? 'en';
}
?>

<header class="header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo-section">
                <a href="index.php" class="logo">
                    <i class="fas fa-store"></i>
                    <?php echo $translations[$lang]['logo_text']; ?>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="nav-links">
                <!-- Language Menu -->
                <div class="language-menu">
                    <button class="language-toggle" onclick="toggleLanguageMenu()">
                        <i class="fas fa-globe"></i>
                        <span class="current-lang">
                            <?php 
                            switch($lang) {
                                case 'rw': echo 'RW'; break;
                                case 'fr': echo 'FR'; break;
                                default: echo 'EN'; break;
                            }
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="language-dropdown" id="languageDropdown">
                        <a href="?lang=en" class="lang-option <?php echo $lang == 'en' ? 'active' : ''; ?>">
                            <span class="flag">🇺🇸</span>
                            <span>English</span>
                        </a>
                        <a href="?lang=fr" class="lang-option <?php echo $lang == 'fr' ? 'active' : ''; ?>">
                            <span class="flag">🇫🇷</span>
                            <span>Français</span>
                        </a>
                        <a href="?lang=rw" class="lang-option <?php echo $lang == 'rw' ? 'active' : ''; ?>">
                            <span class="flag">🇷🇼</span>
                            <span>Kinyarwanda</span>
                        </a>
                    </div>
                </div>

                <a href="contact.php" class="nav-link">
                    <i class="fas fa-phone"></i>
                    <?php echo $translations[$lang]['contact_us']; ?>
                </a>
                


                <!-- Authentication Links -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_type'] == 'admin'): ?>
                        <a href="admin_dashboard.php" class="nav-link">
                            <i class="fas fa-shield-alt"></i>
                            Admin Panel
                        </a>
                    <?php elseif ($_SESSION['user_type'] == 'seller'): ?>
                        <a href="seller_dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="buyer_dashboard.php" class="nav-link">
                            <i class="fas fa-user"></i>
                            My Account
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <?php echo $translations[$lang]['logout']; ?>
                    </a>
                <?php else: ?>
                    <a href="register.php" class="nav-link register-link">
                        <i class="fas fa-user-plus"></i>
                        <?php echo $translations[$lang]['register']; ?>
                    </a>
                    <a href="login.php" class="nav-link login-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <?php echo $translations[$lang]['login']; ?>
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            <div class="mobile-nav-links">
                <!-- Language Menu Mobile -->
                <div class="mobile-language-menu">
                    <button class="mobile-language-toggle" onclick="toggleMobileLanguageMenu()">
                        <i class="fas fa-globe"></i>
                        <span>
                            <?php 
                            switch($lang) {
                                case 'rw': echo 'Kinyarwanda'; break;
                                case 'fr': echo 'Français'; break;
                                default: echo 'English'; break;
                            }
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="mobile-language-dropdown" id="mobileLanguageDropdown">
                        <a href="?lang=en">🇺🇸 English</a>
                        <a href="?lang=fr">🇫🇷 Français</a>
                        <a href="?lang=rw">🇷🇼 Kinyarwanda</a>
                    </div>
                </div>

                <a href="contact.php" class="mobile-nav-link">
                    <i class="fas fa-phone"></i>
                    <?php echo $translations[$lang]['contact_us']; ?>
                </a>
                


                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_type'] == 'admin'): ?>
                        <a href="admin_dashboard.php" class="mobile-nav-link">
                            <i class="fas fa-shield-alt"></i>
                            Admin Panel
                        </a>
                    <?php elseif ($_SESSION['user_type'] == 'seller'): ?>
                        <a href="seller_dashboard.php" class="mobile-nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="buyer_dashboard.php" class="mobile-nav-link">
                            <i class="fas fa-user"></i>
                            My Account
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="mobile-nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <?php echo $translations[$lang]['logout']; ?>
                    </a>
                <?php else: ?>
                    <a href="register.php" class="mobile-nav-link">
                        <i class="fas fa-user-plus"></i>
                        <?php echo $translations[$lang]['register']; ?>
                    </a>
                    <a href="login.php" class="mobile-nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <?php echo $translations[$lang]['login']; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>

// Language menu functionality
function toggleLanguageMenu() {
    const dropdown = document.getElementById('languageDropdown');
    const languageMenu = document.querySelector('.language-menu');
    
    dropdown.classList.toggle('show');
    languageMenu.classList.toggle('active');
}

// Mobile menu functionality
function toggleMobileMenu() {
    const mobileNav = document.getElementById('mobileNav');
    mobileNav.classList.toggle('show');
}

function toggleMobileLanguageMenu() {
    const dropdown = document.getElementById('mobileLanguageDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const languageMenu = document.querySelector('.language-menu');
    const dropdown = document.getElementById('languageDropdown');
    const mobileNav = document.getElementById('mobileNav');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    
    // Close language dropdown
    if (languageMenu && !languageMenu.contains(event.target)) {
        dropdown.classList.remove('show');
        languageMenu.classList.remove('active');
    }
    
    // Close mobile menu
    if (mobileNav && !mobileNav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
        mobileNav.classList.remove('show');
    }
});


</script>
