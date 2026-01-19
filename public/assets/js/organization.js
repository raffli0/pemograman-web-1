/**
 * organization.js
 * Handles Platform Admin viewing and managing organizations
 */

// Depends on auth.js for fetchAPI

async function loadOrganizations() {
    const tbody = document.getElementById('orgTableBody');
    if (!tbody) return;

    try {
        const res = await fetchAPI('/organization/index');
        tbody.innerHTML = '';

        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No organizations found.</td></tr>';
            return;
        }

        res.data.forEach(org => {
            let statusBadge = org.status === 'active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Suspended</span>';

            let actionBtn = org.status === 'active'
                ? `<button class="btn btn-sm btn-outline-danger" onclick="toggleOrgStatus(${org.id}, 'suspended')">Suspend</button>`
                : `<button class="btn btn-sm btn-outline-success" onclick="toggleOrgStatus(${org.id}, 'active')">Activate</button>`;

            tbody.innerHTML += `
                <tr>
                    <td><strong>${org.name}</strong></td>
                    <td>${statusBadge}</td>
                    <td class="small text-muted">${org.created_at}</td>
                    <td>${actionBtn}</td>
                </tr>
            `;
        });
    } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="4" class="text-danger text-center">Failed to load data.</td></tr>';
    }
}

async function toggleOrgStatus(id, newStatus) {
    if (!confirm(`Set organization status to ${newStatus}?`)) return;

    try {
        await fetchAPI('/organization/updateStatus', 'POST', { id, status: newStatus });
        loadOrganizations();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Register Org Form Logic (public page)
const registerForm = document.getElementById('registerOrgForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            org_name: document.getElementById('orgName').value,
            admin_name: document.getElementById('adminName').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        try {
            const res = await fetchAPI('/auth/registerOrg', 'POST', data);
            if (res.status === 'success') {
                showToast('Organization registered successfully. Please login.', 'success');
                setTimeout(() => window.location.href = 'login.php', 2000);
            }
        } catch (err) {
            document.getElementById('error-msg').textContent = err.message;
        }
    });
}
