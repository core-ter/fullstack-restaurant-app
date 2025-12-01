<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Bejelentkezés - Debreceni Étterem</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <i class="fas fa-utensils"></i>
                <h1>Admin Panel</h1>
                <p>Debreceni Étterem</p>
            </div>

            <form id="loginForm" class="admin-login-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Felhasználónév
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        autocomplete="username"
                        placeholder="admin"
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Jelszó
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Bejelentkezés
                </button>
            </form>

            <div class="admin-login-footer">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i>
                    Vissza a főoldalra
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>
