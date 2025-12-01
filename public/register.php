<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Debreceni Étterem</title>
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
                <p>Regisztráció</p>
            </div>

            <form id="registerForm" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">Keresztnév *</label>
                        <input type="text" id="firstName" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Vezetéknév *</label>
                        <input type="text" id="lastName" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email cím *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Telefonszám</label>
                    <input type="tel" id="phone" name="phone" placeholder="+36 30 123 4567">
                </div>

                <div class="form-group">
                    <label for="password">Jelszó *</label>
                    <input type="password" id="password" name="password" required minlength="8">
                    <small>Legalább 8 karakter</small>
                </div>

                <div class="form-group">
                    <label for="password confirm">Jelszó megerősítése *</label>
                    <input type="password" id="passwordConfirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i>
                    Regisztráció
                </button>
            </form>

            <div class="auth-footer">
                <p>Már van fiókod? <a href="login.php">Bejelentkezés</a></p>
                <p><a href="index.php"><i class="fas fa-arrow-left"></i> Vissza a főoldalra</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
