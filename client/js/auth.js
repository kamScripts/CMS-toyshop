export async function checkAuth() {
    try {
        const response = await fetch('http://localhost/CMS-toyshop/server/users/me', {
            method: 'GET',
            credentials: 'include',
            cache: 'no-store'
        });

        if (!response.ok) return null;

        const result = await response.json();
        return (result.status === "success") ? result.user : null;

    } catch (error) {
        console.error("Auth check failed:", error);
        return null;
    }
}

export async function redirectIfLoggedIn(targetPage = 'dashboard.html') {
    const user = await checkAuth();
    if (user) {
        localStorage.setItem('currentUser', JSON.stringify(user));
        window.location.href = targetPage;
    }
}