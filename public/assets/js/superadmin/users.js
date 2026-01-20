/**
 * public/assets/js/superadmin/users.js
 * Manages the Global User Registry for Super Admins.
 */

let allUsers = [];

document.addEventListener('DOMContentLoaded', () => {
    loadGlobalUsers();
    document.getElementById('tableSearchInput')?.addEventListener('input', handleSearch);
});

async function loadGlobalUsers() {
    const tableBody = document.getElementById('userTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-400">Loading registry...</td></tr>';

    try {
        const res = await fetchAPI('/user/indexGlobal');
        if (res.status === 'success') {
            allUsers = res.data;
            renderUsers(allUsers);
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500 font-bold">${res.message}</td></tr>`;
        }
    } catch (err) {
        console.error(err);
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500 font-bold">Failed to load users</td></tr>`;
    }
}

function renderUsers(users) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';

    if (users.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-slate-400">No users found in the system</td></tr>`;
        return;
    }

    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors border-b border-slate-50';

        // Role Badge logic
        let roleBadge = '';
        if (user.role === 'super_admin') {
            roleBadge = '<span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-black rounded uppercase tracking-wider border border-purple-200">System Root</span>';
        } else if (user.role === 'org_admin') {
            roleBadge = '<span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded uppercase tracking-wider border border-blue-100">Org Admin</span>';
        } else {
            roleBadge = '<span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wider border border-slate-200">Member</span>';
        }

        // Date formatting
        const joinDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
        const orgName = user.organization_name || '<span class="text-slate-400 italic">System Scope</span>';

        tr.innerHTML = `
            <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 leading-tight">${user.name}</p>
                        <p class="text-[10px] text-slate-400 font-medium">${user.email}</p>
                    </div>
                </div>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-semibold text-slate-700 block truncate max-w-[150px]" title="${user.organization_name}">
                    ${orgName}
                </span>
            </td>
            <td class="py-4 px-4">
                ${roleBadge}
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-mono text-slate-500">${joinDate}</span>
            </td>
            <td class="py-4 px-6 text-right">
                <button onclick="openManageModal('${user.id}')" class="text-slate-400 hover:text-primary transition-colors text-xs font-bold uppercase tracking-wider">
                    Manage
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });
}

function handleSearch(e) {
    const query = e.target.value.toLowerCase();
    const filtered = allUsers.filter(u =>
        u.name.toLowerCase().includes(query) ||
        u.email.toLowerCase().includes(query) ||
        (u.organization_name && u.organization_name.toLowerCase().includes(query))
    );
    renderUsers(filtered);
}

// --- Manage User Logic ---

function openManageModal(userId) {
    const user = allUsers.find(u => u.id == userId);
    if (!user) return;

    document.getElementById('manageUserId').value = user.id;
    document.getElementById('manageName').value = user.name;
    document.getElementById('manageEmail').value = user.email;
    document.getElementById('manageRole').value = user.role;
    document.getElementById('managePassword').value = ''; // Reset password field

    const modal = document.getElementById('manageUserModal');
    modal.classList.remove('hidden');
    // Small delay to allow display:block to apply before opacity transition
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('manageUserModalContent').classList.remove('scale-95');
        document.getElementById('manageUserModalContent').classList.add('scale-100');
    }, 10);
}

function closeManageModal() {
    const modal = document.getElementById('manageUserModal');
    modal.classList.add('opacity-0');
    document.getElementById('manageUserModalContent').classList.remove('scale-100');
    document.getElementById('manageUserModalContent').classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Bind form submit
document.getElementById('manageUserForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const data = {
        id: document.getElementById('manageUserId').value,
        name: document.getElementById('manageName').value,
        email: document.getElementById('manageEmail').value,
        role: document.getElementById('manageRole').value,
        password: document.getElementById('managePassword').value
    };

    try {
        const res = await fetchAPI('/user/updateGlobal', 'POST', data);
        if (res.status === 'success') {
            closeManageModal();
            loadGlobalUsers(); // Refresh list
            // Optionally show toast
            alert('User updated successfully');
        } else {
            alert(res.message || 'Update failed');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
});

async function deleteUser() {
    if (!confirm('Are you sure you want to PERMANENTLY delete this user? This action cannot be undone.')) {
        return;
    }

    const userId = document.getElementById('manageUserId').value;
    try {
        const res = await fetchAPI('/user/deleteGlobal', 'POST', { id: userId });
        if (res.status === 'success') {
            closeManageModal();
            loadGlobalUsers();
            alert('User deleted successfully');
        } else {
            alert(res.message || 'Delete failed');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred');
    }
}
