<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Debreceni Étterem - Rendelje meg kedvenc ételeit online! Gyors kiszállítás, friss alapanyagok.">
    <title>Debreceni Étterem - Online Rendelés</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/home.css">
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
                <li><a href="/" class="active">Főoldal</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Ízek Debrecenből</h1>
                <p>Friss, házias ízek egyenesen az Ön otthonába. Rendeljen most és élvezze a valódi magyar konyhát!</p>
                <div class="hero-buttons">
                    <a href="/public/menu.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-book-open"></i> Étlap megtekintése
                    </a>
                    <a href="/public/register.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-user-plus"></i> Regisztráció (10% kedvezmény!)
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="text-center mb-4">Miért válassz minket?</h2>
            <p class="text-center" style="max-width: 600px; margin: 0 auto; color: var(--gray-600);">
                Több mint 10 éve szolgáljuk ki Debrecen lakóit a legjobb magyar ételekkel.
            </p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Gyors Kiszállítás</h3>
                    <p>20-60 perc alatt házhoz szállítjuk kedvenc ételeit a távolságtól függően.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Friss Alapanyagok</h3>
                    <p>Csak a legfrissebb, helyi termelőktől származó alapanyagokat használjuk.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <h3>Első Rendelés -10%</h3>
                    <p>Regisztrált felhasználóink első rendelésükből 10% kedvezményt kapnak!</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Értesítések</h3>
                    <p>Email értesítés minden rendelés státusz változásról.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Készen áll a rendelésre?</h2>
            <p>Fedezze fel gazdag étlapunkat és rendeljen most!</p>
            <a href="/public/menu.php" class="btn btn-hero btn-hero-primary">
                <i class="fas fa-pizza-slice"></i> Rendelés most
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h4>Debreceni Étterem</h4>
                    <p>Autentikus magyar konyhát kínálunk 2015 óta.</p>
                    <p style="margin-top: var(--space-4);">
                        <i class="fas fa-map-marker-alt"></i> Debrecen, Piac utca 1.<br>
                        <i class="fas fa-phone"></i> +36 30 123 4567<br>
                        <i class="fas fa-envelope"></i> info@debrecenietterem.hu
                    </p>
                </div>
                
                <div>
                    <h4>Gyors linkek</h4>
                    <ul class="footer-links">
                        <li><a href="/">Főoldal</a></li>
                        <li><a href="/public/menu.php">Étlap</a></li>
                        <li><a href="/public/my-orders.php">Rendeléseim</a></li>
                        <li><a href="/public/login.php">Bejelentkezés</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4>Nyitvatartás</h4>
                    <ul class="footer-links">
                        <li>Hétfő-Csütörtök: 10:00-22:00</li>
                        <li>Péntek: 10:00-23:00</li>
                        <li>Szombat: 11:00-23:00</li>
                        <li>Vasárnap: 11:00-21:00</li>
                    </ul>
                </div>
                
                <div>
                    <h4>Kövessen minket</h4>
                    <div style="display: flex; gap: var(--space-4); font-size: var(--text-2xl); margin-top: var(--space-4);">
                        <a href="#" style="color: var(--gray-300);"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--gray-300);"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: var(--gray-300);"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Debreceni Étterem. Minden jog fenntartva.</p>
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
