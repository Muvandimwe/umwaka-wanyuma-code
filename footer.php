<footer class="fixed-footer">
    <div class="footer-container">
        <div class="footer-content">


            <!-- Copyright Section -->
            <div class="footer-copyright">
                <p>&copy; 2024 <?php echo $translations[$lang]['logo_text']; ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<style>
/* Fixed Footer Styles */
.fixed-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    background: linear-gradient(135deg, #232f3e 0%, #1a252f 100%);
    color: white;
    z-index: 999;
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    border-top: 2px solid rgba(255, 255, 255, 0.1);
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.footer-content {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0;
    text-align: center;
}

.footer-copyright {
    width: 100%;
}

.footer-copyright p {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.4;
    font-weight: 500;
}

/* Language selector removed from footer */

/* Ensure body has bottom padding to prevent content overlap */
body {
    padding-bottom: 50px !important; /* Reduced for smaller footer height */
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .footer-content {
        padding: 0.4rem 0;
    }

    .footer-copyright p {
        font-size: 0.8rem;
    }

    body {
        padding-bottom: 45px !important; /* Reduced for smaller footer */
    }
}

/* Tablet Responsive */
@media (max-width: 1024px) and (min-width: 769px) {
    .footer-content {
        padding: 0.45rem 0;
    }

    .footer-copyright p {
        font-size: 0.85rem;
    }
}

/* Very small screens */
@media (max-width: 480px) {
    .footer-content {
        padding: 0.7rem 0;
    }

    .footer-copyright p {
        font-size: 0.75rem;
    }

    body {
        padding-bottom: 55px !important;
    }
}

/* Print styles - hide footer when printing */
@media print {
    .fixed-footer {
        display: none !important;
    }

    body {
        padding-bottom: 0 !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .fixed-footer {
        background: #000;
        border-top-color: #fff;
    }

    .footer-link {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .footer-link {
        transition: none;
    }

    .footer-link:hover {
        transform: none;
    }
}

/* Navigation Styles */
.main-navigation {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0 2rem;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}



@media (max-width: 768px) {
    .main-navigation {
        margin: 0 1rem;
        gap: 0.5rem;
    }

    .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }


}

/* Language Success Notification */
.language-success {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    z-index: 10000;
    animation: slideIn 0.5s ease-out;
    font-weight: bold;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.language-success.fade-out {
    animation: fadeOut 0.5s ease-out forwards;
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>

<!-- Language Lock System -->
<script src="includes/language_lock.js"></script>

<script>
// Language success notification
document.addEventListener('DOMContentLoaded', function() {
    // Check if language was just changed
    const urlParams = new URLSearchParams(window.location.search);
    const langSuccess = urlParams.get('lang_success');

    if (langSuccess && ['en', 'fr', 'rw'].includes(langSuccess)) {
        const langNames = {
            'en': '🇺🇸 English',
            'fr': '🇫🇷 French',
            'rw': '🇷🇼 Kinyarwanda'
        };

        // Show success notification
        const notification = document.createElement('div');
        notification.className = 'language-success';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Language changed to ${langNames[langSuccess]}
        `;

        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(function() {
            notification.classList.add('fade-out');
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 3000);

        // Clean URL by removing success parameter
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]lang_success=[^&]*/, '').replace(/[?&]t=[^&]*/, '');
        window.history.replaceState({}, document.title, newUrl);
    }

    // Force language persistence
    const currentLang = document.documentElement.getAttribute('lang');
    if (currentLang && ['en', 'fr', 'rw'].includes(currentLang)) {
        localStorage.setItem('preferred_language', currentLang);
        localStorage.setItem('language_locked', 'true');
        sessionStorage.setItem('preferred_language', currentLang);
    }
});
</script>

<script>
// Footer enhancement functionality
document.addEventListener('DOMContentLoaded', function() {
    // Ensure footer is always visible
    const footer = document.querySelector('.fixed-footer');
    if (footer) {
        // Add smooth appearance animation
        footer.style.opacity = '0';
        footer.style.transform = 'translateY(100%)';

        setTimeout(() => {
            footer.style.transition = 'all 0.5s ease';
            footer.style.opacity = '1';
            footer.style.transform = 'translateY(0)';
        }, 100);

        // Adjust body padding dynamically based on footer height
        const adjustBodyPadding = () => {
            const footerHeight = footer.offsetHeight;
            document.body.style.paddingBottom = (footerHeight + 10) + 'px';
        };

        // Adjust on load and resize
        adjustBodyPadding();
        window.addEventListener('resize', adjustBodyPadding);

        // Footer is now simplified with only copyright and language selector
    }
});
</script>
