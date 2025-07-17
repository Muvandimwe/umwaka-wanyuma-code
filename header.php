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
            <a href="index.php" class="logo">
                <i class="fas fa-store"></i> <?php echo $translations[$lang]['logo_text']; ?>
            </a>
            

            
            <!-- Navigation Links -->
            <nav class="nav-links">
                <!-- Remove this home link since logo already links to home -->
                <!-- <a href="index.php" class="nav-link"><?php echo $translations[$lang]['home']; ?></a> -->
                <a href="contact.php" class="nav-link"><?php echo $translations[$lang]['contact_us']; ?></a>

                <!-- Custom Language Menu -->
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
                        <a href="change_language.php?lang=en&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="lang-option <?php echo $lang == 'en' ? 'active' : ''; ?>">
                            <span class="flag">🇺🇸</span>
                            <span>English</span>
                        </a>
                        <a href="change_language.php?lang=fr&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="lang-option <?php echo $lang == 'fr' ? 'active' : ''; ?>">
                            <span class="flag">🇫🇷</span>
                            <span>French</span>
                        </a>
                        <a href="change_language.php?lang=rw&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="lang-option <?php echo $lang == 'rw' ? 'active' : ''; ?>">
                            <span class="flag">🇷🇼</span>
                            <span>Kinyarwanda</span>
                        </a>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="main-navigation">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i> <?php echo $translations[$lang]['home'] ?? 'Home'; ?>
                    </a>
                </nav>

                <!-- Authentication Buttons -->
                <div class="auth-buttons">
                    <?php if (is_logged_in()): ?>
                        <!-- User is logged in -->



                        <?php if ($_SESSION['user_type'] == 'seller'): ?>
                            <a href="seller_dashboard.php" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt"></i> <?php echo $translations[$lang]['seller_dashboard']; ?>
                            </a>
                        <?php else: ?>
                            <a href="buyer_dashboard.php" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt"></i> <?php echo $translations[$lang]['buyer_dashboard']; ?>
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> <?php echo $translations[$lang]['logout']; ?>
                        </a>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> <?php echo $translations[$lang]['register']; ?>
                        </a>
                        <a href="login.php" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt"></i> <?php echo $translations[$lang]['login']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</header>

<!-- Universal Back Navigation -->
<?php include_once 'includes/back_navigation.php'; ?>

<script>


// Language menu functionality
function toggleLanguageMenu() {
    const dropdown = document.getElementById('languageDropdown');
    const languageMenu = document.querySelector('.language-menu');

    // Toggle dropdown
    dropdown.classList.toggle('show');
    languageMenu.classList.toggle('active');

    // Close other dropdowns if any
    document.querySelectorAll('.language-dropdown').forEach(function(otherDropdown) {
        if (otherDropdown !== dropdown) {
            otherDropdown.classList.remove('show');
        }
    });
}

// Close language menu when clicking outside
document.addEventListener('click', function(event) {
    const languageMenu = document.querySelector('.language-menu');
    const dropdown = document.getElementById('languageDropdown');

    if (languageMenu && !languageMenu.contains(event.target)) {
        dropdown.classList.remove('show');
        languageMenu.classList.remove('active');
    }
});

// Close dropdown when pressing Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('languageDropdown');
        const languageMenu = document.querySelector('.language-menu');
        if (dropdown) {
            dropdown.classList.remove('show');
            languageMenu.classList.remove('active');
        }
    }
});


</script>


