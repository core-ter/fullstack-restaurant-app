let currentCategory = 'all';
let categories = [];
let menuItems = [];
let uploadedImageUrl = null;

document.addEventListener('DOMContentLoaded', function() {
    initImageUpload();
    loadMenuItems();
});

function filterByCategory(categoryId) {
    currentCategory = categoryId;
    document.querySelectorAll('.filter-tab').forEach(function(tab) { tab.classList.remove('active'); });
    document.querySelector('[data-category="' + categoryId + '"]').classList.add('active');
    renderMenuItems();
}

async function loadMenuItems() {
    const container = document.getElementById('menuItemsContainer');
    try {
        const response = await fetch('../api/admin/menu/list.php');
        const data = await response.json();
        if (data.success) {
            menuItems = data.items;
            categories = data.categories;
            renderCategoryFilters();
            renderMenuItems();
        } else {
            container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba</p></div>';
        }
    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba</p></div>';
    }
}

function renderCategoryFilters() {
    const filtersContainer = document.getElementById('categoryFilters');
    const allButton = filtersContainer.querySelector('[data-category="all"]');
    const addButton = filtersContainer.querySelector('.filter-tab-add');
    filtersContainer.innerHTML = '';
    filtersContainer.appendChild(allButton);
    categories.forEach(function(cat) {
        const button = document.createElement('button');
        button.className = 'filter-tab';
        button.setAttribute('data-category', cat.id);
        button.onclick = function() { filterByCategory(cat.id); };
        button.innerHTML = cat.name + ' <span class="category-actions" onclick="event.stopPropagation()"><button class="category-action-btn" onclick="openCategoryModal(' + cat.id + ')" title="Szerkesztés"><i class="fas fa-edit"></i></button><button class="category-action-btn" onclick="deleteCategory(' + cat.id + ')" title="Törlés"><i class="fas fa-trash"></i></button></span>';
        filtersContainer.appendChild(button);
    });
    filtersContainer.appendChild(addButton);
    const categorySelect = document.getElementById('itemCategory');
    categorySelect.innerHTML = categories.map(function(cat) { return '<option value="' + cat.id + '">' + cat.name + '</option>'; }).join('');
}

function renderMenuItems() {
    const container = document.getElementById('menuItemsContainer');
    const filtered = currentCategory === 'all' ? menuItems : menuItems.filter(function(item) { return item.category_id == currentCategory; });
    if (filtered.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>Nincs termék</p></div>';
        return;
    }
    container.innerHTML = '<div class="menu-items-grid">' + filtered.map(renderMenuItem).join('') + '</div>';
}

function renderMenuItem(item) {
    const availableClass = item.is_available == 1 ? 'available' : 'unavailable';
    const availableIcon = item.is_available == 1 ? 'fa-check-circle' : 'fa-times-circle';
    const availableText = item.is_available == 1 ? 'Elérhető' : 'Nem elérhető';
    const imageHtml = item.image_url ? '<img src="' + item.image_url + '" alt="' + item.name + '" class="menu-item-image">' : '';
    return '<div class="menu-item-card ' + availableClass + '">' + imageHtml + '<div class="menu-item-header"><div><h3>' + item.name + '</h3><span class="menu-item-category">' + item.category_name + '</span></div><div class="menu-item-price">' + formatPrice(item.price) + '</div></div>' + (item.description ? '<p class="menu-item-description">' + item.description + '</p>' : '') + (item.ingredients ? '<div class="menu-item-meta"><strong>Összetevők:</strong> ' + item.ingredients + '</div>' : '') + (item.allergens ? '<div class="menu-item-meta allergens"><i class="fas fa-exclamation-triangle"></i> <strong>Allergének:</strong> ' + item.allergens + '</div>' : '') + '<div class="menu-item-actions"><button type="button" onclick="toggleAvailability(' + item.id + ')" class="btn btn-sm ' + (item.is_available == 1 ? 'btn-warning' : 'btn-success') + '"><i class="fas ' + availableIcon + '"></i> ' + availableText + '</button><button type="button" onclick="openEditModal(' + item.id + ')" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Szerkesztés</button><button type="button" onclick="deleteMenuItem(' + item.id + ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Törlés</button></div></div>';
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Új termék';
    document.getElementById('menuForm').reset();
    document.getElementById('itemId').value = '';
    document.getElementById('itemAvailable').checked = true;
    document.getElementById('menuModal').classList.add('show');
    removeImage();
}

