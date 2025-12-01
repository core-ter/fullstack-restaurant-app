<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosár - Debreceni Étterem</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    <link rel="stylesheet" href="/assets/css/cart.css">
    
    <!-- Font Awesome -->
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

    <!-- Cart Page -->
    <section class="cart-page">
        <div class="container">
            <h1 style="margin-bottom: var(--space-8); text-align: center;">Kosaram</h1>
            
            <div class="cart-container">
                <!-- Cart Items -->
                <div id="cartItems" class="cart-items">
                    <!-- Items will be rendered here by JavaScript -->
                </div>
                
                <!-- Cart Summary -->
                <div id="cartSummary">
                    <!-- Summary will be rendered here by JavaScript -->
                </div>
                
                <!-- Empty Cart State -->
                <div id="emptyCart" class="empty-cart" style="display: none;">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Üres a kosarad</h2>
                    <p>Még nem adtál hozzá semmit a kosárhoz</p>
                    <a href="/menu.php" class="btn btn-primary">
                        <i class="fas fa-utensils"></i>
                        Vissza az étlaphoz
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h4>Debreceni Étterem</h4>
                    <p>Hagyományos magyar ételek, modern stílusban</p>
                </div>
                <div>
                    <h4>Kapcsolat</h4>
                    <p>Telefon: +36 30 123 4567</p>
                    <p>Email: info@debrecenietterem.hu</p>
                </div>
                <div>
                    <h4>Nyitvatartás</h4>
                    <p>Hétfő-Péntek: 11:00 - 22:00</p>
                    <p>Hétvége: 12:00 - 23:00</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Debreceni Étterem. Minden jog fenntartva.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/auth.js"></script>
    <script src="/assets/js/cart.js"></script>
    <script src="/assets/js/navbar.js"></script>
</body>
</html>
