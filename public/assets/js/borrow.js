/**
 * borrow.js
 * Manages the Borrow Requests interface for both Admins and Members.
 * Handles request listing, approval workflows, return submissions, and admin verification.
 */

// --- State Management ---
let requests = [];          // All borrow requests loaded from API
let filteredRequests = [];  // Requests currently displayed after filtering

/**
 * Initialization
 * Sets up global event listeners and initial data load.
 */
document.addEventListener('DOMContentLoaded', function () {
    // --- Event Listeners ---
    document.getElementById('searchInput')?.addEventListener('input', filterRequests);
    document.getElementById('tableFilter')?.addEventListener('input', filterRequests);

    // Return Submission Form (Member Action)
    document.getElementById('returnSubmitForm')?.addEventListener('submit', handleReturnSubmit);
});

/**
 * Fetches borrow requests from the API.
 * @param {boolean} isAdmin - Flag to determine which UI elements to render (handled in display)
 */
async function loadRequests(isAdmin) {
    const tbody = document.getElementById('borrowTableBody');
    if (!tbody) return;

    try {
        const res = await fetchAPI('/borrow/index');
        if (res.status === 'success') {
            requests = res.data;
            filteredRequests = requests;
            displayRequests(isAdmin);
        }
    } catch (err) {
        console.error('Error loading requests:', err);
    }
}

/**
 * Renders the list of borrow requests into the table.
 * Contains logic for Status Badges and Action Buttons based on User Role.
 * @param {boolean} isAdmin - Controls visibility of Admin actions (Approve, Reject, Verify)
 */
