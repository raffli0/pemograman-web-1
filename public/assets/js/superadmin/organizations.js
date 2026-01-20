/**
 * public/assets/js/superadmin/organizations.js
 * Manages Organization CRUD operations.
 */

let organizations = [];

document.addEventListener('DOMContentLoaded', () => {
    console.log('Org JS Loaded');
    loadOrganizations();
});

async function loadOrganizations() {
    const tbody = document.getElementById('orgTableBody');
    if (!tbody) return;

    try {
        const res = await fetchAPI('/organization/index');
        if (res.status === 'success') {
            organizations = res.data;
            renderTable(organizations);
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500 font-bold">${res.message}</td></tr>`;
        }
    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500 font-bold">Failed to load data</td></tr>`;
    }
}

function renderTable(data) {
    const tbody = document.getElementById('orgTableBody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-slate-400">No organizations registered via system</td></tr>`;
        return;
    }

    data.forEach(org => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors border-b border-slate-50';

        const statusBadge = org.status === 'active'
            ? '<span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded uppercase tracking-wider border border-emerald-100">Active</span>'
            : '<span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-black rounded uppercase tracking-wider border border-red-100">Suspended</span>';

        const actionBtn = org.status === 'active'
            ? `<button onclick="updateStatus('${org.id}', 'suspended')" class="text-red-400 hover:text-red-600 text-[10px] font-bold uppercase tracking-wider">Suspend</button>`
            : `<button onclick="updateStatus('${org.id}', 'active')" class="text-emerald-400 hover:text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Activate</button>`;

        const date = new Date(org.created_at).toLocaleDateString();

        tr.innerHTML = `
            <td class="py-4 px-6">
                <span class="font-bold text-slate-700">${org.name}</span>
            </td>
            <td class="py-4 px-4">
                ${statusBadge}
            </td>
            <td class="py-4 px-4">
                <span class="text-sm font-medium text-slate-600">${org.owner_name || '<span class="text-slate-400 italic">No Owner</span>'}</span>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-mono text-slate-500">${date}</span>
            </td>
            <td class="py-4 px-6 text-right space-x-3">
                ${actionBtn}
                <button onclick="openOrgModal('${org.id}')" class="text-primary hover:text-primary/80 text-[10px] font-bold uppercase tracking-wider">Edit</button>
                <button onclick="deleteOrg('${org.id}')" class="text-slate-400 hover:text-red-500 transition-colors text-[10px] font-bold uppercase tracking-wider">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// --- CRUD Operations ---

async function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to set status to ${status.toUpperCase()}?`)) return;

    try {
        const res = await fetchAPI('/organization/updateStatus', 'POST', { id, status });
        if (res.status === 'success') {
            loadOrganizations();
        } else {
            alert(res.message);
        }
    } catch (err) {
        console.error(err);
        alert('Action failed');
    }
}

async function deleteOrg(id) {
    if (!confirm('Are you sure you want to PERMANENTLY delete this organization? All associated data may be lost.')) return;

    try {
        const res = await fetchAPI('/organization/delete', 'POST', { id });
        if (res.status === 'success') {
            loadOrganizations();
        } else {
            alert(res.message);
        }
    } catch (err) {
        console.error(err);
        alert('Delete failed');
    }
}

// --- Modal Logic ---

function openOrgModal(orgId = null) {
    const modal = document.getElementById('orgModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('orgForm');

    // Reset form
    form.reset();
    document.getElementById('orgId').value = '';
    const adminFields = document.getElementById('adminFields');
    const requiredInputs = adminFields.querySelectorAll('input');

    if (orgId) {
        const org = organizations.find(o => o.id == orgId);
        if (org) {
            document.getElementById('orgId').value = org.id;
            document.getElementById('orgName').value = org.name;
            title.textContent = 'Edit Organization';

            // Hide Admin Fields in Edit Mode
            adminFields.classList.add('hidden');
            requiredInputs.forEach(input => input.removeAttribute('required'));
        }
    } else {
        title.textContent = 'New Organization';
        // Show Admin Fields in Create Mode
        adminFields.classList.remove('hidden');
        requiredInputs.forEach(input => input.setAttribute('required', 'true'));
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('orgModalContent').classList.remove('scale-95');
        document.getElementById('orgModalContent').classList.add('scale-100');
    }, 10);
}

function closeOrgModal() {
    const modal = document.getElementById('orgModal');
    modal.classList.add('opacity-0');
    document.getElementById('orgModalContent').classList.remove('scale-100');
    document.getElementById('orgModalContent').classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Expose to Global Scope for HTML onclick
window.openOrgModal = openOrgModal;
window.closeOrgModal = closeOrgModal;
window.updateStatus = updateStatus;
window.deleteOrg = deleteOrg;

// Form Submit (Create/Update)
document.getElementById('orgForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const id = document.getElementById('orgId').value;
    const name = document.getElementById('orgName').value;

    const endpoint = id ? '/organization/update' : '/organization/create';
    let payload = { name };

    if (id) {
        payload.id = id;
    } else {
        // Add Admin details for Create
        payload.admin_name = document.getElementById('adminName').value;
        payload.email = document.getElementById('adminEmail').value;
        payload.password = document.getElementById('adminPassword').value;
    }

    try {
        const res = await fetchAPI(endpoint, 'POST', payload);
        if (res.status === 'success') {
            closeOrgModal();
            loadOrganizations();
            // showToast('Saved Successfully', 'success');
        } else {
            alert(res.message);
        }
    } catch (err) {
        console.error(err);
        alert('Operation failed');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
});
