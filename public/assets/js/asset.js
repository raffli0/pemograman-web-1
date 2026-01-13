/**
 * asset.js
 * Handles Asset CRUD and Listing
 */

const assetModalEl = document.getElementById('assetModal');
const bsAssetModal = assetModalEl ? new bootstrap.Modal(assetModalEl) : null;
const assetForm = document.getElementById('assetForm');

async function loadAssets(isAdmin) {
    const tbody = document.getElementById('assetTableBody');
    if (!tbody) return;

    try {
        const res = await fetchAPI('/asset/index');
        tbody.innerHTML = '';

        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted small py-3">No assets found.</td></tr>';
            return;
        }

        res.data.forEach(asset => {
            let actions = '';
            // Status Badge
            let statusBadge = `<span class="badge bg-success">Active</span>`;
            if (asset.status === 'maintenance') statusBadge = `<span class="badge bg-warning text-dark">Maintenance</span>`;
            if (asset.status === 'lost') statusBadge = `<span class="badge bg-danger">Lost</span>`;

            if (isAdmin) {
                actions = `
                    <button class="btn btn-sm btn-light border py-0 px-2 small" onclick='editAsset(${JSON.stringify(asset)})'>Edit</button>
                    <button class="btn btn-sm btn-outline-danger py-0 px-2 small" onclick="deleteAsset(${asset.id})">Del</button>
                `;
            } else {
                if (asset.status === 'active' && asset.quantity > 0) {
                    actions = `<button class="btn btn-sm btn-primary py-0 px-2 small" onclick="initBorrow(${asset.id}, '${asset.name}')">Borrow</button>`;
                } else if (asset.status !== 'active') {
                    actions = `<span class="badge bg-secondary">Unavailable</span>`;
                } else {
                    actions = `<span class="badge bg-secondary">Out of Stock</span>`;
                }
            }

            tbody.innerHTML += `
                <tr>
                    <td><strong>${asset.name}</strong></td>
                    <td class="small text-muted">${asset.description || '-'}</td>
                    <td>${asset.quantity}</td>
                    <td class="small">${asset.condition_note}</td>
                    <td>${statusBadge}</td>
                    <td>${actions}</td>
                </tr>
            `;
        });
    } catch (err) {
        console.error(err);
    }
}

if (assetForm) {
    assetForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('assetId').value;
        const data = {
            id: id,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            quantity: document.getElementById('quantity').value,
            condition_note: document.getElementById('condition').value,
            status: document.getElementById('status') ? document.getElementById('status').value : 'active'
        };

        const url = id ? '/asset/update' : '/asset/store';
        try {
            await fetchAPI(url, 'POST', data);
            bsAssetModal.hide();
            if (typeof loadAssets === 'function') loadAssets(true);
        } catch (err) { alert(err.message); }
    });
}

function editAsset(asset) {
    document.getElementById('assetId').value = asset.id;
    document.getElementById('name').value = asset.name;
    document.getElementById('description').value = asset.description;
    document.getElementById('quantity').value = asset.quantity;
    document.getElementById('condition').value = asset.condition_note;

    // Status Field Handling
    const statusField = document.getElementById('status');
    if (statusField) statusField.value = asset.status;

    document.getElementById('modalTitle').textContent = 'Modify Asset';
    bsAssetModal.show();
}

function clearAssetForm() {
    assetForm.reset();
    document.getElementById('assetId').value = '';
    const statusField = document.getElementById('status');
    if (statusField) statusField.value = 'active';
    document.getElementById('modalTitle').textContent = 'New Asset';
}

async function deleteAsset(id) {
    if (confirm('Confirm deletion? This will be logged.')) {
        await fetchAPI('/asset/delete', 'POST', { id });
        if (typeof loadAssets === 'function') loadAssets(true);
    }
}
