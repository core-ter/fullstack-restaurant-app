<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendeléseim - Debreceni Étterem</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .orders-page {
            padding: var(--space-16) 0;
            min-height: 70vh;
        }
        
        .orders-header {
            text-align: center;
            margin-bottom: var(--space-12);
        }
        
        .orders-list {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .order-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            margin-bottom: var(--space-6);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-4);
            padding-bottom: var(--space-4);
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-pending { background: #FFA500; color: white; }
        .status-confirmed { background: #06D6A0; color: white; }
        .status-preparing { background: #3B82F6; color: white; }
        .status-delivering { background: #8B5CF6; color: white; }
        .status-completed { background: #10B981; color: white; }
        .status-cancelled { background: #EF4444; color: white; }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-4);
            margin-bottom: var(--space-4);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-item i {
            color: var(--primary);
        }
        
        .order-items {
            margin-bottom: var(--space-4);
        }
        
        .order-items h4 {
            margin-bottom: var(--space-2);
            font-size: 1rem;
        }
        
        .item-list {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: var(--space-3);
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        
        .item-row:not(:last-child) {
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-total {
            display: flex;
            justify-content: flex-end;
            gap: var(--space-8);
            padding-top: var(--space-4);
            border-top: 2px solid var(--border-color);
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: var(--space-16) var(--space-4);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: var(--space-4);
        }
        
        .loading {
            text-align: center;
            padding: var(--space-16);
        }
        
        .spinner {
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto var(--space-4);
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
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
                <li><a href="/my-orders.php" class="active">Rendeléseim</a></li>
                <li><a href="/login.php">Bejelentkezés</a></li>
            </ul>
            
            <div class="navbar-actions">
                <button id="theme-toggle" onclick="toggleTheme()" class="theme-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" style="display: none;">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Orders Page -->
    <section class="orders-page">
        <div class="container">
            <div class="orders-header">
                <h1><i class="fas fa-receipt"></i> Rendeléseim</h1>
                <p>Tekintsd meg korábbi rendeléseidet és azok státuszát</p>
            </div>
            
            <!-- Loading State -->
            <div id="loadingState" class="loading">
                <div class="spinner"></div>
                <p>Rendelések betöltése...</p>
            </div>
            
            <!-- Orders List -->
            <div id="ordersList" class="orders-list" style="display: none;">
                <!-- Orders will be rendered here -->
            </div>
            
            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <i class="fas fa-inbox"></i>
                <h2>Még nincs rendelésed</h2>
                <p>Kezdj el rendelni kedvenc ételeidből!</p>
                <a href="/menu.php" class="btn btn-primary" style="margin-top: var(--space-4);">
                    <i class="fas fa-utensils"></i> Étlap megtekintése
                </a>
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
    <script src="/assets/js/cart.js"></script>
    <script>
        // Load orders on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();
        });
        
        async function loadOrders() {
            const loadingState = document.getElementById('loadingState');
            const ordersList = document.getElementById('ordersList');
            const emptyState = document.getElementById('emptyState');
            
            try {
                const response = await fetch('/api/orders/list.php');
                const data = await response.json();
                
                loadingState.style.display = 'none';
                
                if (data.success && data.orders && data.orders.length > 0) {
                    ordersList.style.display = 'block';
                    renderOrders(data.orders);
                } else {
                    emptyState.style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                loadingState.style.display = 'none';
                emptyState.style.display = 'block';
            }
        }
        
        function renderOrders(orders) {
            const ordersList = document.getElementById('ordersList');
            
            ordersList.innerHTML = orders.map(order => `
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">#${order.order_number}</div>
                        <div class="order-status status-${order.status_code}">${order.status_name}</div>
                    </div>
                    
                    <div class="order-info">
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>${formatDate(order.created_at)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${truncateAddress(order.delivery_address)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>${order.guest_phone}</span>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <h4>Tételek:</h4>
                        <div class="item-list">
                            ${order.items.map(item => `
                                <div class="item-row">
                                    <span>${item.quantity}x ${item.item_name}</span>
                                    <span>${formatPrice(item.item_price * item.quantity)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="order-total">
                        <span>Összesen:</span>
                        <span style="color: var(--primary);">${formatPrice(order.total_amount)}</span>
                    </div>
                </div>
            `).join('');
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('hu-HU', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function truncateAddress(address) {
            return address.length > 50 ? address.substring(0, 50) + '...' : address;
        }
    </script>
</body>
</html>
