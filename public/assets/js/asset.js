// Global variables
let assets = [];
let filteredAssets = [];

// Load assets when page loads
document.addEventListener('DOMContentLoaded', function () {
    loadAssets();

    // Setup event listeners
    document.getElementById('searchInput')?.addEventListener('input', filterAssets);
    document.getElementById('tableSearchInput')?.addEventListener('input', filterAssets);
    document.getElementById('filterCategory')?.addEventListener('change', filterAssets);
    document.getElementById('filterStatus')?.addEventListener('change', filterAssets);
    document.getElementById('assetForm')?.addEventListener('submit', handleAssetSubmit);
    document.getElementById('borrowForm')?.addEventListener('submit', handleBorrowSubmit);
    document.getElementById('addAssetBtn')?.addEventListener('click', () => {
        clearAssetForm();
        document.getElementById('assetModal').classList.remove('hidden');
    });
    document.getElementById('exportCsvBtn')?.addEventListener('click', exportToCSV);

    // Check for 'add new asset' action from URL (cross-page link support)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'new_asset') {
        // Clean URL first
        window.history.replaceState({}, document.title, window.location.pathname);
        // Open modal
        clearAssetForm();
        document.getElementById('assetModal')?.classList.remove('hidden');
    }
});

// Load assets from API
function loadAssets() {
    fetch('/ukm/public/api/asset/index')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                assets = data.data;
                filteredAssets = assets;
                displayAssets();
                updateStats();
                populateCategories();
            }
        })
        .catch(error => console.error('Error:', error));
}

// Display assets in table
function displayAssets() {
    const tbody = document.getElementById('assetTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    filteredAssets.forEach(asset => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors';

        // Status badge
        let statusBadge = '';
        let statusColor = '';
        switch (asset.status) {
            case 'active':
                statusColor = 'bg-emerald-50 text-emerald-700';
                statusBadge = 'Available';
                break;
            case 'in_use':
                statusColor = 'bg-blue-50 text-blue-700';
                statusBadge = 'In Use';
                break;
            case 'maintenance':
                statusColor = 'bg-amber-50 text-amber-700'; // Changed from status-orange to match dashboard feel
                statusBadge = 'Maintenance';
                break;
            default:
                statusColor = 'bg-slate-100 text-slate-600';
                statusBadge = 'Unknown';
        }

        // Actions based on role - get from DOM data attribute
        const isAdmin = document.body.dataset.isAdmin === 'true';
        let actions = '';
        if (isAdmin) {
            actions = `
                <div class="flex items-center justify-end gap-1">
                    <button onclick="editAsset(${asset.id})" class="p-1.5 text-slate-400 hover:text-primary transition-colors" title="Edit">
                        <span class="material-symbols-outlined text-lg">edit_note</span>
                    </button>
                    <button onclick="deleteAsset(${asset.id})" class="p-1.5 text-slate-400 hover:text-red-500 transition-colors" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            `;
        } else {
            if (asset.status === 'active' && asset.quantity > 0) {
                actions = `<button class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all" onclick="initBorrow(${asset.id}, '${asset.name}')">Borrow</button>`;
            } else {
                actions = `<span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-lg text-xs font-bold uppercase tracking-widest">Unavailable</span>`;
            }
        }

        tr.innerHTML = `
            <td class="py-4 px-4">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-900">${asset.name}</span>
                    <!-- <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">ID: ${asset.code || 'N/A'}</span> -->
                </div>
            </td>
            <td class="py-4 px-4">
                <span class="text-sm font-medium text-slate-600">${asset.category}</span>
            </td>
            <td class="py-4 px-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider ${statusColor}">
                    ${statusBadge}
                </span>
            </td>
            <td class="py-4 px-4">
                <span class="text-sm text-slate-500 flex items-center gap-1.5 font-medium">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    ${asset.location}
                </span>
            </td>
            <td class="py-4 px-4">
                <span class="text-sm font-bold text-slate-700">${asset.quantity}</span>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-bold text-slate-400 uppercase">${formatDate(asset.created_at)}</span>
            </td>
            <td class="py-4 px-4 text-right">
                ${actions}
            </td>
        `;

        tbody.appendChild(tr);
    });

    updatePagination();
}

// Update statistics
function updateStats() {
    const total = assets.length;
    const inUse = assets.filter(a => a.status === 'in_use').length;
    const maintenance = assets.filter(a => a.status === 'maintenance').length;

    const elTotal = document.getElementById('totalCount');
    const elInUse = document.getElementById('inUseCount');
    const elMaintenance = document.getElementById('maintenanceCount');

    if (elTotal) elTotal.textContent = total;
    if (elInUse) elInUse.textContent = inUse;
    if (elMaintenance) elMaintenance.textContent = maintenance;
}

