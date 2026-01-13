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
            window.location.href = 'login.php';
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
        window.location.href = 'login.php';
    }
}

// For use in forms
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorMsg = document.getElementById('error-msg');

        try {
            const res = await login(email, password);
            if (res.status === 'success') {
                localStorage.setItem('user_role', res.data.role);
                window.location.href = 'dashboard.php';
            }
        } catch (err) {
            if (errorMsg) errorMsg.textContent = 'Invalid credentials. Please try again.';
        }
    });
}
