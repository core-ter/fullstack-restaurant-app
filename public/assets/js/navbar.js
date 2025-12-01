// Dynamic Navbar User Menu
document.addEventListener('DOMContentLoaded', async function () {
    const container = document.getElementById('userMenuContainer');
    if (!container) return;

    // Check if user is logged in
    const user = await checkAuth();

    if (user) {
        // User is logged in - show user dropdown
        container.innerHTML = `
            <div class="user-menu">
                <button class="user-menu-toggle" id="userMenuToggle">
                    <i class="fas fa-user-circle"></i>
                    <span>${user.first_name}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <a href="/user/profile.php" class="user-menu-item">
                        <i class="fas fa-user"></i> Profilom
                    </a>
                    <a href="/user/orders.php" class="user-menu-item">
                        <i class="fas fa-shopping-bag"></i> Rendeléseim
                    </a>

                    <div class="user-menu-divider"></div>
                    <button onclick="userLogout()" class="user-menu-item user-menu-logout">
                        <i class="fas fa-sign-out-alt"></i> Kijelentkezés
                    </button>
                </div>
            </div>
        `;

        // Dropdown toggle
        const toggle = document.getElementById('userMenuToggle');
        const dropdown = document.getElementById('userMenuDropdown');

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            dropdown.classList.remove('show');
        });

    } else {
        // User is NOT logged in - show login button
        container.innerHTML = `
            <a href="/login.php" class="btn btn-primary btn-sm">
                <i class="fas fa-sign-in-alt"></i>
                Bejelentkezés
            </a>
        `;
    }
});
