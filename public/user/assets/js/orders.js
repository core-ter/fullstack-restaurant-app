// Helper function
function formatPrice(price) {
    return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF',
        minimumFractionDigits: 0
    }).format(price);
}

let allOrders = [];

document.addEventListener('DOMContentLoaded', function () {
    loadOrders();
});

async function loadOrders() {
    const container = document.getElementById('ordersContainer');

    try {
        const response = await fetch('/api/user/orders.php');
        const data = await response.json();

        if (data.success) {
            allOrders = data.orders;
            renderOrders();
        } else {
            container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>' + data.message + '</p></div>';
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba történt</p></div>';
    }
}

function renderOrders() {
    const container = document.getElementById('ordersContainer');

    if (allOrders.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Még nincs rendelésed</p><a href="/menu.php" class="btn btn-primary" style="margin-top: 1rem;">Rendelés leadása</a></div>';
        return;
    }

    container.innerHTML = '<div class="orders-list">' + allOrders.map(function (order) {
        const statusBadge = '<span class="status-badge" style="background-color: ' + order.status_color + '">' + order.status_name + '</span>';
        const date = new Date(order.created_at).toLocaleDateString('hu-HU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return '<div class="order-card" onclick="showOrderDetails(' + order.id + ')">' +
            '<div class="order-card-header">' +
            '<div>' +
            '<strong>#' + order.order_number + '</strong>' +
            '<span class="text-muted"> • ' + date + '</span>' +
            '</div>' +
            statusBadge +
            '</div>' +
            '<div class="order-card-body">' +
            '<div class="order-address"><i class="fas fa-map-marker-alt"></i> ' + order.delivery_address + '</div>' +
            '<div class="order-items-count"><i class="fas fa-shopping-bag"></i> ' + order.item_count + ' tétel</div>' +
            '<div class="order-total"><strong>' + formatPrice(order.total_amount) + '</strong></div>' +
            '</div>' +
            '</div>';
    }).join('') + '</div>';
}

function showOrderDetails(orderId) {
    const order = allOrders.find(function (o) { return o.id === orderId; });
    if (!order) return;

    document.getElementById('modalOrderNumber').textContent = '#' + order.order_number + ' - Rendelés részletei';

    const itemsHtml = order.items.map(function (item) {
        return '<div class="order-detail-item">' +
            '<div class="order-detail-item-info">' +
            '<strong>' + item.item_name + '</strong>' +
            '<span class="text-muted">' + item.quantity + ' db × ' + formatPrice(item.price) + '</span>' +
            '</div>' +
            '<div class="order-detail-item-price">' + formatPrice(item.quantity * item.price) + '</div>' +
            '</div>';
    }).join('');

    const detailsHtml = '<div class="order-details">' +
        '<div class="order-detail-section">' +
        '<h4>Termékek</h4>' +
        itemsHtml +
        '</div>' +
        '<div class="order-detail-section">' +
        '<h4>Szállítási adatok</h4>' +
        '<p><i class="fas fa-map-marker-alt"></i> ' + order.delivery_address + '</p>' +
        '<p><i class="fas fa-phone"></i> ' + order.phone + '</p>' +
        (order.special_instructions ? '<p><i class="fas fa-comment"></i> ' + order.special_instructions + '</p>' : '') +
        '</div>' +
        '<div class="order-detail-section">' +
        '<h4>Fizetési mód</h4>' +
        '<p>' + (order.payment_method === 'cash' ? 'Készpénz' : 'Kártya') + '</p>' +
        '</div>' +
        '<div class="order-detail-total">' +
        '<div class="order-detail-row">' +
        '<span>Termékek összesen:</span>' +
        '<span>' + formatPrice(order.total_amount - order.delivery_fee) + '</span>' +
        '</div>' +
        '<div class="order-detail-row">' +
        '<span>Szállítási díj:</span>' +
        '<span>' + formatPrice(order.delivery_fee) + '</span>' +
        '</div>' +
        '<div class="order-detail-row order-detail-total-row">' +
        '<strong>Végösszeg:</strong>' +
        '<strong>' + formatPrice(order.total_amount) + '</strong>' +
        '</div>' +
        '</div>' +
        '</div>';

    document.getElementById('orderDetailsContainer').innerHTML = detailsHtml;
    document.getElementById('orderModal').classList.add('show');
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('show');
}

window.showOrderDetails = showOrderDetails;
window.closeOrderModal = closeOrderModal;
