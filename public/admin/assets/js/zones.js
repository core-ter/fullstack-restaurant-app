let zones = [];

document.addEventListener('DOMContentLoaded', function () {
    loadZones();
});

async function loadZones() {
    const container = document.getElementById('zonesContainer');

    try {
        const response = await fetch('../api/admin/zones/list.php');
        const data = await response.json();

        if (data.success) {
            zones = data.zones;
            renderZones();
        } else {
            container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba</p></div>';
        }
    } catch (error) {
        console.error('Error loading zones:', error);
        container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba</p></div>';
    }
}

function renderZones() {
    const container = document.getElementById('zonesContainer');

    if (zones.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Nincs zóna definiálva</p></div>';
        return;
    }

    container.innerHTML = '<div class="table-responsive"><table class="admin-table"><thead><tr><th>Távolság</th><th>Kiszállítási díj</th><th>Szállítási idő</th><th>Műveletek</th></tr></thead><tbody>' +
        zones.map(function (zone) {
            return '<tr><td>' + zone.distance_from_km + ' - ' + zone.distance_to_km + ' km</td><td>' + formatPrice(zone.fee) + '</td><td>' + zone.delivery_time_minutes + ' perc</td><td><button onclick="openEditModal(' + zone.id + ')" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button> <button onclick="deleteZone(' + zone.id + ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td></tr>';
        }).join('') +
        '</tbody></table></div>';
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Új zóna';
    document.getElementById('zoneForm').reset();
    document.getElementById('zoneId').value = '';
    document.getElementById('zoneModal').classList.add('show');
}

function openEditModal(zoneId) {
    const zone = zones.find(function (z) { return z.id == zoneId; });
    if (!zone) return;

    document.getElementById('modalTitle').textContent = 'Zóna szerkesztése';
    document.getElementById('zoneId').value = zone.id;
    document.getElementById('distanceFrom').value = zone.distance_from_km;
    document.getElementById('distanceTo').value = zone.distance_to_km;
    document.getElementById('zoneFee').value = zone.fee;
    document.getElementById('deliveryTime').value = zone.delivery_time_minutes;
    document.getElementById('zoneModal').classList.add('show');
}

function closeModal() {
    document.getElementById('zoneModal').classList.remove('show');
}

document.getElementById('zoneForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = {
        distance_from_km: parseFloat(formData.get('distance_from_km')),
        distance_to_km: parseFloat(formData.get('distance_to_km')),
        fee: parseFloat(formData.get('fee')),
        delivery_time_minutes: parseInt(formData.get('delivery_time_minutes'))
    };

    const zoneId = formData.get('id');
    if (zoneId) data.id = parseInt(zoneId);

    const endpoint = zoneId ? 'update.php' : 'create.php';

    try {
        const response = await fetch('../api/admin/zones/' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            closeModal();
            loadZones();
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error saving zone:', error);
        showToast('Hiba történt!', 'error');
    }
});

async function deleteZone(zoneId) {
    if (!confirm('Biztosan törölni szeretnéd ezt a zónát?')) return;

    try {
        const response = await fetch('../api/admin/zones/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: zoneId })
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            loadZones();
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting zone:', error);
        showToast('Hiba történt!', 'error');
    }
}

window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.closeModal = closeModal;
window.deleteZone = deleteZone;
