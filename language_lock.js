/**
 * Language Lock System - Prevents automatic language changes
 * Ensures selected language stays persistent on client side
 */

// Language persistence functions
const LanguageLock = {
    // Set language in local storage
    setLanguage: function(lang) {
        if (['en', 'fr', 'rw'].includes(lang)) {
            localStorage.setItem('preferred_language', lang);
            localStorage.setItem('language_locked', 'true');
            localStorage.setItem('language_timestamp', Date.now().toString());
            
            // Also set in session storage as backup
            sessionStorage.setItem('preferred_language', lang);
            sessionStorage.setItem('language_locked', 'true');
            
            console.log('Language locked to:', lang);
        }
    },
    
    // Get language from local storage
    getLanguage: function() {
        const lang = localStorage.getItem('preferred_language');
        const locked = localStorage.getItem('language_locked');
        
        if (locked === 'true' && ['en', 'fr', 'rw'].includes(lang)) {
            return lang;
        }
        
        // Fallback to session storage
        const sessionLang = sessionStorage.getItem('preferred_language');
        if (['en', 'fr', 'rw'].includes(sessionLang)) {
            return sessionLang;
        }
        
        return 'en'; // Default
    },
    
    // Check if language is locked
    isLocked: function() {
        return localStorage.getItem('language_locked') === 'true' || 
               sessionStorage.getItem('language_locked') === 'true';
    },
    
    // Prevent any automatic language changes
    preventAutoChange: function() {
        // Disable any automatic language detection
        if (navigator.language || navigator.languages) {
            // Override browser language detection
            Object.defineProperty(navigator, 'language', {
                get: function() { return LanguageLock.getLanguage(); },
                configurable: false
            });
        }
        
        // Prevent any form auto-submission that might change language
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.querySelector('input[name="lang"]') || form.querySelector('select[name="lang"]')) {
                // This is a language form, allow it
                return true;
            }
        });
        
        // Monitor for any URL changes that might affect language
        let currentLang = LanguageLock.getLanguage();
        setInterval(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const urlLang = urlParams.get('lang');
            
            if (urlLang && ['en', 'fr', 'rw'].includes(urlLang) && urlLang !== currentLang) {
                // Language changed via URL, update our storage
                LanguageLock.setLanguage(urlLang);
                currentLang = urlLang;
            }
        }, 1000);
    },
    
    // Initialize the language lock system
    init: function() {
        // Set language from PHP session if available
        const phpLang = document.documentElement.getAttribute('lang');
        if (phpLang && ['en', 'fr', 'rw'].includes(phpLang)) {
            this.setLanguage(phpLang);
        }
        
        // Prevent automatic changes
        this.preventAutoChange();
        
        // Add event listeners to language links
        document.addEventListener('DOMContentLoaded', function() {
            const langLinks = document.querySelectorAll('a[href*="change_language.php"]');
            langLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    const url = new URL(link.href);
                    const lang = url.searchParams.get('lang');
                    if (lang && ['en', 'fr', 'rw'].includes(lang)) {
                        LanguageLock.setLanguage(lang);
                        console.log('Language change initiated:', lang);
                    }
                });
            });
        });
        
        console.log('Language Lock System initialized');
        console.log('Current language:', this.getLanguage());
        console.log('Language locked:', this.isLocked());
    }
};

// Auto-initialize when script loads
LanguageLock.init();

// Expose globally for debugging
window.LanguageLock = LanguageLock;
