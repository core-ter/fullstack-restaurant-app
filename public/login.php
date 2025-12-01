<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés - Debreceni Étterem</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-utensils"></i> Debreceni Étterem</h1>
                <p>Bejelentkezés</p>
            </div>

            <form id="loginForm" class="auth-form">
                <div class="form-group">
                    <label for="email">Email cím *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Jelszó *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Bejelentkezés
                </button>
            </form>

            <div class="auth-footer">
                <p>Még nincs fiókod? <a href="register.php">Regisztráció</a></p>
                <p><a href="index.php"><i class="fas fa-arrow-left"></i> Vissza a főoldalra</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
