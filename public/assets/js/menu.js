/**
 * Menu Page JavaScript
 * Handles menu item loading, category filtering, and cart operations
 */

let allMenuItems = [];
let allCategories = [];
let currentCategory = 'all';

/**
 * Initialize menu page
 */
document.addEventListener('DOMContentLoaded', () => {
    loadMenuData();
});

/**
 * Load menu items and categories from API
 */
async function loadMenuData() {
    try {
        const response = await fetch('/api/menu/get-items.php');
        const result = await response.json();

        if (result.success) {
            allMenuItems = result.data.items;
            allCategories = result.data.categories;

            renderCategoryFilters();
            renderMenuItems();
        } else {
            showError('Hiba az étlap betöltésekor.');
        }
    } catch (error) {
        console.error('Error loading menu:', error);
        showError('Hiba az étlap betöltésekor.');
    }
}

/**
 * Render category filter buttons
 */
function renderCategoryFilters() {
    const container = document.getElementById('categoryFilters');

    // Keep the "All" button
    const allButton = container.querySelector('[data-category="all"]');
    container.innerHTML = '';
    container.appendChild(allButton);

    // Add category buttons
    allCategories.forEach(category => {
        const button = document.createElement('button');
        button.className = 'category-btn';
        button.dataset.category = category.id;
        button.innerHTML = `<i class="fas fa-utensils"></i> ${category.name}`;
        button.addEventListener('click', () => filterByCategory(category.id));
        container.appendChild(button);
    });

    // Add click handler to "All" button
    allButton.addEventListener('click', () => filterByCategory('all'));
}

/**
 * Filter menu items by category
 */
function filterByCategory(categoryId) {
    currentCategory = categoryId;

    // Update active button
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const activeBtn = document.querySelector(`[data-category="${categoryId}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }

    renderMenuItems();
}

/**
 * Render menu items
 */
function renderMenuItems() {
    const container = document.getElementById('menuGrid');

    // Filter items
    let itemsToShow = allMenuItems;
    if (currentCategory !== 'all') {
        itemsToShow = allMenuItems.filter(item => item.category_id == currentCategory);
    }

    // Clear container
    container.innerHTML = '';

    // Show empty state if no items
    if (itemsToShow.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <h3>Nincs elérhető étel ebben a kategóriában</h3>
                <p>Próbáljon ki egy másik kategóriát!</p>
            </div>
        `;
        return;
    }

    // Render items
    itemsToShow.forEach(item => {
        const card = createMenuItemCard(item);
        container.appendChild(card);
    });
}

/**
 * Create menu item card element
 */
function createMenuItemCard(item) {
    const card = document.createElement('div');
    card.className = 'menu-item-card';

    const allergens = item.allergens ? item.allergens.split(',').map(a => a.trim()) : [];
    const allergensHTML = allergens.map(a => `<span class="allergen-tag">${a}</span>`).join('');

    // Create image HTML
    let imageHTML;
    if (item.image_url) {
        imageHTML = `<img src="${item.image_url}" alt="${item.name}" class="menu-item-image" onerror="this.src='/assets/images/placeholder-food.svg'">`;
    } else {
        imageHTML = `<img src="/assets/images/placeholder-food.svg" alt="${item.name}" class="menu-item-image">`;
    }

    card.innerHTML = `
        ${imageHTML}
        <div class="menu-item-content">
            <div class="menu-item-header">
                <h3 class="menu-item-name">${item.name}</h3>
                <span class="menu-item-price">${formatPrice(item.price)}</span>
            </div>
            ${item.description ? `<p class="menu-item-description">${item.description}</p>` : ''}
            ${item.ingredients ? `<p class="menu-item-ingredients">Összetevők: ${item.ingredients}</p>` : ''}
            ${allergens.length > 0 ? `<div class="menu-item-allergens">${allergensHTML}</div>` : ''}
            <div class="menu-item-footer">
                <button 
                    class="add-to-cart-btn" 
                    onclick="handleAddToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price})"
                    ${item.is_available == 0 ? 'disabled' : ''}
                >
                    <i class="fas fa-shopping-cart"></i>
                    ${item.is_available == 0 ? 'Nem elérhető' : 'Kosárba'}
                </button>
            </div>
        </div>
    `;

    return card;
}

/**
 * Handle add to cart button click
 */
function handleAddToCart(itemId, itemName, itemPrice) {
    const item = {
        id: itemId,
        name: itemName,
        price: itemPrice
    };

    addToCart(item, 1);
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.getElementById('menuGrid');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-circle"></i>
            <h3>${message}</h3>
            <button class="btn btn-primary" onclick="loadMenuData()">Újrapróbálás</button>
        </div>
    `;
}