function displayRequests(isAdmin) {
    const tbody = document.getElementById('borrowTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (filteredRequests.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-slate-400 font-bold uppercase tracking-widest text-[10px]">No requisition records found</td></tr>`;
        return;
    }

    filteredRequests.forEach(req => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors group';

        // --- Status Badge Styling ---
        let statusBadge = '';
        let badgeStyle = '';
        switch (req.status) {
            case 'pending':
                badgeStyle = 'bg-amber-50 text-amber-700';
                statusBadge = 'Pending Review';
                break;
            case 'approved':
                badgeStyle = 'bg-emerald-50 text-emerald-700';
                statusBadge = 'Approved & In Use';
                break;
            case 'returning':
                badgeStyle = 'bg-primary/10 text-primary';
                statusBadge = 'Verification Pending';
                break;
            case 'rejected':
                badgeStyle = 'bg-rose-50 text-rose-700';
                statusBadge = 'Rejected';
                break;
            case 'returned':
                badgeStyle = 'bg-slate-100 text-slate-400';
                statusBadge = 'Archived & Returned';
                break;
            default:
                badgeStyle = 'bg-slate-100 text-slate-500';
                statusBadge = req.status.toUpperCase();
        }

        // --- Action Buttons Logic ---
        let actions = '';
        if (isAdmin && req.status === 'pending') {
            // Admin: Approve / Reject
            actions = `
                <div class="flex items-center justify-end gap-2">
                    <button onclick="updateReqStatus(${req.id}, 'approve')" class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-600 transition-colors shadow-sm">Approve</button>
                    <button onclick="updateReqStatus(${req.id}, 'reject')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-slate-50 transition-colors">Reject</button>
                </div>
            `;
        } else if (isAdmin) {
            // Admin: Visual Indicator for non-pending
            actions = `
                <div class="flex items-center justify-end text-slate-300">
                    <span class="material-symbols-outlined text-lg">verified</span>
                </div>
            `;
        } else if (!isAdmin && req.status === 'approved') {
            // Member: Submit Return
            actions = `
                <div class="flex items-center justify-end">
                    <button onclick="openReturnModal(${req.id})" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-primary hover:text-white transition-all shadow-sm">Submit Return</button>
                </div>
            `;
        } else if (isAdmin && req.status === 'returning') {
            // Admin: Inspect Return (Alternative entry point)
            actions = `
                <div class="flex items-center justify-end gap-2">
                    <button onclick='openVerifyModal(${JSON.stringify(req).replace(/'/g, "&apos;")})' class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-primary hover:text-white transition-all shadow-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">visibility</span> Inspect
                    </button>
                </div>
            `;
        }

        // --- Table Row Content ---
        tr.innerHTML = `
            <td class="py-4 px-8">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-500 border border-slate-200 group-hover:bg-primary group-hover:text-white transition-all">
                        ${req.user_name?.charAt(0).toUpperCase() || '?'}
                    </div>
                    <span class="text-sm font-bold text-slate-900">${req.user_name}</span>
                </div>
            </td>
            <td class="py-4 px-4">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-slate-700">${req.asset_name}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Quantity: ${req.quantity || '1'}</span>
                </div>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    ${req.start_date} - ${req.end_date}
                </span>
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
 * Opens the Admin Verification Modal.
 * Populates it with return details (notes, images) for inspection.
 * @param {Object} req - The request object
 */
function openVerifyModal(req) {
    document.getElementById('verifyRequestId').value = req.id;
    document.getElementById('verifyBorrowerName').textContent = req.user_name || '-';
    document.getElementById('verifyAssetName').textContent = req.asset_name || '-';
    document.getElementById('verifyDuration').textContent = `${req.start_date} -> ${req.end_date}`;

    // Populate User Note
    const noteEl = document.getElementById('verifyUserNote');
    noteEl.textContent = req.return_note ? `"${req.return_note}"` : "No remarks provided.";

    // Populate Proof Image
    const proofCont = document.getElementById('verifyProofContainer');
    if (req.return_proof_image) {
        proofCont.innerHTML = `<img src="/ukm/public/${req.return_proof_image}" class="w-full h-auto object-cover" alt="Proof">`;
    } else {
        proofCont.innerHTML = `<span class="text-xs text-slate-400 font-medium">No image uploaded</span>`;
    }

    document.getElementById('verifyReturnModal').classList.remove('hidden');
}

/**
 * Submits the Admin's verification decision (Finalize Return).
 */
async function submitVerification() {
    const id = document.getElementById('verifyRequestId').value;
    const adminNote = document.getElementById('verifyAdminNote').value;
    const btn = document.getElementById('btnVerifyParams');

    try {
        btn.disabled = true;
        btn.innerHTML = 'Verifying...';

        const res = await fetchAPI('/borrow/verifyReturn', 'POST', { id, admin_note: adminNote });
        if (res.status === 'success') {
            document.getElementById('verifyReturnModal').classList.add('hidden');
            loadRequests(true); // reload as admin
        }
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Verify & Restock';
    }
}

/**
 * Filters the request list based on search input (User Name, Asset Name, Status).
 */
function filterRequests() {
    const headerQuery = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const filterQuery = document.getElementById('tableFilter')?.value.toLowerCase() || '';
    const query = headerQuery || filterQuery;

    filteredRequests = requests.filter(req =>
        req.user_name.toLowerCase().includes(query) ||
        req.asset_name.toLowerCase().includes(query) ||
        req.status.toLowerCase().includes(query)
    );

    const isAdmin = typeof window.isAdmin !== 'undefined' ? window.isAdmin : true;
    displayRequests(isAdmin);
}

/**
 * Updates the status of a request (Approved/Rejected) by Admin.
 */
async function updateReqStatus(id, action) {
    try {
        const res = await fetchAPI(`/borrow/${action}`, 'POST', { id });
        if (res.status === 'success') {
            loadRequests(isAdmin);
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

/**
 * Opens the Return Submission modal for members.
 * @param {number} id - Request ID
 */
function openReturnModal(id) {
    document.getElementById('returnBorrowId').value = id;
    document.getElementById('returnSubmitModal').classList.remove('hidden');
}

/**
 * Handles the member's return submission (Image upload + Condition note).
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
        formData.append('condition_note', note);

        const fileInput = document.getElementById('returnProofImage');
        if (fileInput.files.length > 0) {
            formData.append('return_proof', fileInput.files[0]);
        }

        // Use fetch directly for FormData to avoid Content-Type issues with Auth wrapper
        const response = await fetch('/ukm/public/api/borrow/submitReturn', {
            method: 'POST',
            body: formData
        });

        const res = await response.json();

        if (res.status === 'success') {
            document.getElementById('returnSubmitModal').classList.add('hidden');
            document.getElementById('returnSubmitForm').reset();
            loadRequests(typeof window.isAdmin !== 'undefined' ? window.isAdmin : false);
        } else {
            showToast(res.message, 'error');
        }
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Submit Return';
    }
}

/**
 * Updates pagination text.
 */
function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    const count = filteredRequests.length;
    info.textContent = `Showing ${count > 0 ? '1 to ' + count : '0'} of ${count} entries`;
}


