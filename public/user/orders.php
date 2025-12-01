<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Felhasználó';
?>
<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendeléseim - Debreceni Étterem</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/user-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="user-layout">
        <!-- Sidebar -->
        <aside class="user-sidebar">
            <div class="user-sidebar-header">
                <a href="/index.php">
                    <i class="fas fa-utensils"></i> Debreceni Étterem
                </a>
            </div>

            <nav class="user-nav">
                <a href="/index.php" class="user-nav-item">
                    <i class="fas fa-home"></i>
                    <span>Főoldal</span>
                </a>
                <a href="/user/profile.php" class="user-nav-item">
                    <i class="fas fa-user"></i>
                    <span>Profilom</span>
                </a>
                <a href="/user/orders.php" class="user-nav-item active">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Rendeléseim</span>
                </a>

            </nav>

            <div class="user-sidebar-footer">
                <div style="margin-bottom: var(--space-3); color: var(--text-secondary); font-size: var(--text-sm);">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userName); ?>
                </div>
                <button onclick="userLogout()" class="btn btn-secondary btn-block btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Kijelentkezés
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="user-content">
            <header class="user-header">
                <h1><i class="fas fa-shopping-bag"></i> Rendeléseim</h1>
                <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-moon"></i>
                </button>
            </header>

            <div class="user-main">
                <!-- Orders List -->
                <div class="card">
                    <div class="card-body">
                        <div id="ordersContainer">
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

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="modalOrderNumber">Rendelés részletei</h3>
                <button onclick="closeOrderModal()" class="modal-close">&times;</button>
            </div>
            <div id="orderDetailsContainer"></div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/user/assets/js/orders.js"></script>
</body>
</html>
