/**
 * Admin Login JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        // Clear previous errors
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

        if (!username || !password) {
            showToast('Töltsd ki az összes mezőt!', 'error');
            return;
        }

        try {
            const response = await fetch('../api/admin/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'login',
                    username,
                    password
                })
            });

            const result = await response.json();

            if (result.success) {
                showToast('Sikeres bejelentkezés!', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                showToast(result.message || 'Hibás felhasználónév vagy jelszó!', 'error');
                document.getElementById('password').classList.add('error');
            }
        } catch (error) {
            console.error('Login error:', error);
            showToast('Hiba történt a bejelentkezés során!', 'error');
        }
    });
});
