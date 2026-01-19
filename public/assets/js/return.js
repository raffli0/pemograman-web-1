/**
 * return.js
 * Handles Asset Returns with Dashboard UI matching
 */

let returnRequests = [];
let filteredReturns = [];

document.addEventListener('DOMContentLoaded', function () {
    // Setup event listeners
    document.getElementById('searchInput')?.addEventListener('input', filterReturns);
    document.getElementById('tableFilter')?.addEventListener('input', filterReturns);
    document.getElementById('returnForm')?.addEventListener('submit', handleReturnSubmit);
});

async function loadReturns() {
    const tbody = document.getElementById('returnsTableBody');
    if (!tbody) return;

    try {
        // Reuse borrow index but filter for items submitted for return
        const res = await fetchAPI('/borrow/index');
        if (res.status === 'success') {
            returnRequests = res.data.filter(r => r.status === 'returning');
            filteredReturns = returnRequests;
            displayReturns();
        }
    } catch (err) {
        console.error('Error loading returns:', err);
    }
}

function displayReturns() {
    const tbody = document.getElementById('returnsTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (filteredReturns.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12 text-slate-400 font-bold uppercase tracking-widest text-[10px]">No returns pending verification</td></tr>`;
        return;
    }

    filteredReturns.forEach(req => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50 transition-colors group';

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
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Asset Managed Registry</span>
                </div>
            </td>
            <td class="py-4 px-4">
                <span class="text-xs font-bold text-rose-600 flex items-center gap-1.5 bg-rose-50 px-2.5 py-1 rounded-full w-fit">
                    <span class="material-symbols-outlined text-sm">event_busy</span>
                    Due: ${req.end_date}
                </span>
            </td>
            <td class="py-4 px-8 text-right">
                <button onclick="openReturnModal(${req.id})" class="px-4 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-600 hover:shadow-md transition-all">
                    Verify & Restock
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    });

    updatePagination();
}

function filterReturns() {
    const headerQuery = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const filterQuery = document.getElementById('tableFilter')?.value.toLowerCase() || '';
    const query = headerQuery || filterQuery;

    filteredReturns = returnRequests.filter(req =>
        req.user_name.toLowerCase().includes(query) ||
        req.asset_name.toLowerCase().includes(query)
    );

    displayReturns();
}

function openReturnModal(id) {
    const modalIdField = document.getElementById('returnRequestId');
    if (modalIdField) modalIdField.value = id;
    document.getElementById('returnModal')?.classList.remove('hidden');
}

async function handleReturnSubmit(e) {
    e.preventDefault();
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    const data = {
        id: document.getElementById('returnRequestId').value,
        condition_note: document.getElementById('conditionNote').value
    };

    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Verifying...';

        const res = await fetchAPI('/borrow/verifyReturn', 'POST', data);
        if (res.status === 'success') {
            document.getElementById('returnModal').classList.add('hidden');
            document.getElementById('returnForm').reset();
            loadReturns();
        }
    } catch (err) {
        alert(err.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

function updatePagination() {
    const el = document.getElementById('paginationInfo');
    if (el) {
        const count = filteredReturns.length;
        el.textContent = `Monitoring ${count} items pending final verification`;
    }
}

