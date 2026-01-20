/**
 * user.js
 * Manages the User Management interface for Organization Admins.
 * Handles listing users, searching/filtering, and creating new users.
 */

// --- State Management ---
let users = [];         // Full list of users fetched from API
let filteredUsers = []; // List currently displayed after filtering

/**
 * Initialization
 * Loads users and sets up event listeners.
 */
document.addEventListener('DOMContentLoaded', function () {
    loadUsers();

    // --- Event Listeners ---
    document.getElementById('searchInput')?.addEventListener('input', filterUsers);
    document.getElementById('tableSearchInput')?.addEventListener('input', filterUsers);
    document.getElementById('userForm')?.addEventListener('submit', handleUserSubmit);
});

/**
 * Fetches the user list from the API.
 */
async function loadUsers() {
    try {
        const res = await fetchAPI('/auth/getUsers');
        if (res.status === 'success') {
            users = res.data;
            filteredUsers = users;
            displayUsers();
            updateStats();
        }
    } catch (err) {
        console.error('Error loading users:', err);
    }
}

/**
 * Renders the users table with role badges and actions.
 */
function displayUsers() {
    const tbody = document.getElementById('userTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    filteredUsers.forEach(user => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors group';

        // --- Role Badge Logic ---
        let roleBadge = '';
        if (user.role === 'org_admin') {
            roleBadge = '<span class="px-2 py-0.5 bg-primary/5 text-primary text-[10px] font-black rounded uppercase tracking-wider border border-primary/10">Administrator</span>';
        } else {
            roleBadge = '<span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wider border border-slate-200">Staff Member</span>';
        }

        tr.innerHTML = `
            <td class="py-4 px-8">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold border border-slate-200 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-all">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-sm font-bold text-slate-900">${user.name}</span>
                </div>
            </td>
            <td class="py-4 px-4">
                <span class="text-sm font-medium text-slate-600">${user.email}</span>
            </td>
            <td class="py-4 px-4">
                ${roleBadge}
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-tight">${formatDate(user.created_at)}</span>
            </td>
            <td class="py-4 px-8 text-right">
                <button class="p-1.5 text-slate-400 hover:text-primary transition-colors opacity-0 group-hover:opacity-100 italic text-[10px] font-bold uppercase tracking-widest">
                    View Profile
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });

    updatePagination();
}

/**
 * Updates the dashboard statistics counters (Total Members, Admins, Recent Joins).
 */
function updateStats() {
    const total = users.length;
    const admins = users.filter(u => u.role === 'org_admin').length;

    // Recent joins (last 30 days)
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    const recent = users.filter(u => new Date(u.created_at) > thirtyDaysAgo).length;

    const elTotal = document.getElementById('totalMembers');
    const elAdmins = document.getElementById('adminCount');
    const elRecent = document.getElementById('recentJoins');

    if (elTotal) animateCounter(elTotal, total);
    if (elAdmins) animateCounter(elAdmins, admins);
    if (elRecent) animateCounter(elRecent, recent);
}

/**
 * Filters the user list based on search input.
 */
function filterUsers() {
    const headerSearch = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const tableSearch = document.getElementById('tableSearchInput')?.value.toLowerCase() || '';
    const query = headerSearch || tableSearch;

    filteredUsers = users.filter(user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
    );

    displayUsers();
}

/**
 * Handles the creation of a new user.
 */
async function handleUserSubmit(e) {
    e.preventDefault();
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    const data = {
        name: document.getElementById('userName').value,
        email: document.getElementById('userEmail').value,
        password: document.getElementById('userPassword').value
    };

    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Provisioning...';

        const res = await fetchAPI('/auth/createUser', 'POST', data);
        if (res.status === 'success') {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userForm').reset();
            loadUsers();
            showToast('User created successfully', 'success');
        }
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

/**
 * Updates the pagination text info.
 */
function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    const count = filteredUsers.length;
    info.textContent = `Showing ${count > 0 ? '1 to ' + Math.min(10, count) : '0'} of ${count} entries`;
}

// --- Helpers ---

/**
 * Formats a date string to specific locale format.
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
}

/**
 * Animates a number counting up from 0 to target.
 */
function animateCounter(el, target) {
    let curr = 0;
    const step = Math.ceil(target / 20) || 1;
    const timer = setInterval(() => {
        curr += step;
        if (curr >= target) {
            el.textContent = target;
            clearInterval(timer);
        } else {
            el.textContent = curr;
        }
    }, 30);
}
