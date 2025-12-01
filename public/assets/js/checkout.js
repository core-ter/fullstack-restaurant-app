/**
 * Checkout Page JavaScript
 * Handles map integration, form validation, and order submission
 */

let map;
let marker;
let selectedLocation = { lat: 47.5316, lng: 21.6273 }; // Debrecen default

document.addEventListener('DOMContentLoaded', () => {
    checkCartEmpty();
    initMap();
    renderOrderSummary();
    setupAddressSearch();
    checkUserAndAutoFill(); // Auto-fill for logged-in users
});

function checkCartEmpty() {
    const cart = getCart();
    if (cart.length === 0) {
        window.location.href = '/menu.php';
    }
}

async function checkUserAndAutoFill() {
    try {
        const user = await checkAuth();
        if (user) {
            window.currentUser = user; // Store globally for discount calculation
            updateOrderSummary(); // Recalculate to show discount if applicable

            // Auto-fill form fields for logged-in users
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const phoneField = document.getElementById('phone');

            if (nameField && !nameField.value) {
                nameField.value = (user.first_name + ' ' + user.last_name).trim();
            }

            if (emailField && !emailField.value) {
                emailField.value = user.email || '';
            }

            if (phoneField && !phoneField.value && user.phone) {
                phoneField.value = user.phone;
            }

            // Hide personal details section if we have all data
            const personalSection = document.getElementById('personal-details-section');
            if (personalSection && user.first_name && user.last_name && user.email && user.phone) {
                personalSection.style.display = 'none';

                // Add a small info message
                const infoMsg = document.createElement('div');
                infoMsg.className = 'alert alert-info';
                infoMsg.innerHTML = `<i class="fas fa-user-check"></i> Bejelentkezve mint: <strong>${user.first_name} ${user.last_name}</strong>`;
                personalSection.parentNode.insertBefore(infoMsg, personalSection);
            }

            console.log('✅ Form auto-filled for user:', user.first_name);
        }
    } catch (error) {
        // Not logged in - no problem, user fills manually
        console.log('Guest checkout');
    }
}

function initMap() {
    map = L.map('map').setView([selectedLocation.lat, selectedLocation.lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    marker = L.marker([selectedLocation.lat, selectedLocation.lng], {
        draggable: true
    }).addTo(map);

    marker.on('dragend', function (e) {
        const position = e.target.getLatLng();
        selectedLocation = { lat: position.lat, lng: position.lng };
        reverseGeocode(position.lat, position.lng);
        calculateDeliveryFee(position.lat, position.lng);
    });

    map.on('click', function (e) {
        setDeliveryLocation(e.latlng.lat, e.latlng.lng);
    });

    map.whenReady(function () {
        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    });
}

function setDeliveryLocation(lat, lng) {
    selectedLocation = { lat, lng };
    marker.setLatLng([lat, lng]);
    map.panTo([lat, lng]);
    reverseGeocode(lat, lng);
    calculateDeliveryFee(lat, lng);
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
        );
        const data = await response.json();

        if (data && data.display_name) {
            document.getElementById('addressSearch').value = data.display_name;
        }
    } catch (error) {
        console.error('Reverse geocode error:', error);
    }
}

function setupAddressSearch() {
    const searchInput = document.getElementById('addressSearch');
    const resultsContainer = document.getElementById('addressResults');
    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length < 3) {
            resultsContainer.classList.remove('show');
            return;
        }

        searchTimeout = setTimeout(() => searchAddress(query), 500);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper')) {
            resultsContainer.classList.remove('show');
        }
    });
}

