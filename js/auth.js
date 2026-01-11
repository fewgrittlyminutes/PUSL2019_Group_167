class AuthManager {
    constructor() {
        this.init();
    }
    
    init() {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            const newForm = loginForm.cloneNode(true);
            loginForm.parentNode.replaceChild(newForm, loginForm);
            newForm.addEventListener('submit', (e) => this.handleLogin(e));
        }
    }
    
    async handleLogin(e) {
        e.preventDefault();
        
        const form = e.target;
        const username = form.querySelector('#username').value;
        const password = form.querySelector('#password').value;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Authenticating...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();

            if (result.success) {
                sessionStorage.setItem('utilitypro_user', JSON.stringify(result.user));
                window.location.href = 'dashboard.php';
            } else {
                throw new Error(result.message || 'Login failed');
            }

        } catch (error) {
            alert(error.message);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }
}

const authManager = new AuthManager();