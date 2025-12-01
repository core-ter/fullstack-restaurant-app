<!DOCTYPE html>
<html lang="hu" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email megerősítés - Debreceni Étterem</title>
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
                <p>Email megerősítés</p>
            </div>

            <div id="verificationStatus" class="auth-form">
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Email cím ellenőrzése...</p>
                </div>
            </div>

            <div class="auth-footer">
                <p><a href="index.php"><i class="fas fa-arrow-left"></i> Vissza a főoldalra</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const params = new URLSearchParams(window.location.search);
            const token = params.get('token');
            const container = document.getElementById('verificationStatus');
            
            if (!token) {
                container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiányzó verification link</p></div>';
                return;
            }
            
            try {
                const response = await fetch('/api/auth/verify-email.php?token=' + encodeURIComponent(token));
                const result = await response.json();
                
                if (result.success) {
                    container.innerHTML = '<div class="success-state" style="text-align: center; padding: 2rem;"><i class="fas fa-check-circle" style="font-size: 4rem; color: var(--success); margin-bottom: 1rem;"></i><h2>Sikeres megerősítés!</h2><p>' + result.message + '</p><a href="/login.php" class="btn btn-primary" style="margin-top: 1rem;">Most jelentkezz be a felhasználódba!</a><p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">Átirányítás a bejelentkezéshez 3 másodpercen belül...</p></div>';
                    setTimeout(() => {
                        window.location.href = '/login.php';
                    }, 3000);
                } else {
                    container.innerHTML = '<div class="error-state" style="text-align: center; padding: 2rem;"><i class="fas fa-times-circle" style="font-size: 4rem; color: var(--error); margin-bottom: 1rem;"></i><h2>Hiba</h2><p>' + result.message + '</p></div>';
                }
            } catch (error) {
                console.error('Verification error:', error);
                container.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Hiba történt a megerősítés során</p></div>';
            }
        });
    </script>
</body>
</html>