function openEditModal(itemId) {
    const item = menuItems.find(function(i) { return i.id == itemId; });
    if (!item) return;
    document.getElementById('modalTitle').textContent = 'Termék szerkesztése';
    document.getElementById('itemId').value = item.id;
    document.getElementById('itemName').value = item.name;
    document.getElementById('itemCategory').value = item.category_id;
    document.getElementById('itemPrice').value = item.price;
    document.getElementById('itemDescription').value = item.description || '';
    document.getElementById('itemIngredients').value = item.ingredients || '';
    document.getElementById('itemAllergens').value = item.allergens || '';
    document.getElementById('itemAvailable').checked = item.is_available == 1;
    document.getElementById('menuModal').classList.add('show');
    if (item.image_url) {
        document.getElementById('itemImageUrl').value = item.image_url;
        document.getElementById('previewImage').src = item.image_url;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('imagePreview').style.display = 'block';
        uploadedImageUrl = item.image_url;
    } else {
        removeImage();
    }
}

function closeModal() {
    document.getElementById('menuModal').classList.remove('show');
}

document.getElementById('menuForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        name: formData.get('name'),
        category_id: formData.get('category_id'),
        price: formData.get('price'),
        description: formData.get('description'),
        ingredients: formData.get('ingredients'),
        allergens: formData.get('allergens'),
        is_available: formData.get('is_available') ? 1 : 0,
        image_url: document.getElementById('itemImageUrl').value || null
    };
    const itemId = formData.get('id');
    if (itemId) data.id = itemId;
    const endpoint = itemId ? 'update.php' : 'create.php';
    try {
        const response = await fetch('../api/admin/menu/' + endpoint, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        const result = await response.json();
        if (result.success) { showToast(result.message, 'success'); closeModal(); loadMenuItems(); } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba történt!', 'error'); }
});

async function toggleAvailability(itemId) {
    try {
        const response = await fetch('../api/admin/menu/toggle-available.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: itemId}) });
        const result = await response.json();
        if (result.success) { showToast(result.message, 'success'); loadMenuItems(); } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba történt!', 'error'); }
}

async function deleteMenuItem(itemId) {
    if (!confirm('Biztosan törölni szeretnéd ezt a terméket?')) return;
    try {
        const response = await fetch('../api/admin/menu/delete.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: itemId}) });
        const result = await response.json();
        if (result.success) { showToast(result.message, 'success'); loadMenuItems(); } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba történt!', 'error'); }
}

function openCategoryModal(categoryId) {
    if (categoryId) {
        const category = categories.find(function(c) { return c.id == categoryId; });
        if (!category) return;
        document.getElementById('categoryModalTitle').textContent = 'Kategória szerkesztése';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
    } else {
        document.getElementById('categoryModalTitle').textContent = 'Új kategória';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
    }
    document.getElementById('categoryModal').classList.add('show');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.remove('show');
}

document.getElementById('categoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = { name: formData.get('name') };
    const categoryId = formData.get('id');
    if (categoryId) data.id = categoryId;
    const endpoint = categoryId ? 'update.php' : 'create.php';
    try {
        const response = await fetch('../api/admin/categories/' + endpoint, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        const result = await response.json();
        if (result.success) { showToast(result.message, 'success'); closeCategoryModal(); loadMenuItems(); } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba történt!', 'error'); }
});

async function deleteCategory(categoryId) {
    if (!confirm('Biztosan törölni szeretnéd ezt a kategóriát?')) return;
    try {
        const response = await fetch('../api/admin/categories/delete.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: categoryId}) });
        const result = await response.json();
        if (result.success) { showToast(result.message, 'success'); loadMenuItems(); } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba történt!', 'error'); }
}

function initImageUpload() {
    const zone = document.getElementById('imageUploadZone');
    const input = document.getElementById('imageFileInput');
    zone.addEventListener('click', function(e) { if (!e.target.closest('.remove-image-btn')) input.click(); });
    input.addEventListener('change', function(e) { if (e.target.files.length > 0) uploadImage(e.target.files[0]); });
    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragging'); });
    zone.addEventListener('dragleave', function() { zone.classList.remove('dragging'); });
    zone.addEventListener('drop', function(e) { e.preventDefault(); zone.classList.remove('dragging'); if (e.dataTransfer.files.length > 0) uploadImage(e.dataTransfer.files[0]); });
}

async function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    try {
        const response = await fetch('../api/admin/menu/upload.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            uploadedImageUrl = result.image_url;
            document.getElementById('itemImageUrl').value = result.image_url;
            document.getElementById('previewImage').src = result.image_url;
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'block';
            showToast('Kép feltöltve!', 'success');
        } else { showToast(result.message, 'error'); }
    } catch (error) { console.error('Error:', error); showToast('Hiba a feltöltés során!', 'error'); }
}

function removeImage() {
    uploadedImageUrl = null;
    document.getElementById('itemImageUrl').value = '';
    document.getElementById('imageFileInput').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImage').src = '';
}

window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.toggleAvailability = toggleAvailability;
window.deleteMenuItem = deleteMenuItem;
window.openCategoryModal = openCategoryModal;
window.closeCategoryModal = closeCategoryModal;
window.deleteCategory = deleteCategory;
window.removeImage = removeImage;
