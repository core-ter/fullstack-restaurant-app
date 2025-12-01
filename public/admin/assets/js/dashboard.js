let currentPeriod = 'today';
let refreshInterval = null;

document.addEventListener('DOMContentLoaded', function () {
    loadDashboardData();
    loadRecentOrders();
    startAutoRefresh();
});

function changePeriod(period) {
    currentPeriod = period;
    const allTabs = document.querySelectorAll('.filter-tab');
    const theme = document.documentElement.getAttribute('data-theme');

    allTabs.forEach(function (tab) {
        tab.classList.remove('active');
        if (theme === 'light') {
            tab.style.backgroundColor = '#ffffff';
            tab.style.color = '#1f2937';
            tab.style.borderColor = '#d1d5db';
        }
    });

    const activeTab = document.querySelector('[data-period="' + period + '"]');
    if (activeTab) {
        activeTab.classList.add('active');
        if (theme === 'light') {
            activeTab.style.backgroundColor = '#e63946';
            activeTab.style.color = '#ffffff';
            activeTab.style.borderColor = '#e63946';
        }
    }

    loadDashboardData();
}

async function loadDashboardData() {
    try {
        const response = await fetch('../api/admin/stats.php?period=' + currentPeriod);
        const data = await response.json();

        if (data.success) {
            updateStats(data.stats);
            updatePopularItems(data.popular_items);
            updateStatusBreakdown(data.status_breakdown);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateStats(stats) {
    document.getElementById('stat-total-orders').textContent = stats.total_orders;
    document.getElementById('stat-revenue').textContent = formatPrice(stats.total_revenue);
    document.getElementById('stat-avg-order').textContent = formatPrice(stats.avg_order_value);
    document.getElementById('stat-active-orders').textContent = stats.active_orders;
}

function updatePopularItems(items) {
    const container = document.getElementById('popular-items-container');

    if (!items || items.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Nincs adat</p></div>';
        return;
    }

    container.innerHTML = '<div class="popular-items-list">' + items.map(function (item, index) {
        return '<div class="popular-item"><div class="popular-item-rank">' + (index + 1) + '</div><div class="popular-item-details"><div class="popular-item-name">' + item.name + '</div><div class="popular-item-stats">' + item.total_quantity + ' db • ' + item.order_count + ' rendelés</div></div></div>';
    }).join('') + '</div>';
}

function updateStatusBreakdown(breakdown) {
    const container = document.getElementById('status-breakdown-container');

    if (!breakdown || breakdown.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Nincs adat</p></div>';
        return;
    }

    const total = breakdown.reduce(function (sum, item) { return sum + parseInt(item.count); }, 0);

    container.innerHTML = '<div class="status-breakdown-list">' + breakdown.map(function (item) {
        const percentage = total > 0 ? Math.round((item.count / total) * 100) : 0;
        return '<div class="status-breakdown-item"><div class="status-breakdown-info"><span class="status-badge" style="background-color: ' + item.color_hex + '">' + item.display_name + '</span><span class="status-count">' + item.count + ' db</span></div><div class="status-breakdown-bar"><div class="status-breakdown-fill" style="width: ' + percentage + '%; background-color: ' + item.color_hex + '"></div></div></div>';
    }).join('') + '</div>';
}

async function loadRecentOrders() {
    const container = document.getElementById('recent-orders-container');

    try {
        const response = await fetch('../api/admin/orders/list.php?limit=5');
        const data = await response.json();

        if (data.success && data.orders.length > 0) {
            container.innerHTML = data.orders.map(function (order) {
                return '<div class="order-card"><div class="order-card-header"><div><strong>#' + order.order_number + '</strong><span class="text-muted"> • ' + formatDate(order.created_at) + '</span></div>' + getStatusBadge(order.status_code, order.status_name) + '</div><div class="order-card-body"><div class="order-customer"><i class="fas fa-user"></i> ' + order.guest_first_name + ' ' + (order.guest_last_name || '') + '</div><div class="order-items-count"><i class="fas fa-shopping-bag"></i> ' + order.items.length + ' tétel</div><div class="order-total"><strong>' + formatPrice(order.total_amount) + '</strong></div></div></div>';
            }).join('');
        } else {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Még nincsenek rendelések</p></div>';
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba történt</p></div>';
    }
}

function startAutoRefresh() {
    refreshInterval = setInterval(function () {
        loadDashboardData();
        loadRecentOrders();
    }, 30000);
}

window.changePeriod = changePeriod;
