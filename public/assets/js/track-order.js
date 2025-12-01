// Helper functions
function formatPrice(price) {
    return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF',
        minimumFractionDigits: 0
    }).format(price);
}

function formatDateTime(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('hu-HU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Get order number from URL
const urlParams = new URLSearchParams(window.location.search);
const orderNumber = urlParams.get('number');

let refreshInterval;

document.addEventListener('DOMContentLoaded', function () {
    if (!orderNumber) {
        showError('Hiányzó rendelésszám');
        return;
    }

    loadOrderTracking();
    startAutoRefresh();
});

async function loadOrderTracking() {
    const container = document.getElementById('trackingContainer');

    try {
        const response = await fetch('/api/orders/track.php?number=' + encodeURIComponent(orderNumber));
        const data = await response.json();

        if (data.success) {
            renderOrderTracking(data.order);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Tracking error:', error);
        showError('Hiba történt a rendelés lekérdezése során');
    }
}

function renderOrderTracking(order) {
    const container = document.getElementById('trackingContainer');

    const statusSteps = [
        { code: 'pending', name: 'Fogadva', icon: 'fa-clock' },
        { code: 'confirmed', name: 'Megerősítve', icon: 'fa-check-circle' },
        { code: 'preparing', name: 'Készítés', icon: 'fa-utensils' },
        { code: 'out_for_delivery', name: 'Kiszállítás', icon: 'fa-shipping-fast' },
        { code: 'delivered', name: 'Kiszállítva', icon: 'fa-check-double' }
    ];

    const currentStatusIndex = statusSteps.findIndex(s => s.code === order.status_code);

    const statusHtml = statusSteps.map((step, index) => {
        const isActive = index === currentStatusIndex;
        const isCompleted = index < currentStatusIndex;
        const stateClass = isCompleted ? 'completed' : (isActive ? 'active' : 'pending');

        return '<div class="status-step ' + stateClass + '">' +
            '<div class="status-circle"><i class="fas ' + step.icon + '"></i></div>' +
            '<div class="status-label">' + step.name + '</div>' +
            '</div>';
    }).join('<div class="status-line"></div>');

    const itemsHtml = order.items.map(item =>
        '<div class="order-item">' +
        '<div class="order-item-info">' +
        '<strong>' + item.item_name + '</strong>' +
        '<span class="text-muted">' + item.quantity + ' db × ' + formatPrice(item.price) + '</span>' +
        '</div>' +
        '<div class="order-item-price">' + formatPrice(item.quantity * item.price) + '</div>' +
        '</div>'
    ).join('');

    container.innerHTML =
        '<div class="tracking-card">' +
        '<div class="tracking-header">' +
        '<h1><i class="fas fa-receipt"></i> Rendelés #' + order.order_number + '</h1>' +
        '<div class="tracking-date">' + formatDateTime(order.created_at) + '</div>' +
        '</div>' +

        '<div class="tracking-status">' +
        '<div class="current-status">' +
        '<span class="status-badge" style="background-color: ' + order.status_color + '">' + order.status_name + '</span>' +
        '</div>' +
        '<div class="estimated-time">' +
        '<i class="fas fa-clock"></i> Becsült kiszállítás: <strong>kb. ' + order.delivery_time_minutes + ' perc</strong>' +
        '</div>' +
        '</div>' +

        '<div class="status-timeline">' + statusHtml + '</div>' +

        '<div class="order-details-section">' +
        '<h3><i class="fas fa-shopping-bag"></i> Rendelt tételek</h3>' +
        '<div class="order-items-list">' + itemsHtml + '</div>' +
        '</div>' +

        '<div class="delivery-info-section">' +
        '<h3><i class="fas fa-map-marker-alt"></i> Szállítási információk</h3>' +
        '<p><strong>Cím:</strong> ' + order.delivery_address + '</p>' +
        '<p><strong>Telefon:</strong> ' + order.phone + '</p>' +
        (order.special_instructions ? '<p><strong>Megjegyzés:</strong> ' + order.special_instructions + '</p>' : '') +
        '</div>' +

        '<div class="order-summary">' +
        '<div class="summary-row">' +
        '<span>Termékek összesen:</span>' +
        '<span>' + formatPrice(order.total_amount - order.delivery_fee) + '</span>' +
        '</div>' +
        '<div class="summary-row">' +
        '<span>Szállítási díj:</span>' +
        '<span>' + formatPrice(order.delivery_fee) + '</span>' +
        '</div>' +
        '<div class="summary-row summary-total">' +
        '<strong>Végösszeg:</strong>' +
        '<strong>' + formatPrice(order.total_amount) + '</strong>' +
        '</div>' +
        '</div>' +

        '<div class="tracking-footer">' +
        '<a href="/menu.php" class="btn btn-primary">Vissza az étlaphoz</a>' +
        '</div>' +
        '</div>';
}

function showError(message) {
    const container = document.getElementById('trackingContainer');
    container.innerHTML =
        '<div class="error-state">' +
        '<i class="fas fa-exclamation-triangle"></i>' +
        '<h2>Hiba</h2>' +
        '<p>' + message + '</p>' +
        '<a href="/menu.php" class="btn btn-primary">Vissza az étlaphoz</a>' +
        '</div>';
}

function startAutoRefresh() {
    // Refresh every 30 seconds
    refreshInterval = setInterval(loadOrderTracking, 30000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function () {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
