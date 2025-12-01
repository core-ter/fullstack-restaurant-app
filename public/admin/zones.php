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
    <title>Kiszállítási Zónák - Admin</title>
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
                <a href="orders.php" class="admin-nav-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Rendelések</span>
                </a>
                <a href="menu.php" class="admin-nav-item">
                    <i class="fas fa-utensils"></i>
                    <span>Menü kezelés</span>
                </a>
                <a href="zones.php" class="admin-nav-item active">
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
                <h1><i class="fas fa-map-marked-alt"></i> Kiszállítási Zónák</h1>
                <div class="admin-header-actions">
                    <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button onclick="openAddModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Új zóna
                    </button>
                </div>
            </header>

            <div class="admin-main">
                <!-- Zones Table -->
                <div class="card">
                    <div class="card-body">
                        <div id="zonesContainer">
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

    <!-- Zone Modal -->
    <div id="zoneModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Új zóna</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <form id="zoneForm">
                <input type="hidden" id="zoneId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="distanceFrom">Távolság-tól (km) *</label>
                        <input type="number" id="distanceFrom" name="distance_from_km" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="distanceTo">Távolság-ig (km) *</label>
                        <input type="number" id="distanceTo" name="distance_to_km" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zoneFee">Kiszállítási díj (Ft) *</label>
                        <input type="number" id="zoneFee" name="fee" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="deliveryTime">Szállítási idő (perc)</label>
                        <input type="number" id="deliveryTime" name="delivery_time_minutes" min="10" value="30">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Mégse</button>
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/zones.js"></script>
</body>
</html>
