import CONFIG from './config.js'
const registerForm = document.querySelector('#registerForm');
const username = document.querySelector('#regUsername');
const email = document.querySelector('#regEmail');
const usernameError = document.querySelector('#usernameError');
const usernameConstraint = document.querySelector('#usernameConstraints');

const passwordInput = document.getElementById('regPassword');
const confirmInput = document.getElementById('confirmPassword');
const passwordMessage = document.getElementById('passwordMessage');
const strengthBar = document.getElementById('strengthBar');
const confirmError = document.getElementById('confirmError');
const submitBtn = document.querySelector('#registerBtn');
const hasUpper = /[A-Z]/;
const hasLower = /[a-z]/;
const hasNumber = /[0-9]/;
const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/;

// Regex: letters, numbers, and underscore
const usernameRegex = /^[a-zA-Z0-9_]+$/;

//USERNAME VALIDATION (onkeyup)
username.addEventListener('keyup', () => {
    const value = username.value.trim();

    usernameConstraint.textContent = '';
    usernameConstraint.classList.remove('error', 'success');

    if (value === '') {
        return; // don't show error while typing empty
    }

    if (!usernameRegex.test(value)) {
        usernameConstraint.textContent = "Username can only contain letters, numbers, and underscores (_)";
        usernameConstraint.classList.add('error');
    }
    else if (value.length < 3) {
        usernameConstraint.textContent = "Username must be at least 3 characters long";
        usernameConstraint.classList.add('error');
    }
    else if (value.length > 16) {
        usernameConstraint.textContent = "Username must be maximum 16 characters long";
        usernameConstraint.classList.add('error');
    }
});

//USERNAME AVAILABILITY CHECK (onblur)
username.addEventListener('blur', async () => {
    const value = username.value.trim();

    // Clear previous availability message
    usernameError.textContent = '';
    usernameError.classList.remove('error', 'success');

    // Don't check if field is empty or has format errors
    if (value.length < 3 || value.length > 16 || !usernameRegex.test(value)) {
        return;
    }

    try {
        const response = await fetch(CONFIG.API_BASE+'users/checkUser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username: value })
        });

        const result = await response.json();

        usernameError.textContent = result.message;

        if (result.status === "error") {
            usernameError.classList.add('error');
        } else {
            usernameError.classList.add('success');
        }

    } catch (err) {
        console.error(err);
        usernameError.textContent = "Could not check username availability";
        usernameError.classList.add('error');
    }
});

//PASSWORD STRENGTH CHECK
passwordInput.addEventListener('input', () => {
    const password = passwordInput.value;

    passwordMessage.textContent = '';
    strengthBar.style.width = '0%';
    strengthBar.className = 'strength-bar'; // reset classes

    if (password === '') {
        return;
    }

    let strength = 0;
    let message = '';

    // Length check
    if (password.length < 9) {
        message = "Password must be at least 9 characters long";
    }
    else if (password.length > 32) {
        message = "Password must be maximum 32 characters long";
    }
    else {
        // Count criteria
        if (hasUpper.test(password)) strength++;
        if (hasLower.test(password)) strength++;
        if (hasNumber.test(password)) strength++;
        if (hasSpecial.test(password)) strength++;

        if (strength === 4) {
            message = "Strong password";
            strengthBar.classList.add('strong');
        }
        else if (strength === 3) {
            message = "Good password.";
            strengthBar.classList.add('good');
        }
        else if (strength === 2) {
            message = "Medium password.";
            strengthBar.classList.add('medium');
        }
        else {
            message = "Weak password.";
            strengthBar.classList.add('weak');
        }
    }

    passwordMessage.textContent = message;
    strengthBar.style.width = `${(strength / 4) * 100}%`;
});
//CONFIRM PASSWORD MATCH
confirmInput.addEventListener('input', () => {
    const password = passwordInput.value;
    const confirm = confirmInput.value;

    confirmError.textContent = '';
    confirmError.classList.remove('error', 'success');

    if (confirm === '') {
        return;
    }

    if (password === confirm) {
        confirmError.textContent = "Passwords match.";
        confirmError.classList.add('success');
    } else {
        confirmError.textContent = "Passwords do not match.";
        confirmError.classList.add('error');
    }
});
//SEND REQUEST
registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Clean and prepare values for post
    const value = username.value.trim();
    const trimmedEmail = email.value.trim();
    const passwordValue = passwordInput.value;
    const confirmValue = confirmInput.value;
    //Validate again and return if not pass
    if (!usernameRegex.test(value)) {
        alert(' Username can only contain letters, numbers, and underscores');
        return;
    }

    if (value.length < 3) {
        alert("Username must be at least 3 characters long.");
        return;
    }

    if (!trimmedEmail.includes('@') || trimmedEmail.length < 5) {
        alert("Please enter a valid email address.");
        return;
    }

    if (passwordValue.length < 6) {
        alert("Password must be at least 6 characters long.");
        return;
    }

    if (passwordValue !== confirmValue) {
        alert("Passwords do not match!");
        return;
    }

    // Disable button for request processing
    submitBtn.disabled = true;
    submitBtn.textContent = "Creating account...";

    try {
        const response = await fetch(CONFIG.API_BASE+'users/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: value,
                email: trimmedEmail,
                password: passwordValue
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert("Registration successful! You can now log in.");
            registerForm.reset();
            window.location.href = "login.html";
        } else {
            alert(result.message || "Registration failed. Please try again.");
        }

    } catch (error) {
        console.error("Error:", error);
        alert("Unable to connect to server. Please try again later.");
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.textContent = "Register";

    }
});