/**
 * member_asset.js
 * Manages the interactive functionality of the Member Asset Catalog.
 * Handles display, filtering, and borrowing requests.
 */

// --- State Management ---
let assets = [];            // Complete list of assets fetched from API
let filteredAssets = [];    // Subset of assets currently displayed (after search/filter)

/**
 * Initialization
 * Loads assets and sets up global event listeners when the DOM is ready.
 */
document.addEventListener('DOMContentLoaded', function () {
    loadAssets();

    // --- Event Listeners ---
    document.getElementById('tableSearchInput')?.addEventListener('input', filterAssets);
    document.getElementById('filterCategory')?.addEventListener('change', filterAssets);
    document.getElementById('filterStatus')?.addEventListener('change', filterAssets);
    document.getElementById('borrowForm')?.addEventListener('submit', handleBorrowSubmit);
});

/**
 * Fetches the latest asset data from the backend API.
 * Updates state, UI, and statistics.
 */
function loadAssets() {
    fetch('/ukm/public/api/asset/index')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                assets = data.data;
                filteredAssets = assets;

                // Refresh UI components
                displayAssets();
                updateStats();
                populateCategories();
            }
        })
        .catch(error => console.error('Error:', error));
}

/**
 * Renders the asset list into the HTML table (Member Catalog View).
 */
function displayAssets() {
    const tbody = document.getElementById('assetTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    filteredAssets.forEach(asset => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors';

        // --- Member Status Badge Logic ---
        let statusBadge = '';
        let statusColor = '';

        switch (asset.status) {
            case 'active':
                if (asset.quantity > 0) {
                    statusColor = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                    statusBadge = 'Ready to Borrow';
                } else {
                    statusColor = 'bg-slate-100 text-slate-500 border border-slate-200';
                    statusBadge = 'Out of Stock';
                }
                break;
            case 'in_use':
                statusColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                statusBadge = 'Currently Borrowed';
                break;
            case 'maintenance':
                statusColor = 'bg-slate-100 text-slate-400 border border-slate-200';
                statusBadge = 'Unavailable';
                break;
            default:
                statusColor = 'bg-slate-100 text-slate-400';
                statusBadge = 'Unavailable';
        }

        // --- Action Buttons ---
        let actions = '';
        if (asset.status === 'active' && asset.quantity > 0) {
            actions = `<button class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all" onclick="initBorrow(${asset.id}, '${asset.name}')">Request Borrow</button>`;
        } else {
            actions = `<span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-lg text-xs font-bold uppercase tracking-widest">Unavailable</span>`;
        }

        // Construct Row HTML
        tr.innerHTML = `
            <td class="py-4 px-4">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-900">${asset.name}</span>
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

/**
 * Updates the dashboard counter cards with live data (Member Stats).
 */
function updateStats() {
    // MEMBER STATS
    const elMemAvail = document.getElementById('memberAvailableCount');
    const elMemActive = document.getElementById('memberActiveBorrows');
    const elMemPending = document.getElementById('memberPendingRequests');

    if (elMemAvail) {
        // Available Assets (calculated locally)
        const available = assets.filter(a => a.status === 'active' && a.quantity > 0).length;
        elMemAvail.textContent = available;

        // Fetch User Specific Stats from Backend
        fetch('/ukm/public/api/dashboard/index')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (elMemActive) elMemActive.textContent = res.data.your_active_borrows || 0;
                    if (elMemPending) elMemPending.textContent = res.data.your_pending_requests || 0;
                }
            })
            .catch(e => console.error("Stats Error", e));
    }
}

/**
 * Dynamically populates the Category filter dropdown based on unique categories in the dataset.
 */
function populateCategories() {
    const categories = [...new Set(assets.map(a => a.category))];
    const select = document.getElementById('filterCategory');
    if (!select) return;

    // Reset options (keep first "All" option)
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

/**
 * Sorts and filters the asset list based on user input.
 */
function filterAssets() {
    const tableSearchEl = document.getElementById('tableSearchInput');
    const categoryEl = document.getElementById('filterCategory');
    const statusEl = document.getElementById('filterStatus');

    if (!categoryEl || !statusEl) return;

    const tableSearch = tableSearchEl ? tableSearchEl.value.toLowerCase() : '';
    const category = categoryEl.value;
    const status = statusEl.value;

    filteredAssets = assets.filter(asset => {
        const matchesTableSearch = !tableSearch ||
            asset.name.toLowerCase().includes(tableSearch) ||
            (asset.code && asset.code.toLowerCase().includes(tableSearch));
        const matchesCategory = !category || asset.category === category;
        const matchesStatus = !status || asset.status === status;

        return matchesTableSearch && matchesCategory && matchesStatus;
    });

    displayAssets();
}

/**
 * Updates the pagination text (e.g., "Showing 1 to 10 of 50").
 */
function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    const count = filteredAssets.length;
    info.textContent = `Showing ${count > 0 ? '1 to ' + Math.min(10, count) : '0'} of ${count} entries`;
}

// --- Helper Functions ---

/**
 * Formats a ISO date string into a readable format (e.g., "Jan 1, 2024")
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Opens the Borrow Modal for a specific asset.
 * @param {number} id - Asset ID
 * @param {string} name - Asset Name
 */
function initBorrow(id, name) {
    document.getElementById('borrowAssetId').value = id;
    document.getElementById('borrowAssetName').textContent = name;
    document.getElementById('borrowModal').classList.remove('hidden');
}

/**
 * Handles the submission of a Borrow Request.
 */
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
                showToast('Borrow request submitted successfully!', 'success');
                loadAssets();
            } else {
                showToast('Error: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => console.error('Error:', error));
}