async function searchAddress(query) {
    const resultsContainer = document.getElementById('addressResults');

    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)},Debrecen,Hungary&format=json&limit=5`
        );
        const results = await response.json();

        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="autocomplete-no-results">Nincs találat</div>';
            resultsContainer.classList.add('show');
            return;
        }

        resultsContainer.innerHTML = results.map(result => `
            <div class="autocomplete-item" onclick="selectAddress(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
                <div class="autocomplete-item-name">${result.name || result.display_name.split(',')[0]}</div>
                <div class="autocomplete-item-address">${result.display_name}</div>
            </div>
        `).join('');

        resultsContainer.classList.add('show');

    } catch (error) {
        console.error('Address search error:', error);
        resultsContainer.innerHTML = '<div class="autocomplete-no-results">Hiba történt</div>';
        resultsContainer.classList.add('show');
    }
}

function selectAddress(lat, lng, displayName) {
    const resultsContainer = document.getElementById('addressResults');

    const latNum = parseFloat(lat);
    const lngNum = parseFloat(lng);

    selectedLocation = { lat: latNum, lng: lngNum };
    marker.setLatLng([latNum, lngNum]);

    map.setView([latNum, lngNum], 16, {
        animate: true,
        duration: 0.5
    });

    document.getElementById('addressSearch').value = displayName;
    resultsContainer.classList.remove('show');

    reverseGeocode(latNum, lngNum);
    calculateDeliveryFee(latNum, lngNum);
}

async function calculateDeliveryFee(lat, lng) {
    try {
        const response = await fetch(`/api/delivery/calculate-fee.php?lat=${lat}&lng=${lng}`);
        const data = await response.json();

        if (data.success) {
            window.deliveryFeeData = {
                fee: data.fee,
                distance: data.distance_km,
                deliveryTime: data.delivery_time
            };

            updateOrderSummary();
        }
    } catch (error) {
        console.error('Delivery fee calculation error:', error);
    }
}

function updateOrderSummary() {
    const subtotal = getCartSubtotal();
    const FREE_DELIVERY_THRESHOLD = 5000;

    let delivery = window.deliveryFeeData ? window.deliveryFeeData.fee : 0;

    // Free delivery for orders >= 5000 Ft
    if (subtotal >= FREE_DELIVERY_THRESHOLD) {
        delivery = 0;
    }

    // First Order Discount Calculation
    let discount = 0;
    const user = window.currentUser; // Assuming checkAuth sets this globally or we fetch it

    // We need to check if user is eligible for discount
    // This logic relies on the user object having is_first_order property
    // We might need to ensure checkAuth returns this
    if (user && user.is_first_order) {
        discount = Math.round(subtotal * 0.1);
    }

    const total = subtotal + delivery - discount;

    document.getElementById('subtotal').textContent = formatPrice(subtotal);

    // Display Discount Row if applicable
    const summaryTotals = document.querySelector('.summary-totals');
    let discountRow = document.getElementById('discountRow');

    if (discount > 0) {
        if (!discountRow) {
            discountRow = document.createElement('div');
            discountRow.id = 'discountRow';
            discountRow.className = 'summary-row discount';
            discountRow.style.color = 'var(--success)';
            discountRow.innerHTML = `
                <span>Első rendelés kedvezmény (-10%):</span>
                <span id="discountAmount">-${formatPrice(discount)}</span>
            `;
            // Insert before delivery row
            const deliveryRow = document.querySelector('.summary-row:nth-child(2)'); // Assuming delivery is 2nd
            summaryTotals.insertBefore(discountRow, deliveryRow);
        } else {
            document.getElementById('discountAmount').textContent = `-${formatPrice(discount)}`;
        }
    } else if (discountRow) {
        discountRow.remove();
    }

    if (delivery === 0) {
        if (subtotal >= FREE_DELIVERY_THRESHOLD) {
            document.getElementById('delivery').textContent = 'Ingyenes (5000 Ft felett)';
        } else {
            document.getElementById('delivery').textContent = 'Ingyenes';
        }
    } else {
        const distanceText = window.deliveryFeeData ? ` (${window.deliveryFeeData.distance} km)` : '';
        document.getElementById('delivery').textContent = formatPrice(delivery) + distanceText;
    }

    document.getElementById('total').textContent = formatPrice(total);
}

function renderOrderSummary() {
    const cart = getCart();
    const orderItemsContainer = document.getElementById('orderItems');

    orderItemsContainer.innerHTML = cart.map(item => `
        <div class="order-item">
            <div class="order-item-info">
                <div class="order-item-name">${item.name}</div>
                <div class="order-item-quantity">${item.quantity} x ${formatPrice(item.price)}</div>
            </div>
            <div class="order-item-price">${formatPrice(item.price * item.quantity)}</div>
        </div>
    `).join('');

    updateOrderSummary();
}

function validateForm() {
    const form = document.getElementById('checkoutForm');
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('addressSearch').value.trim();

    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

    let isValid = true;

    if (!name || name.length < 3) {
        document.getElementById('name').classList.add('error');
        isValid = false;
    }

    if (!phone || phone.length < 9) {
        document.getElementById('phone').classList.add('error');
        isValid = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        document.getElementById('email').classList.add('error');
        isValid = false;
    }

    if (!address || address.length < 10) {
        document.getElementById('addressSearch').classList.add('error');
        isValid = false;
    }

    if (!isValid) {
        showNotification('Kérlek töltsd ki az összes kötelező mezőt!', 'error');
    }

    return isValid;
}

async function submitOrder() {
    if (!validateForm()) return;

    const loadingOverlay = document.getElementById('loadingOverlay');
    const cart = getCart();
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;

    const subtotal = getCartSubtotal();
    const FREE_DELIVERY_THRESHOLD = 5000;

    let deliveryFee = window.deliveryFeeData ? window.deliveryFeeData.fee : 0;

    // Apply free delivery for orders >= 5000 Ft
    if (subtotal >= FREE_DELIVERY_THRESHOLD) {
        deliveryFee = 0;
    }

    const total = subtotal + deliveryFee;

    // Show loading overlay
    loadingOverlay.classList.add('show');

    try {
        // Generate reCAPTCHA token
        const recaptchaToken = await new Promise((resolve) => {
            if (typeof grecaptcha === 'undefined' || !window.RECAPTCHA_SITE_KEY) {
                resolve('');
                return;
            }

            grecaptcha.ready(() => {
                grecaptcha.execute(window.RECAPTCHA_SITE_KEY, { action: 'checkout' })
                    .then(token => resolve(token))
                    .catch(err => {
                        console.error('reCAPTCHA error:', err);
                        resolve('');
                    });
            });
        });

        const orderData = {
            customer_name: document.getElementById('name').value.trim(),
            customer_phone: document.getElementById('phone').value.trim(),
            customer_email: document.getElementById('email').value.trim(),
            delivery_address: document.getElementById('addressSearch').value.trim(),
            delivery_lat: selectedLocation.lat,
            delivery_lng: selectedLocation.lng,
            notes: document.getElementById('notes').value.trim(),
            payment_method: paymentMethod,
            recaptcha_token: recaptchaToken,
            items: cart.map(item => ({
                menu_item_id: item.id,
                name: item.name,
                quantity: item.quantity,
                price: item.price
            })),
            subtotal: subtotal,
            delivery_fee: deliveryFee,
            total: total
        };

        console.log('📦 Sending order data:', orderData);

        const response = await fetch('/api/orders/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();
        console.log('📥 Server response:', result);

        if (result.success) {
            localStorage.removeItem('restaurant_cart');
            updateCartBadge();

            showNotification('Rendelés sikeresen leadva! #' + result.order_number, 'success');

            // Redirect to order tracking page
            setTimeout(() => {
                window.location.href = '/track-order.php?number=' + encodeURIComponent(result.order_number);
            }, 2000);
        } else {
            loadingOverlay.classList.remove('show');
            showNotification('Hiba: ' + result.message, 'error');
        }
    } catch (error) {
        loadingOverlay.classList.remove('show');
        console.error('Order submission error:', error);
        showNotification('Hiba történt a rendelés leadása során!', 'error');
    }
}
