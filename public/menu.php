<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Étlapunk - Házias magyar ételek, friss alapanyagokból készítve. Rendeljen most online!">
    <title>Étlap - Debreceni Étterem</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/menu.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    
    <!-- Icon (Font Awesome CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="/" class="navbar-brand">
                <i class="fas fa-utensils"></i> Debreceni Étterem
            </a>
            
            <ul class="navbar-menu">
                <li><a href="/">Főoldal</a></li>
                <li><a href="/menu.php" class="active">Étlap</a></li>
            </ul>
            
            <div class="navbar-actions">
                <button id="theme-toggle" onclick="toggleTheme()" class="theme-toggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" style="display: none;">0</span>
                </a>
                
                <!-- User Menu (populated by JavaScript) -->
                <div id="userMenuContainer"></div>
            </div>
        </div>
    </nav>

    <!-- Menu Header -->
    <section class="menu-header">
        <div class="container">
            <h1>Étlapunk</h1>
            <p>Házias magyar ételek, friss alapanyagokból készítve</p>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="menu-section">
        <div class="container">
            <!-- Category Filters -->
            <div class="category-filters" id="categoryFilters">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-th-large"></i> Összes
                </button>
            </div>
            
            <!-- Menu Grid -->
            <div id="menuGrid" class="menu-grid">
                <!-- Loading spinner -->
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Étlap betöltése...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/menu.js"></script>
    <script src="/assets/js/cart.js"></script>
    <script src="/assets/js/navbar.js"></script>
</body>
</html>
