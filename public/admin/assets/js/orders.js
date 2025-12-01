let currentFilter = 'all';
let searchQuery = '';
let refreshInterval;

document.addEventListener('DOMContentLoaded', () => {
    loadOrders();
    refreshInterval = setInterval(() => { loadOrders(); }, 30000);
});

function filterByStatus(status) {
    currentFilter = status;
    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`[data-status="${status}"]`).classList.add('active');
    loadOrders();
}

function searchOrders() {
    searchQuery = document.getElementById('searchInput').value.trim();
    loadOrders();
}

async function loadOrders() {
    const container = document.getElementById('ordersContainer');
    let url = '../api/admin/orders/list.php?limit=50';
    if (currentFilter !== 'all') url += `&status=${currentFilter}`;
    if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

    try {
        const response = await fetch(url);
        const data = await response.json();
        container.innerHTML = (data.success && data.orders.length > 0)
            ? data.orders.map(order => renderOrderCard(order)).join('')
            : '<div class="empty-state"><i class="fas fa-inbox"></i><p>Nincs találat</p></div>';
    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba</p></div>';
    }
}

function renderOrderCard(order) {
    const statusColors = {
        'pending': 'warning',
        'confirmed': 'info',
        'preparing': 'primary',
        'delivering': 'primary',
        'completed': 'success',
        'cancelled': 'danger'
    };
    const statusColor = statusColors[order.status_code] || 'secondary';
    let statusActions = '';

    switch (order.status_code) {
        case 'pending':
            statusActions = `
                <button type="button" class="btn btn-success btn-sm" onclick="window.updateOrderStatus(${order.id}, 'confirmed')">
                    <i class="fas fa-check"></i> Elfogadás
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="window.updateOrderStatus(${order.id}, 'cancelled')">
                    <i class="fas fa-times"></i> Elutasítás
                </button>
            `;
            break;
        case 'confirmed':
            statusActions = `<button type="button" class="btn btn-primary btn-sm" onclick="window.updateOrderStatus(${order.id}, 'preparing')"><i class="fas fa-fire"></i> Kész</button>`;
            break;
        case 'preparing':
            statusActions = `<button type="button" class="btn btn-success btn-sm" onclick="window.updateOrderStatus(${order.id}, 'delivering')"><i class="fas fa-truck"></i> Kiszállítás</button>`;
            break;
        case 'delivering':
            statusActions = `<button type="button" class="btn btn-success btn-sm" onclick="window.updateOrderStatus(${order.id}, 'completed')"><i class="fas fa-check-circle"></i> Teljesítve</button>`;
            break;
        case 'completed':
            statusActions = '<div class="alert alert-success" style="margin: 0;"><i class="fas fa-check-circle"></i> Teljesítve</div>';
            break;
        case 'cancelled':
            statusActions = '<div class="alert alert-danger" style="margin: 0;"><i class="fas fa-ban"></i> Törölve</div>';
            break;
    }

    return `
        <div class="card order-detail-card">
            <div class="card-header">
                <div><strong>#${order.order_number}</strong><span class="text-muted"> • ${formatDate(order.created_at)}</span></div>
                <span class="badge badge-${statusColor}">${order.status_name}</span>
            </div>
            <div class="card-body">
                <div class="order-info-grid">
                    <div class="order-info-section">
                        <h4><i class="fas fa-user"></i> Vevő</h4>
                        <p><strong>${order.guest_first_name} ${order.guest_last_name || ''}</strong></p>
                        <p><i class="fas fa-phone"></i> ${order.guest_phone}</p>
                        <p><i class="fas fa-envelope"></i> ${order.guest_email}</p>
                    </div>
                    <div class="order-info-section">
                        <h4><i class="fas fa-map-marker-alt"></i> Szállítási cím</h4>
                        <p>${order.delivery_address}</p>
                    </div>
                    <div class="order-info-section">
                        <h4><i class="fas fa-shopping-bag"></i> Termékek</h4>
                        ${order.items.map(item => {
        const qty = item.quantity || 1;
        const price = parseFloat(item.unit_price) || 0;
        return `<div class="order-item-row"><span>${qty}x ${item.item_name || 'Ismeretlen'}</span><span>${formatPrice(qty * price)}</span></div>`;
    }).join('')}
                    </div>
                    <div class="order-info-section">
                        <h4><i class="fas fa-coins"></i> Összesítő</h4>
                        <div class="order-item-row"><span>Részösszeg:</span><span>${formatPrice(order.subtotal || 0)}</span></div>
                        <div class="order-item-row"><span>Szállítás:</span><span>${order.delivery_fee > 0 ? formatPrice(order.delivery_fee) : 'Ingyenes'}</span></div>
                        <div class="order-item-row" style="font-weight: bold; font-size: 1.1rem; color: var(--primary);"><span>Végösszeg:</span><span>${formatPrice(order.total_amount || 0)}</span></div>
                        <div style="margin-top: var(--space-2); padding-top: var(--space-2); border-top: 1px solid var(--border-color);">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);"><i class="fas fa-credit-card"></i> ${order.payment_method === 'card' ? 'Bankkártya' : 'Készpénz'}</span>
                        </div>
                    </div>
                </div>
                ${order.customer_notes ? `<div class="order-notes"><i class="fas fa-sticky-note"></i><strong>Megjegyzés:</strong> ${order.customer_notes}</div>` : ''}
                <div class="order-actions">${statusActions}</div>
            </div>
        </div>
    `;
}

async function updateOrderStatus(orderId, newStatus) {
    if (!newStatus) return;

    try {
        const response = await fetch('../api/admin/orders/update-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status_code: newStatus })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Státusz sikeresen frissítve!', 'success');
            loadOrders();
        } else {
            showToast(result.message || 'Hiba történt!', 'error');
        }
    } catch (error) {
        console.error('Status update error:', error);
        showToast('Hiba történt!', 'error');
    }
}

window.updateOrderStatus = updateOrderStatus;
