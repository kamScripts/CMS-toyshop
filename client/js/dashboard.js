
import { checkAuth } from './auth.js';

document.addEventListener('DOMContentLoaded', async () => {

    const user = await checkAuth();
    if (!user) {
        alert("You must be logged in to access the dashboard.");
        window.location.href = "login.html";
        return;
    }

    document.getElementById('displayUsername').textContent = user.username;
    document.getElementById('displayEmail').textContent = user.email || 'Not provided';

    const logoutBtn = document.createElement('button');
    logoutBtn.textContent = 'Logout';
    logoutBtn.classList.add('secondaryBttn');
    logoutBtn.style.marginLeft = '10px';

    logoutBtn.addEventListener('click', async () => {
        if (confirm('Are you sure you want to logout?')) {
            try {
                await fetch('http://localhost/CMS-toyshop/server/users/logout', {
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
    });

    const card = document.querySelector('.card');
    card.appendChild(logoutBtn);

    document.getElementById('editProfileBtn').addEventListener('click', () => {
        alert("Profile editing feature coming soon!");
    });
});