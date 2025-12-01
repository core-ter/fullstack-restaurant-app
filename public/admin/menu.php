<?php
require_once __DIR__ . '/../api/admin/middleware.php';
requireAuth();

$adminUsername = getAdminUsername();
?>
<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menü kezelés - Debreceni Étterem</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/filters.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
                <p>Debreceni Étterem</p>
            </div>

            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="orders.php" class="admin-nav-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Rendelések</span>
                </a>
                <a href="menu.php" class="admin-nav-item active">
                    <i class="fas fa-utensils"></i>
                    <span>Menü kezelés</span>
                </a>
                <a href="zones.php" class="admin-nav-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Kiszállítási zónák</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <div style="margin-bottom: var(--space-3); color: var(--text-secondary); font-size: var(--text-sm);">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminUsername); ?>
                </div>
                <button onclick="logout()" class="btn btn-secondary btn-block btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Kijelentkezés
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-utensils"></i> Menü kezelés</h1>
                <div class="admin-header-actions">
                    <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button onclick="openAddModal()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Új termék
                    </button>
                </div>
            </header>

            <div class="admin-main">
                <!-- Category Filters -->
                <div class="card">
                    <div class="card-body">
                        <div class="filter-tabs" id="categoryFilters">
                            <button class="filter-tab active" data-category="all" onclick="filterByCategory('all')">
                                <i class="fas fa-list"></i>
                                Összes
                            </button>
                            <button class="filter-tab filter-tab-add" onclick="openCategoryModal()" title="Új kategória">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu Items Grid -->
                <div id="menuItemsContainer">
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        Betöltés...
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="menuModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Új termék</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <form id="menuForm">
                <input type="hidden" id="itemId" name="id">
                <input type="hidden" id="itemImageUrl" name="image_url">
                
                <div class="form-group">
                    <label>Termék kép</label>
                    <div class="image-upload-zone" id="imageUploadZone">
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Húzd ide a képet vagy kattints a feltöltéshez</p>
                            <small>JPEG, PNG, WebP | Max 2MB</small>
                        </div>
                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImage" src="" alt="Preview">
                            <button type="button" class="remove-image-btn" onclick="removeImage()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="file" id="imageFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="itemName">Név *</label>
                    <input type="text" id="itemName" name="name" required>
                </div>

                <div class="form-group">
                    <label for="itemCategory">Kategória *</label>
                    <select id="itemCategory" name="category_id" required></select>
                </div>

                <div class="form-group">
                    <label for="itemPrice">Ár (Ft) *</label>
                    <input type="number" id="itemPrice" name="price" min="0" step="10" required>
                </div>

                <div class="form-group">
                    <label for="itemDescription">Leírás</label>
                    <textarea id="itemDescription" name="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="itemIngredients">Összetevők</label>
                    <textarea id="itemIngredients" name="ingredients" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="itemAllergens">Allergének</label>
                    <input type="text" id="itemAllergens" name="allergens" placeholder="pl. tej, tojás, glutén">
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="itemAvailable" name="is_available" checked>
                        Elérhető
                    </label>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Mégse</button>
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 id="categoryModalTitle">Új kategória</h3>
                <button onclick="closeCategoryModal()" class="modal-close">&times;</button>
            </div>
            <form id="categoryForm">
                <input type="hidden" id="categoryId" name="id">
                
                <div class="form-group">
                    <label for="categoryName">Kategória név *</label>
                    <input type="text" id="categoryName" name="name" required>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeCategoryModal()" class="btn btn-secondary">MĂ©gse</button>
                    <button type="submit" class="btn btn-primary">MentĂ©s</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/filter-fix.js"></script>`r`n    <script src="assets/js/menu.js"></script>
</body>
</html>

