// Main JavaScript file for Local Commerce System

document.addEventListener('DOMContentLoaded', function() {
    // Load products on homepage
    if (document.getElementById('products-grid')) {
        loadProducts();
    }
    
    // Initialize form validation
    initializeFormValidation();
});

// Load products for homepage
function loadProducts() {
    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;
    
    // Show loading state
    productsGrid.innerHTML = '<div class="loading">Loading products...</div>';
    
    fetch('api/get_products.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
            } else {
                productsGrid.innerHTML = '<div class="no-products">No products found.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            productsGrid.innerHTML = '<div class="error">Error loading products.</div>';
        });
}

// Display products in grid
function displayProducts(products) {
    const productsGrid = document.getElementById('products-grid');
    
    if (products.length === 0) {
        productsGrid.innerHTML = '<div class="no-products">No products found.</div>';
        return;
    }
    
    let html = '';
    products.forEach(product => {
        html += `
            <div class="product-card">
                <img src="${product.image ? 'uploads/products/' + product.image : 'assets/images/no-image.png'}" 
                     alt="${product.name}" class="product-image">
                <div class="product-info">
                    <h3 class="product-name">${product.name}</h3>
                    <p class="product-price">${Math.round(parseFloat(product.price))} FRW</p>
                    <button class="buy-now-btn" onclick="buyNow(${product.id})">
                        <i class="fas fa-shopping-cart"></i> Buy Now
                    </button>
                </div>
            </div>
        `;
    });
    
    productsGrid.innerHTML = html;
}

// Handle buy now button click
function buyNow(productId) {
    // Check if user is logged in
    fetch('api/check_login.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                if (data.user_type === 'buyer') {
                    // Redirect to purchase page
                    window.location.href = `purchase.php?product_id=${productId}`;
                } else {
                    alert('Only buyers can purchase products. Please register as a buyer.');
                }
            } else {
                // Redirect to buyer registration
                window.location.href = `register.php?type=buyer&product_id=${productId}`;
            }
        })
        .catch(error => {
            console.error('Error checking login status:', error);
            // Fallback to registration page
            window.location.href = `register.php?type=buyer&product_id=${productId}`;
        });
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Check password confirmation
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    
    if (password && confirmPassword) {
        if (password.value !== confirmPassword.value) {
            showFieldError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
    }
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    
    // Clear previous errors
    clearFieldError(field);
    
    // Required field validation
    if (field.hasAttribute('required') && value === '') {
        showFieldError(field, 'This field is required');
        isValid = false;
    }
    
    // Email validation
    if (field.type === 'email' && value !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Password validation
    if (field.type === 'password' && value !== '' && value.length < 6) {
        showFieldError(field, 'Password must be at least 6 characters long');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('error');
}

function clearFieldError(field) {
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
    field.classList.remove('error');
}

// Image preview functionality
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Utility functions
function showMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${type}`;
    messageDiv.textContent = message;
    
    // Insert at top of main content
    const main = document.querySelector('.main-content') || document.body;
    main.insertBefore(messageDiv, main.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

function formatPrice(price) {
    return Math.round(parseFloat(price)) + ' FRW';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Search functionality
function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.trim();
    
    if (searchTerm === '') {
        loadProducts(); // Load all products
        return;
    }
    
    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '<div class="loading">Searching...</div>';
    
    fetch(`api/search_products.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
            } else {
                productsGrid.innerHTML = '<div class="no-products">No products found matching your search.</div>';
            }
        })
        .catch(error => {
            console.error('Error searching products:', error);
            productsGrid.innerHTML = '<div class="error">Error searching products.</div>';
        });
}

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('mobile-open');
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Add loading states to buttons
function addLoadingState(button, originalText = null) {
    if (!originalText) {
        originalText = button.textContent;
    }
    button.setAttribute('data-original-text', originalText);
    button.textContent = 'Loading...';
    button.disabled = true;
}

function removeLoadingState(button) {
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.textContent = originalText;
        button.removeAttribute('data-original-text');
    }
    button.disabled = false;
}
