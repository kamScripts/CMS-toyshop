import {checkAuth} from "./auth.js";


document.addEventListener('DOMContentLoaded', async () => {
    const user = await checkAuth();
    if (user) {
        console.log("Logged in as:", user.username);
    } else {
        console.log("Guest user");
    }
});