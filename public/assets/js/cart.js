/**
 * Shopping Cart Management
 * Uses localStorage for persistent cart storage
 */

const CART_STORAGE_KEY = 'restaurant_cart';

// Get cart from localStorage
function getCart() {
    const cartData = localStorage.getItem(CART_STORAGE_KEY);
    return cartData ? JSON.parse(cartData) : [];
}

// Save cart to localStorage
function saveCart(cart) {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    updateCartBadge();
}

// Add item to cart
function addToCart(item) {
    const cart = getCart();
    const existingItem = cart.find(cartItem => cartItem.id === item.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: item.id,
            name: item.name,
            price: parseFloat(item.price),
            quantity: 1,
            image_url: item.image_url || '/assets/images/placeholder-food.svg'
        });
    }

    saveCart(cart);
    showNotification('Termék hozzáadva a kosárhoz!', 'success');
}

// Remove item from cart
function removeFromCart(itemId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== itemId);
    saveCart(cart);
    if (document.getElementById('cartItems')) {
        renderCartPage();
    }
}

// Update item quantity - WITH DEBUG LOGS
function updateQuantity(itemId, quantity) {
    console.log('🔧 updateQuantity:', itemId, quantity);
    const cart = getCart();
    const item = cart.find(cartItem => cartItem.id === itemId);

    if (item) {
        if (quantity <= 0) {
            console.log('❌ Removing item');
            removeFromCart(itemId);
        } else {
            console.log('✏️ Updating from', item.quantity, 'to', quantity);
            item.quantity = quantity;
            saveCart(cart);
            if (document.getElementById('cartItems')) {
                console.log('🔄 Re-rendering NOW');
                renderCartPage();
            }
        }
    }
}

// Get total number of items in cart
function getCartCount() {
    const cart = getCart();
    return cart.reduce((total, item) => total + item.quantity, 0);
}

// Get cart subtotal
function getCartSubtotal() {
    const cart = getCart();
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Get delivery fee
function getDeliveryFee() {
    const subtotal = getCartSubtotal();
    return subtotal >= 5000 ? 0 : 500;
}

// Get cart total
function getCartTotal() {
    return getCartSubtotal() + getDeliveryFee();
}

// Format price to HUF
function formatPrice(price) {
    return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF',
        maximumFractionDigits: 0
    }).format(price);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Render cart page
function renderCartPage() {
    const cart = getCart();
    const cartItemsContainer = document.getElementById('cartItems');
    const cartSummaryContainer = document.getElementById('cartSummary');
    const emptyCartContainer = document.getElementById('emptyCart');

    if (!cartItemsContainer) return;

    if (cart.length === 0) {
        cartItemsContainer.style.display = 'none';
        cartSummaryContainer.style.display = 'none';
        emptyCartContainer.style.display = 'block';
    } else {
        cartItemsContainer.style.display = 'block';
        cartSummaryContainer.style.display = 'block';
        emptyCartContainer.style.display = 'none';

        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item">
                <img src="${item.image_url}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h3 class="cart-item-name">${item.name}</h3>
                    <p class="cart-item-price">${formatPrice(item.price)}</p>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="quantity-value">${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="cart-item-total">
                    ${formatPrice(item.price * item.quantity)}
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');

        const subtotal = getCartSubtotal();
        const deliveryFee = getDeliveryFee();
        const total = getCartTotal();

        cartSummaryContainer.innerHTML = `
            <div class="cart-summary">
                <h2>Összesítés</h2>
                <div class="summary-row">
                    <span>Részösszeg:</span>
                    <span>${formatPrice(subtotal)}</span>
                </div>
                <div class="summary-row">
                    <span>Szállítási díj:</span>
                    <span>${deliveryFee === 0 ? 'Ingyenes' : formatPrice(deliveryFee)}</span>
                </div>
                ${subtotal < 5000 ? `
                    <div class="summary-info">
                        <i class="fas fa-info-circle"></i>
                        Ingyenes szállítás 5000 Ft felett
                    </div>
                ` : ''}
                <div class="summary-row summary-total">
                    <span>Összesen:</span>
                    <span>${formatPrice(total)}</span>
                </div>
                <a href="/checkout.php" class="btn btn-primary btn-checkout">
                    <i class="fas fa-credit-card"></i>
                    Tovább a pénztárhoz
                </a>
            </div>
        `;
    }
}

// Initialize cart page
if (document.getElementById('cartItems')) {
    renderCartPage();
}