// Populate category dropdown
function populateCategories() {
    const categories = [...new Set(assets.map(a => a.category))];
    const select = document.getElementById('filterCategory');
    if (!select) return;

    // Clear existing options except first
    while (select.options.length > 1) {
        select.remove(1);
    }

    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat;
        option.textContent = cat;
        select.appendChild(option);
    });
}

// Filter assets
function filterAssets() {
    const searchEl = document.getElementById('searchInput');
    const tableSearchEl = document.getElementById('tableSearchInput');
    const categoryEl = document.getElementById('filterCategory');
    const statusEl = document.getElementById('filterStatus');

    if (!searchEl || !categoryEl || !statusEl) return;

    const search = searchEl.value.toLowerCase();
    const tableSearch = tableSearchEl ? tableSearchEl.value.toLowerCase() : '';
    const category = categoryEl.value;
    const status = statusEl.value;

    filteredAssets = assets.filter(asset => {
        const matchesHeaderSearch = asset.name.toLowerCase().includes(search) ||
            (asset.code && asset.code.toLowerCase().includes(search));
        const matchesTableSearch = !tableSearch ||
            asset.name.toLowerCase().includes(tableSearch) ||
            (asset.code && asset.code.toLowerCase().includes(tableSearch));
        const matchesCategory = !category || asset.category === category;
        const matchesStatus = !status || asset.status === status;

        return matchesHeaderSearch && matchesTableSearch && matchesCategory && matchesStatus;
    });

    displayAssets();
}

// Update pagination
function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    const count = filteredAssets.length;
    info.textContent = `Showing ${count > 0 ? '1 to ' + Math.min(10, count) : '0'} of ${count} entries`;
}


// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// Clear asset form
function clearAssetForm() {
    document.getElementById('assetForm').reset();
    document.getElementById('assetId').value = '';
    document.getElementById('quantity').value = '1'; // Enforce 1
    document.getElementById('assetStatus').value = 'active'; // Default
    document.getElementById('modalTitle').textContent = 'Register New Asset';
}

// Edit asset
function editAsset(id) {
    const asset = assets.find(a => a.id === id);
    if (!asset) return;

    document.getElementById('assetId').value = asset.id;
    document.getElementById('name').value = asset.name;
    document.getElementById('category').value = asset.category;
    document.getElementById('quantity').value = asset.quantity; // Should be 1
    document.getElementById('location').value = asset.location;
    document.getElementById('assetStatus').value = asset.status;
    document.getElementById('description').value = asset.description || '';
    document.getElementById('modalTitle').textContent = 'Modify Asset Configuration';
    document.getElementById('assetModal').classList.remove('hidden');
}

// Handle asset form submit
function handleAssetSubmit(e) {
    e.preventDefault();

    const id = document.getElementById('assetId').value;
    const formData = {
        id: id, // Add ID to body for Update
        name: document.getElementById('name').value,
        category: document.getElementById('category').value,
        location: document.getElementById('location').value,
        quantity: document.getElementById('quantity').value,
        status: document.getElementById('assetStatus').value,
        description: document.getElementById('description').value
    };

    const url = id ? `/ukm/public/api/asset/update` : '/ukm/public/api/asset/create';
    const method = 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('assetModal').classList.add('hidden');
                loadAssets();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
}

// Delete asset
function deleteAsset(id) {
    if (!confirm('Are you sure you want to delete this asset?')) return;

    fetch(`/ukm/public/api/asset/delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadAssets();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
}

// Initialize borrow
function initBorrow(id, name) {
    document.getElementById('borrowAssetId').value = id;
    document.getElementById('borrowAssetName').textContent = name;
    document.getElementById('borrowModal').classList.remove('hidden');
}

// Handle borrow form submit
function handleBorrowSubmit(e) {
    e.preventDefault();

    const formData = {
        asset_id: document.getElementById('borrowAssetId').value,
        quantity: document.getElementById('quantity').value,
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        purpose: document.getElementById('purpose').value
    };

    fetch('/ukm/public/api/borrow/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('borrowModal').classList.add('hidden');
                document.getElementById('borrowForm').reset();
                alert('Borrow request submitted successfully!');
                loadAssets();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
}
// Export to CSV
function exportToCSV() {
    if (!filteredAssets || filteredAssets.length === 0) {
        alert('No assets to export.');
        return;
    }

    // Define columns
    const headers = ['Name', 'Code', 'Category', 'Status', 'Location', 'Quantity', 'Added Date'];

    // Map data
    const rows = filteredAssets.map(asset => [
        `"${asset.name.replace(/"/g, '""')}"`, // Escape quotes
        `"${asset.code || ''}"`,
        `"${asset.category || ''}"`,
        `"${asset.status}"`,
        `"${asset.location || ''}"`,
        asset.quantity,
        `"${formatDate(asset.created_at)}"`
    ]);

    // Combine headers and rows
    const csvContent = [
        headers.join(','),
        ...rows.map(r => r.join(','))
    ].join('\n');

    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `asset_inventory_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
