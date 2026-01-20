/**
 * auth.js
 * Handles Authentication, Token Management, and Shared API Helpers
 */

const API_BASE = '/ukm/public/api';



// Core Fetch Wrapper
async function fetchAPI(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    if (data) {
        options.body = JSON.stringify(data);
    }

    const response = await fetch(`${API_BASE}${endpoint}`, options);

    // Global 401 Handling
    if (response.status === 401) {
        // Only redirect if not already on login page
        if (!window.location.pathname.includes('login.php')) {
            window.location.href = '/ukm/public/pages/login.php';
        }
        throw new Error('Unauthorized');
    }

    const result = await response.json();
    if (!response.ok) {
        throw new Error(result.message || 'API Error');
    }
    return result;
}

// Auth Actions
async function login(email, password) {
    return await fetchAPI('/auth/login', 'POST', { email, password });
}

async function logout() {
    try {
        await fetchAPI('/auth/logout', 'POST');
    } finally {
        localStorage.removeItem('user_role');
        window.location.href = '/ukm/public/pages/login.php';
    }
}

// For use in forms
const loginBtn = document.getElementById('loginBtn');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

async function handleLoginSubmit(e) {
    if (e) e.preventDefault();

    const email = emailInput.value;
    const password = passwordInput.value;
    const errorMsg = document.getElementById('error-msg');

    // Add loading state
    if (loginBtn) {
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="relative z-10">Signing In...</span>';
    }

    try {
        const res = await login(email, password);

        if (res.status === 'success') {
            localStorage.setItem('user_role', res.data.role);
            // Redirect based on role
            if (res.data.role === 'org_admin' || res.data.role === 'super_admin') {
                window.location.href = '/ukm/public/pages/admin/dashboard.php';
            } else {
                window.location.href = '/ukm/public/pages/member/assets.php';
            }
        }
    } catch (err) {
        console.error('Login Error:', err);
        if (errorMsg) errorMsg.textContent = 'Invalid credentials or Server Error. Check console.';
    } finally {
        if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<span class="relative z-10">Sign In</span>';
        }
    }
}

if (loginBtn) {
    loginBtn.addEventListener('click', handleLoginSubmit);
}

// Bind Enter Key
if (passwordInput) {
    passwordInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            handleLoginSubmit(e);
        }
    });
}
if (emailInput) {
    emailInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            handleLoginSubmit(e);
        }
    });
}
