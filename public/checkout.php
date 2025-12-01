<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pénztár - Debreceni Étterem</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    <link rel="stylesheet" href="/assets/css/checkout.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google reCAPTCHA v3 Configuration -->
    <?php
    require_once __DIR__ . '/api/config/database.php';
    $recaptchaSiteKey = RECAPTCHA_SITE_KEY;
    ?>
    <script>
        window.RECAPTCHA_SITE_KEY = '<?php echo $recaptchaSiteKey; ?>';
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $recaptchaSiteKey; ?>"></script>
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
                <a href="/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" style="display: none;">0</span>
                </a>
                
                <!-- User Menu (populated by JavaScript) -->
                <div id="userMenuContainer"></div>
            </div>
        </div>
    </nav>

    <!-- Checkout Page -->
    <section class="checkout-page">
        <div class="container">
            <h1 style="margin-bottom: var(--space-8); text-align: center;">Rendelés véglegesítése</h1>
            
            <div class="checkout-container">
                <!-- Left Column: Forms -->
                <div class="checkout-forms">
                    <!-- Delivery Information -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-map-marker-alt"></i> Szállítási adatok</h2>
                        
                        <form id="checkoutForm">
                            <div id="personal-details-section">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name">Név *</label>
                                        <input type="text" id="name" name="name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">Telefonszám *</label>
                                        <input type="tel" id="phone" name="phone" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email cím *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="addressSearch">Pontos cím * (Kezd el gépelni a kereséshez)</label>
                                <div class="autocomplete-wrapper">
                                    <input type="text" id="addressSearch" name="address" placeholder="Utca, házszám..." autocomplete="off" required>
                                    <div id="addressResults" class="autocomplete-results"></div>
                                </div>
                            </div>
                            
                            <!-- Map Container -->
                            <div id="map" class="map-container"></div>
                            <p class="map-hint"><i class="fas fa-info-circle"></i> Kattints a térképre a pontos hely kijelöléséhez</p>
                            
                            <div class="form-group">
                                <label for="notes">Megjegyzés (opcionális)</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Pl.: csengő nem működik, hívj telefonon"></textarea>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-credit-card"></i> Fizetési mód</h2>
                        
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment" value="cash" checked>
                                <div class="payment-card">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Készpénz átvételkor</span>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment" value="card">
                                <div class="payment-card">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Bankkártya átvételkor</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Order Summary -->
                <div class="checkout-summary">
                    <h2>Rendelés összesítő</h2>
                    
                    <div id="orderItems" class="order-items">
                        <!-- Items will be rendered here -->
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Részösszeg:</span>
                            <span id="subtotal">0 Ft</span>
                        </div>
                        <div class="summary-row">
                            <span>Szállítási díj:</span>
                            <span id="delivery">500 Ft</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Fizetendő:</span>
                            <span id="total">0 Ft</span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="submitOrder()" class="btn btn-primary btn-submit">
                        <i class="fas fa-check-circle"></i>
                        Rendelés leadása
                    </button>
                    
                    <a href="/cart.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Vissza a kosárhoz
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Kis türelmet...</div>
        </div>
    </div>

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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/assets/js/checkout.js"></script>
    <script src="/assets/js/navbar.js"></script>
</body>
</html>
