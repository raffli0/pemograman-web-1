/**
 * borrow.js
 * Handles Borrow Requests
 */

const borrowModalEl = document.getElementById('borrowModal');
const bsBorrowModal = borrowModalEl ? new bootstrap.Modal(borrowModalEl) : null;
const borrowForm = document.getElementById('borrowForm');

function initBorrow(id, name) {
    document.getElementById('borrowAssetId').value = id;
    document.getElementById('borrowAssetName').textContent = name;
    bsBorrowModal.show();
}

if (borrowForm) {
    borrowForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            asset_id: document.getElementById('borrowAssetId').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value
        };
        try {
            await fetchAPI('/borrow/request', 'POST', data);
            bsBorrowModal.hide();
            alert('Request submitted.');
            // Ideally refresh the list if on borrow page
        } catch (err) { alert(err.message); }
    });
}

async function loadRequests(isAdmin) {
    const tbody = document.getElementById('borrowTableBody');
    if (!tbody) return;

    try {
        const res = await fetchAPI('/borrow/index');
        tbody.innerHTML = '';
        if (res.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small py-3">No records found.</td></tr>';
            return;
        }
        res.data.forEach(req => {
            let actions = '';
            if (isAdmin && req.status == 'pending') {
                actions = `
                    <button class="btn btn-sm btn-success py-0 px-2 small" onclick="updateReqStatus(${req.id}, 'approve')">Approve</button>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 small" onclick="updateReqStatus(${req.id}, 'reject')">Reject</button>
                `;
            } else if (isAdmin) {
                actions = `<span class="text-muted small">-</span>`;
            }

            let badgeClass = 'bg-secondary';
            if (req.status === 'pending') badgeClass = 'bg-warning text-dark';
            if (req.status === 'approved') badgeClass = 'bg-success';
            if (req.status === 'rejected') badgeClass = 'bg-danger';
            if (req.status === 'returned') badgeClass = 'bg-info text-dark';

            const badge = `<span class="badge ${badgeClass} fw-normal" style="font-size:0.7rem;">${req.status.toUpperCase()}</span>`;

            tbody.innerHTML += `
                <tr>
                    <td>${req.user_name}</td>
                    <td>${req.asset_name}</td>
                    <td class="small text-muted">${req.start_date} / ${req.end_date}</td>
                    <td>${badge}</td>
                    ${isAdmin ? `<td class="text-end">${actions}</td>` : ''}
                </tr>
            `;
        });
    } catch (err) { console.error(err); }
}

async function updateReqStatus(id, action) {
    if (!confirm(`${action} request?`)) return;
    try {
        await fetchAPI(`/borrow/${action}`, 'POST', { id });
        loadRequests(true);
    } catch (err) { alert(err.message); }
}
