<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendelés követése - Debreceni Étterem</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/track-order.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
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
                <li><a href="/menu.php">Étlap</a></li>
            </ul>
            
            <div class="navbar-actions">
                <button id="theme-toggle" onclick="toggleTheme()" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
                <div id="userMenuContainer"></div>
            </div>
        </div>
    </nav>

    <!-- Order Tracking Section -->
    <section class="track-order-section">
        <div class="container">
            <div id="trackingContainer">
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Rendelés betöltése...</p>
                </div>
            </div>
        </div>
    </section>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/navbar.js"></script>
    <script src="/assets/js/track-order.js"></script>
</body>
</html>
