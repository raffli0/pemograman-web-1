/**
 * member_borrow.js
 * Manages the Borrow Requests interface for Members ONLY.
 * Handles request listing and return submissions.
 */

// --- State Management ---
let requests = [];          // Borrow requests loaded from API
let filteredRequests = [];  // Requests currently displayed

/**
 * Initialization
 */
document.addEventListener('DOMContentLoaded', function () {
    // --- Event Listeners ---
    document.getElementById('searchInput')?.addEventListener('input', filterRequests);
    document.getElementById('tableFilter')?.addEventListener('input', filterRequests);
    document.getElementById('returnSubmitForm')?.addEventListener('submit', handleReturnSubmit);

    // Initial Load
    loadMemberBorrowRequests();
});

/**
 * Fetches member's borrow requests.
 */
async function loadMemberBorrowRequests() {
    const tbody = document.getElementById('borrowTableBody');
    if (!tbody) return;

    // Show loading state
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12"><span class="material-symbols-outlined animate-spin text-primary">progress_activity</span></td></tr>`;

    try {
        const res = await fetchAPI('/borrow/index');
        if (res.status === 'success') {
            requests = res.data;
            filteredRequests = requests;
            displayMemberRequests();
        }
    } catch (err) {
        console.error('Error loading requests:', err);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-rose-500 font-medium">Failed to load requests. Please try again.</td></tr>`;
    }
}

/**
 * Renders the member's requests into the table.
 * Strictly Member View: No admin controls, no user columns.
 */
function displayMemberRequests() {
    const tbody = document.getElementById('borrowTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (filteredRequests.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-slate-400 font-bold uppercase tracking-widest text-[10px]">No borrowing records found</td></tr>`;
        return;
    }

    filteredRequests.forEach(req => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors group';

        // --- Status Badge ---
        let statusBadge = '';
        let badgeStyle = '';
        switch (req.status) {
            case 'pending':
                badgeStyle = 'bg-amber-50 text-amber-700';
                statusBadge = 'Pending Review';
                break;
            case 'approved':
                badgeStyle = 'bg-emerald-50 text-emerald-700';
                statusBadge = 'Active';
                break;
            case 'returning':
                badgeStyle = 'bg-blue-50 text-blue-700';
                statusBadge = 'Return Processing';
                break;
            case 'rejected':
                badgeStyle = 'bg-slate-100 text-slate-500';
                statusBadge = 'Declined';
                break;
            case 'returned':
                badgeStyle = 'bg-slate-100 text-slate-400';
                statusBadge = 'Returned';
                break;
            default:
                badgeStyle = 'bg-slate-100 text-slate-500';
                statusBadge = req.status;
        }

        // --- Action Buttons (Member Only) ---
        let actions = '';
        if (req.status === 'approved') {
            actions = `
                <div class="flex items-center justify-end">
                    <button onclick="openReturnModal(${req.id})" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-primary hover:text-white transition-all shadow-sm">
                        Return Asset
                    </button>
                </div>
            `;
        } else if (req.status === 'pending') {
            // Optional: Add Cancel button if API supports it
            actions = `<span class="text-[10px] text-slate-400 font-medium italic">Awaiting Approval</span>`;
        }

        // --- Table Row Content (Member Columns) ---
        // Columns: Asset Name | Duration | Request Date | Status | Action
        tr.innerHTML = `
            <td class="py-4 px-8">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-900">${req.asset_name}</span>
                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">Qty: ${req.quantity || 1}</span>
                </div>
            </td>
            <td class="py-4 px-4">
                 <span class="text-xs font-medium text-slate-600">
                    ${req.start_date} <span class="text-slate-400 mx-1">to</span> ${req.end_date}
                </span>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs text-slate-500">${new Date(req.created_at || Date.now()).toLocaleDateString()}</span>
            </td>
            <td class="py-4 px-4">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider ${badgeStyle}">
                    ${statusBadge}
                </span>
            </td>
            <td class="py-4 px-8 text-right">${actions}</td>
        `;

        tbody.appendChild(tr);
    });

    updatePagination();
}

/**
 * Filters requests locally.
 */
function filterRequests() {
    const query = (document.getElementById('tableFilter')?.value || '').toLowerCase();

    filteredRequests = requests.filter(req =>
        req.asset_name.toLowerCase().includes(query) ||
        req.status.toLowerCase().includes(query)
    );
    displayMemberRequests();
}

/**
 * Opens Return Modal.
 */
function openReturnModal(id) {
    document.getElementById('returnBorrowId').value = id;
    document.getElementById('returnSubmitModal').classList.remove('hidden');
}

/**
 * Helper to update pagination text.
 */
function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    info.textContent = `Showing ${filteredRequests.length} Requests`;
}

/**
 * Handles Return Submission
 */
async function handleReturnSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('returnBorrowId').value;
    const note = document.getElementById('returnConditionNote').value;
    const btn = document.getElementById('submitReturnBtn');

    try {
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Processing...';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('condition_note', note); // Matches backend expectation

        const fileInput = document.getElementById('returnProofImage');
        if (fileInput.files.length > 0) {
            formData.append('return_proof', fileInput.files[0]);
        }

        const response = await fetch('/asset_management/public/api/borrow/submitReturn', {
            method: 'POST',
            body: formData
        });

        const res = await response.json();

        if (res.status === 'success') {
            document.getElementById('returnSubmitModal').classList.add('hidden');
            document.getElementById('returnSubmitForm').reset();
            showToast('Return submitted successfully', 'success');
            loadMemberBorrowRequests(); // Reload list
        } else {
            showToast(res.message || 'Failed to submit return', 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('System error occurred', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Submit Return';
    }
}
