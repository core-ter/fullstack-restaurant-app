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
    <title>Admin Dashboard - Debreceni Étterem</title>
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
                <a href="index.php" class="admin-nav-item active">
                    <i class="fas fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="orders.php" class="admin-nav-item">
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
                <h1>Dashboard</h1>
                <div class="admin-header-actions">
                    <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <div class="admin-main">
                <!-- Time Period Filters -->
                <div class="card">
                    <div class="card-body">
                        <div class="filter-tabs" id="periodFilters">
                            <button class="filter-tab active" data-period="today" onclick="changePeriod('today')">
                                <i class="fas fa-calendar-day"></i>
                                Ma
                            </button>
                            <button class="filter-tab" data-period="week" onclick="changePeriod('week')">
                                <i class="fas fa-calendar-week"></i>
                                Heti
                            </button>
                            <button class="filter-tab" data-period="month" onclick="changePeriod('month')">
                                <i class="fas fa-calendar-alt"></i>
                                Havi
                            </button>
                            <button class="filter-tab" data-period="all" onclick="changePeriod('all')">
                                <i class="fas fa-infinity"></i>
                                Összes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Rendelések</h3>
                            <p id="stat-total-orders">-</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Összes bevétel</h3>
                            <p id="stat-revenue">-</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Átlagos rendelés</h3>
                            <p id="stat-avg-order">-</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Aktív rendelések</h3>
                            <p id="stat-active-orders">-</p>
                        </div>
                    </div>
                </div>

                <!-- Popular Items & Status Breakdown -->
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-fire"></i> Népszerű termékek</h2>
                        </div>
                        <div class="card-body">
                            <div id="popular-items-container">
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i> Betöltés...
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-chart-pie"></i> Rendelések státusz szerint</h2>
                        </div>
                        <div class="card-body">
                            <div id="status-breakdown-container">
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i> Betöltés...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-history"></i> Legutóbbi rendelések</h2>
                        <a href="orders.php" class="btn btn-primary btn-sm">
                            Összes megtekintése
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div id="recent-orders-container">
                            <div class="loading-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                Betöltés...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/filter-fix.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
