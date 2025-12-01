<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userName = $_SESSION['first_name'] ?? 'Felhasználó';
?>
<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilom - Debreceni Étterem</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
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
                <a href="/user/profile.php" class="user-nav-item active">
                    <i class="fas fa-user"></i>
                    <span>Profilom</span>
                </a>
                <a href="/user/orders.php" class="user-nav-item">
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
                <h1><i class="fas fa-user"></i> <span id="headerUserName">Betöltés...</span></h1>
                <button onclick="toggleTheme()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-moon"></i>
                </button>
            </header>

            <div class="user-main">
                <!-- Statistics Cards -->
                <div class="stats-grid" id="statsContainer">
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        Betöltés...
                    </div>
                </div>

                <!-- Profile Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Személyes adatok</h3>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="first_name">Keresztnév</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Vezetéknév</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email cím</label>
                                    <input type="email" id="email" name="email" class="form-control" readonly>
                                    <small class="text-muted">Email cím nem módosítható</small>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Telefonszám</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" disabled>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" id="editBtn" onclick="toggleEditMode()" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> Szerkesztés
                                </button>
                                <button type="submit" id="saveBtn" class="btn btn-primary" style="display: none;">
                                    <i class="fas fa-save"></i> Mentés
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Change Card -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-key"></i> Jelszó megváltoztatása</h3>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="current_password">Jelenlegi jelszó</label>
                                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <!-- Empty placeholder for grid alignment if needed, or just let it flow -->
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Új jelszó</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                                    <small class="text-muted">Legalább 8 karakter</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Új jelszó megerősítése</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Jelszó frissítése
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/user/assets/js/profile.js"></script>
</body>
</html>
