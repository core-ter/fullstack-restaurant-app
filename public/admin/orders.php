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
    <title>Rendelések - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="assets/css/admin.css">
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
                <a href="orders.php" class="admin-nav-item active">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Rendelések</span>
                </a>
                <a href="menu.php" class="admin-nav-item">
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
                <h1><i class="fas fa-shopping-bag"></i> Rendelések</h1>
                <div class="admin-header-actions">
                    <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <div class="admin-main">
                <!-- Filters -->
                <div class="card">
                    <div class="card-body">
                        <div class="orders-filters">
                            <div class="filter-tabs">
                                <button class="filter-tab active" data-status="all" onclick="filterByStatus('all')">
                                    <i class="fas fa-list"></i>
                                    Aktív
                                </button>
                                <button class="filter-tab" data-status="pending" onclick="filterByStatus('pending')">
                                    <i class="fas fa-clock"></i>
                                    Elfogadásra vár
                                </button>
                                <button class="filter-tab" data-status="confirmed" onclick="filterByStatus('confirmed')">
                                    <i class="fas fa-check"></i>
                                    Elfogadva
                                </button>
                                <button class="filter-tab" data-status="preparing" onclick="filterByStatus('preparing')">
                                    <i class="fas fa-fire"></i>
                                    Készítés alatt
                                </button>
                                <button class="filter-tab" data-status="delivering" onclick="filterByStatus('delivering')">
                                    <i class="fas fa-truck"></i>
                                    Kiszállítás alatt
                                </button>
                                <button class="filter-tab" data-status="completed" onclick="filterByStatus('completed')">
                                    <i class="fas fa-check-circle"></i>
                                    Teljesítve
                                </button>
                                <button class="filter-tab" data-status="cancelled" onclick="filterByStatus('cancelled')">
                                    <i class="fas fa-ban"></i>
                                    Törölve
                                </button>
                            </div>

                            <div class="filter-search">
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    placeholder="Keresés rendelésszám vagy név alapján..." 
                                    onkeyup="searchOrders()"
                                >
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div id="ordersContainer">
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        Betöltés...
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/orders.js"></script>
</body>
</html>
