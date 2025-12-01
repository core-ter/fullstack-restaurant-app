/**
 * Common JavaScript Utilities
 */

// API Base URL
const API_BASE_URL = '/api';

/**
 * Make API request
 * @param {string} endpoint API endpoint
 * @param {object} options Fetch options
 * @returns {Promise}
 */
async function apiRequest(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    const mergedOptions = { ...defaultOptions, ...options };

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, mergedOptions);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Something went wrong');
        }

        return data;
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}

/**
 * Show toast notification
 * @param {string} message Message to display
 * @param {string} type Type: success, error, warning, info
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.style.animation = 'slideInRight 0.3s ease-out';
    toast.textContent = message;

    document.body.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

/**
 * Initialize on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
});

// ============================================
// Dark Mode Functionality
// ============================================

/**
 * Initialize theme from localStorage or system preference
 */
function initTheme() {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
    } else {
        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }

    updateThemeIcon();
}

/**
 * Toggle between light and dark mode
 */
function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const newTheme = current === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon();
}

/**
 * Update theme toggle icon
 */
function updateThemeIcon() {
    const themeToggle = document.getElementById('theme-toggle');
    if (!themeToggle) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const icon = themeToggle.querySelector('i');

    if (icon) {
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    }
}

/**
 * Update cart badge count in navbar
 */
function updateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (!badge) return;

    // Get cart count from cart.js
    const count = typeof getCartCount === 'function' ? getCartCount() : 0;

    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

// Initialize badge on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
});

// CSS animations for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
