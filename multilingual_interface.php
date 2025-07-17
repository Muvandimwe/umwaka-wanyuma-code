<?php
/**
 * Enhanced Multilingual Interface Component
 * Provides comprehensive UI elements for language switching and content adaptation
 */

include_once 'enhanced_translation.php';

class MultilingualInterface {
    private $translation_system;
    private $current_language;
    
    public function __construct($translation_system) {
        $this->translation_system = $translation_system;
        $this->current_language = $translation_system->getCurrentLanguage();
    }
    
    /**
     * Generate enhanced language selector with flags and names
     */
    public function generateLanguageSelector($show_flags = true, $show_names = true) {
        $languages = $this->translation_system->getSupportedLanguages();
        $current_lang = $this->current_language;
        
        ob_start();
        ?>
        <div class="enhanced-language-selector">
            <button class="language-toggle-enhanced" onclick="toggleEnhancedLanguageMenu()" aria-label="Select Language">
                <?php if ($show_flags): ?>
                    <span class="flag"><?php echo $languages[$current_lang]['flag']; ?></span>
                <?php endif; ?>
                <?php if ($show_names): ?>
                    <span class="language-name"><?php echo $languages[$current_lang]['name']; ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-down"></i>
            </button>
            
            <div class="language-dropdown-enhanced" id="enhancedLanguageDropdown">
                <div class="language-dropdown-header">
                    <i class="fas fa-globe"></i>
                    <span><?php echo $this->translate('select_language', 'Select Language'); ?></span>
                </div>
                
                <?php foreach ($languages as $code => $lang): ?>
                    <a href="?lang=<?php echo $code; ?>" 
                       class="language-option-enhanced <?php echo $code === $current_lang ? 'active' : ''; ?>"
                       data-language="<?php echo $code; ?>">
                        <span class="flag"><?php echo $lang['flag']; ?></span>
                        <span class="language-name"><?php echo $lang['name']; ?></span>
                        <?php if ($code === $current_lang): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
                
                <div class="language-dropdown-footer">
                    <small><?php echo $this->translate('auto_translate_note', 'Content will be automatically translated'); ?></small>
                </div>
            </div>
        </div>
        
        <style>
        .enhanced-language-selector {
            position: relative;
            display: inline-block;
        }
        
        .language-toggle-enhanced {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .language-toggle-enhanced:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .language-dropdown-enhanced {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 250px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .language-dropdown-enhanced.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .language-dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .language-option-enhanced {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f8f8f8;
        }
        
        .language-option-enhanced:hover {
            background: #f8f9fa;
            color: #007bff;
            text-decoration: none;
        }
        
        .language-option-enhanced.active {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
        }
        
        .language-option-enhanced .flag {
            font-size: 1.2rem;
        }
        
        .language-option-enhanced .language-name {
            flex: 1;
        }
        
        .language-dropdown-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #f0f0f0;
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
        }
        
        .language-dropdown-footer small {
            color: #666;
            font-size: 0.8rem;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .language-dropdown-enhanced {
                right: -50px;
                min-width: 200px;
            }
            
            .language-toggle-enhanced {
                min-width: 100px;
                padding: 0.5rem 0.75rem;
            }
        }
        </style>
        
        <script>
        function toggleEnhancedLanguageMenu() {
            const dropdown = document.getElementById('enhancedLanguageDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const selector = document.querySelector('.enhanced-language-selector');
            const dropdown = document.getElementById('enhancedLanguageDropdown');
            
            if (selector && !selector.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Handle language selection with smooth transition
        document.querySelectorAll('.language-option-enhanced').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const language = this.dataset.language;
                
                // Show loading state
                document.body.style.opacity = '0.7';
                document.body.style.pointerEvents = 'none';
                
                // Add loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.innerHTML = `
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                                background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
                                z-index: 10000; text-align: center;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #007bff; margin-bottom: 1rem;"></i>
                        <div>Translating content...</div>
                        <div style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">Please wait while we adapt the interface</div>
                    </div>
                `;
                document.body.appendChild(loadingDiv);
                
                // Navigate to new language
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Generate content adaptation notice
     */
    public function generateAdaptationNotice() {
        $current_lang_name = $this->translation_system->getSupportedLanguages()[$this->current_language]['name'];
        
        if ($this->current_language === 'en') {
            return ''; // No notice needed for English
        }
        
        ob_start();
        ?>
        <div class="content-adaptation-notice">
            <div class="notice-content">
                <i class="fas fa-language"></i>
                <span><?php echo $this->translate('content_adapted_notice', "Content has been automatically adapted to {$current_lang_name}"); ?></span>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="notice-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <style>
        .content-adaptation-notice {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 0.75rem 0;
            text-align: center;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .notice-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .notice-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.25rem;
            margin-left: 1rem;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        
        .notice-close:hover {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .notice-content {
                flex-direction: column;
                gap: 0.25rem;
                padding: 0 1rem;
            }
            
            .notice-close {
                position: absolute;
                top: 0.5rem;
                right: 1rem;
                margin: 0;
            }
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Generate language-specific formatting for dates
     */
    public function formatDate($date, $format = null) {
        return $this->translation_system->formatDate($date, $this->current_language);
    }
    
    /**
     * Generate language-specific formatting for currency
     */
    public function formatCurrency($amount) {
        return $this->translation_system->formatCurrency($amount, $this->current_language);
    }
    
    /**
     * Translate text with fallback
     */
    public function translate($key, $fallback = '') {
        global $translations;
        
        if (isset($translations[$this->current_language][$key])) {
            return $translations[$this->current_language][$key];
        }
        
        if (isset($translations['en'][$key])) {
            return $translations['en'][$key];
        }
        
        return $fallback ?: ucfirst(str_replace('_', ' ', $key));
    }
    
    /**
     * Generate RTL support for Arabic and other RTL languages
     */
    public function getRTLSupport() {
        $rtl_languages = ['ar', 'ur'];
        
        if (in_array($this->current_language, $rtl_languages)) {
            return '<style>
                body { direction: rtl; text-align: right; }
                .container { direction: rtl; }
                .nav-links { flex-direction: row-reverse; }
                .product-grid { direction: rtl; }
                .form-group { text-align: right; }
                .btn { margin-left: 0; margin-right: 0.5rem; }
            </style>';
        }
        
        return '';
    }
    
    /**
     * Generate language-specific number formatting
     */
    public function formatNumber($number, $decimals = 0) {
        switch ($this->current_language) {
            case 'fr':
                return number_format($number, $decimals, ',', ' ');
            case 'ar':
            case 'ur':
                return number_format($number, $decimals, '٫', '٬');
            default:
                return number_format($number, $decimals);
        }
    }
    
    /**
     * Get language-specific CSS classes
     */
    public function getLanguageClasses() {
        $classes = ['lang-' . $this->current_language];
        
        // Add RTL class for right-to-left languages
        if (in_array($this->current_language, ['ar', 'ur'])) {
            $classes[] = 'rtl';
        }
        
        // Add font class for languages that need special fonts
        if (in_array($this->current_language, ['ar', 'ur', 'zh', 'ja', 'ko', 'hi'])) {
            $classes[] = 'special-font';
        }
        
        return implode(' ', $classes);
    }
}

// Initialize global multilingual interface
global $multilingual_interface, $translation_system;
if (isset($translation_system)) {
    $multilingual_interface = new MultilingualInterface($translation_system);
}
?>
