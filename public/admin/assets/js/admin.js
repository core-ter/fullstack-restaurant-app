/**
 * Admin Dashboard Common Functions
 */

/**
 * Logout function
 */
async function logout() {
    if (!confirm('Biztosan ki szeretnél jelentkezni?')) {
        return;
    }

    try {
        const response = await fetch('../api/admin/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'logout'
            })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Hiba történt a kijelentkezés során!', 'error');
    }
}

/**
 * Format price helper
 */
function formatPrice(amount) {
    return new Intl.NumberFormat('hu-HU', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount) + ' Ft';
}

/**
 * Format date helper
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('hu-HU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Get status badge HTML
 */
function getStatusBadge(statusCode, displayName) {
    const statusColors = {
        'pending': 'warning',
        'confirmed': 'info',
        'preparing': 'primary',
        'ready': 'success',
        'delivered': 'success',
        'cancelled': 'danger'
    };

    const color = statusColors[statusCode] || 'secondary';

    return `<span class="badge badge-${color}">${displayName}</span>`;
}
