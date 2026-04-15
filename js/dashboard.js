
import { checkAuth } from './auth.js';
import CONFIG from './config.js';
document.addEventListener('DOMContentLoaded', async () => {

    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in to access the dashboard.");
        window.location.href = "login.html";
        return;
    }

    document.getElementById('displayUsername').textContent = user.username;
    document.getElementById('displayEmail').textContent = user.email || 'Not provided';
    const editProfileBtn = document.getElementById('editProfileBtn');
    const deleteProfileBtn = document.getElementById('deleteProfileBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const card = document.querySelector('.card');

    const logout = async () => {
        try {
            await fetch(CONFIG.API_BASE+'users/logout', {
                method: 'POST',
                credentials: 'include'
            });

            localStorage.removeItem('currentUser');
            window.location.href = 'login.html';
        } catch (e) {
            console.error(e);
            window.location.href = 'login.html';
        }
    }
    logoutBtn.addEventListener('click', async () => {
        if (confirm('Are you sure you want to logout?')) {
            await logout()
        }
    });
    card.appendChild(logoutBtn);

    deleteProfileBtn.addEventListener('click', async () => {
        const user_id = user.user_id
        console.log("user_id: " + user_id);
        if (confirm('Are you sure you want to delete profile?')) {
            try {
                 await fetch(CONFIG.API_BASE+`users/${user_id}`, {
                    method: 'DELETE',
                    credentials: 'include'
                });

                localStorage.removeItem('currentUser');
                logout();
            } catch (e) {
                console.error(e);
                window.location.href = 'login.html';
            }
        }
    })

    editProfileBtn.addEventListener('click', () => {
        alert("Profile editing feature coming soon!");
    });
});