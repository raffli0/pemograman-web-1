/**
 * borrow.js
 * Handles Borrow Requests with Dashboard UI matching
 */

let requests = [];
let filteredRequests = [];

document.addEventListener('DOMContentLoaded', function () {
    // Setup event listeners
    document.getElementById('searchInput')?.addEventListener('input', filterRequests);
    document.getElementById('tableFilter')?.addEventListener('input', filterRequests);
    document.getElementById('returnSubmitForm')?.addEventListener('submit', handleReturnSubmit);
});

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

        // Status badge logic
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

        let actions = '';
        if (isAdmin && req.status === 'pending') {
            actions = `
                <div class="flex items-center justify-end gap-2">
                    <button onclick="updateReqStatus(${req.id}, 'approve')" class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-600 transition-colors shadow-sm">Approve</button>
                    <button onclick="updateReqStatus(${req.id}, 'reject')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-slate-50 transition-colors">Reject</button>
                </div>
            `;
        } else if (isAdmin) {
            actions = `
                <div class="flex items-center justify-end text-slate-300">
                    <span class="material-symbols-outlined text-lg">verified</span>
                </div>
            `;
        } else if (!isAdmin && req.status === 'approved') {
            actions = `
                <div class="flex items-center justify-end">
                    <button onclick="openReturnModal(${req.id})" class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-primary hover:text-white transition-all shadow-sm">Submit Return</button>
                </div>
            `;
        } else if (!isAdmin && req.status === 'returning') {
            actions = `
                <div class="flex items-center justify-end text-primary opacity-50">
                    <span class="material-symbols-outlined text-lg animate-pulse">history</span>
                </div>
            `;
        }

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

async function updateReqStatus(id, action) {
    try {
        const res = await fetchAPI(`/borrow/${action}`, 'POST', { id });
        if (res.status === 'success') {
            loadRequests(isAdmin);
        }
    } catch (err) {
        alert(err.message);
    }
}

function openReturnModal(id) {
    document.getElementById('returnBorrowId').value = id;
    document.getElementById('returnSubmitModal').classList.remove('hidden');
}

async function handleReturnSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('returnBorrowId').value;
    const note = document.getElementById('returnConditionNote').value;
    const btn = document.getElementById('submitReturnBtn');

    try {
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Processing...';

        const res = await fetchAPI('/borrow/submitReturn', 'POST', { id, condition_note: note });
        if (res.status === 'success') {
            document.getElementById('returnSubmitModal').classList.add('hidden');
            document.getElementById('returnSubmitForm').reset();
            loadRequests(isAdmin);
        }
    } catch (err) {
        alert(err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Submit Return';
    }
}

function updatePagination() {
    const info = document.getElementById('paginationInfo');
    if (!info) return;
    const count = filteredRequests.length;
    info.textContent = `Showing ${count > 0 ? '1 to ' + count : '0'} of ${count} entries`;
}

