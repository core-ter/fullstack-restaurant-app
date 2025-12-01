// Helper function
function formatPrice(price) {
    return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF',
        minimumFractionDigits: 0
    }).format(price);
}

let isEditMode = false;

document.addEventListener('DOMContentLoaded', async function () {
    await loadUserData();
    await loadStats();
});

async function loadUserData() {
    try {
        const user = await checkAuth();
        if (user) {
            // Set header name
            const fullName = (user.first_name + ' ' + user.last_name).trim();
            document.getElementById('headerUserName').textContent = fullName || 'Felhasználó';

            // Fill form fields
            document.getElementById('first_name').value = user.first_name || '';
            document.getElementById('last_name').value = user.last_name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('phone').value = user.phone || '';
        }
    } catch (error) {
        console.error('Error loading user data:', error);
        window.location.href = '/login.php';
    }
}

async function loadStats() {
    const container = document.getElementById('statsContainer');

    try {
        const response = await fetch('/api/user/stats.php');
        const data = await response.json();

        if (data.success) {
            const stats = data.stats;

            container.innerHTML = `
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-value">${stats.total_orders}</div>
                        <div class="stat-label">
                            <i class="fas fa-shopping-cart"></i>
                            Összes rendelés
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-value">${formatPrice(stats.total_spent)}</div>
                        <div class="stat-label">
                            <i class="fas fa-wallet"></i>
                            Elköltött összeg
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-value">${stats.total_orders > 0 ? formatPrice(stats.average_order) : '0 Ft'}</div>
                        <div class="stat-label">
                            <i class="fas fa-chart-line"></i>
                            Átlagos rendelés
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        container.innerHTML = '<div class="empty-state"><p>Statisztikák betöltése sikertelen</p></div>';
    }
}

function toggleEditMode(skipReload = false) {
    isEditMode = !isEditMode;

    const editableFields = ['first_name', 'last_name', 'phone'];
    editableFields.forEach(fieldId => {
        document.getElementById(fieldId).disabled = !isEditMode;
    });

    document.getElementById('saveBtn').style.display = isEditMode ? 'block' : 'none';
    document.getElementById('editBtn').innerHTML = isEditMode
        ? '<i class="fas fa-times"></i> Mégse'
        : '<i class="fas fa-edit"></i> Szerkesztés';

    if (!isEditMode && !skipReload) {
        // Reload data if cancelled (but not if saved)
        loadUserData();
    }
}

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = {
        first_name: document.getElementById('first_name').value.trim(),
        last_name: document.getElementById('last_name').value.trim(),
        phone: document.getElementById('phone').value.trim()
    };

    try {
        const response = await fetch('/api/user/update-profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            toggleEditMode(true); // Skip reload to keep new values

            // Update header name immediately
            const fullName = (formData.first_name + ' ' + formData.last_name).trim();
            document.getElementById('headerUserName').textContent = fullName;
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error updating profile:', error);
        showToast('Hiba történt a profil frissítése során', 'error');
    }
});

// Password form submission
document.getElementById('passwordForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // Validation
    if (newPassword !== confirmPassword) {
        showToast('Az új jelszavak nem egyeznek', 'error');
        return;
    }

    if (newPassword.length < 6) {
        showToast('A jelszó legalább 6 karakter hosszú legyen', 'error');
        return;
    }

    try {
        const response = await fetch('/api/user/change-password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            this.reset(); // Clear form
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error changing password:', error);
        showToast('Hiba történt a jelszó változtatása során', 'error');
    }
});

// Make toggleEditMode globally available
window.toggleEditMode = toggleEditMode;
