import { redirectIfLoggedIn } from "./auth.js";
import CONFIG from "./config.js";
    await redirectIfLoggedIn("dashboard.html");


    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    const loginBtn = document.getElementById('loginBtn');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        loginError.textContent = '';
        const username = usernameInput.value.trim();
        const password = passwordInput.value;

        if (!username || !password) {
            loginError.textContent = "Please enter both username and password.";
            return;
        }

        loginBtn.disabled = true;
        loginBtn.textContent = "Logging in...";

        try {
            const response = await fetch(CONFIG.API_BASE+'users/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password }),
                credentials: 'include'
            });

            const result = await response.json();

            if (response.ok) {
                alert("Login successful!");
                window.location.href = "dashboard.html";
            } else {
                loginError.textContent = result.message || "Invalid username or password.";
            }
        } catch (error) {
            console.error(error);
            loginError.textContent = "Server connection error.";
        } finally {
            loginBtn.disabled = false;
            loginBtn.textContent = "Login";
        }
    });
