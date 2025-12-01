// Helper function to get reCAPTCHA token
async function getRecaptchaToken(action) {
    if (typeof grecaptcha === 'undefined' || !window.RECAPTCHA_SITE_KEY) {
        console.warn('reCAPTCHA not loaded or configured');
        return '';
    }

    return new Promise((resolve) => {
        grecaptcha.ready(() => {
            grecaptcha.execute(window.RECAPTCHA_SITE_KEY, { action: action })
                .then(token => resolve(token))
                .catch(err => {
                    console.error('reCAPTCHA error:', err);
                    resolve('');
                });
        });
    });
}

// Login form handling
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Bejelentkezés...';

        const formData = new FormData(e.target);
        const token = await getRecaptchaToken('login');

        const data = {
            email: formData.get('email'),
            password: formData.get('password'),
            recaptcha_token: token
        };

        try {
            const response = await fetch('/api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(function () {
                    window.location.href = '/index.php';
                }, 500);
            } else {
                showToast(result.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        } catch (error) {
            console.error('Login error:', error);
            showToast('Hiba történt a bejelentkezés során', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

// Register form handling
if (document.getElementById('registerForm')) {
    document.getElementById('registerForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const password = formData.get('password');
        const passwordConfirm = formData.get('password_confirm');

        // Validate password match
        if (password !== passwordConfirm) {
            showToast('A jelszavak nem egyeznek!', 'error');
            return;
        }

        // Validate password length
        if (password.length < 8) {
            showToast('A jelszónak legalább 8 karakter hosszúnak kell lennie!', 'error');
            return;
        }

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regisztráció...';

        const token = await getRecaptchaToken('register');

        const data = {
            email: formData.get('email'),
            password: password,
            first_name: formData.get('first_name'),
            last_name: formData.get('last_name'),
            phone: formData.get('phone'),
            recaptcha_token: token
        };

        try {
            const response = await fetch('/api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(function () {
                    window.location.href = '/login.php';
                }, 500);
            } else {
                showToast(result.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        } catch (error) {
            console.error('Registration error:', error);
            showToast('Hiba történt a regisztráció során', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

// Check if user is logged in
async function checkAuth() {
    try {
        const response = await fetch('/api/auth/check.php');
        const result = await response.json();
        return result.logged_in ? result.user : null;
    } catch (error) {
        console.error('Auth check error:', error);
        return null;
    }
}

// Logout function
async function userLogout() {
    try {
        const response = await fetch('/api/auth/logout.php', { method: 'POST' });
        const result = await response.json();

        if (result.success) {
            showToast('Sikeres kijelentkezés', 'success');
            setTimeout(function () {
                window.location.href = '/index.php';
            }, 500);
        }
    } catch (error) {
        console.error('Logout error:', error);
        showToast('Hiba történt', 'error');
    }
}

window.userLogout = userLogout;
window.checkAuth = checkAuth;
