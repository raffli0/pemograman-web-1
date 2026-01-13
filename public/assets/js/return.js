/**
 * return.js
 * Handles Asset Returns
 */

const returnModalEl = document.getElementById('returnModal');
const bsReturnModal = returnModalEl ? new bootstrap.Modal(returnModalEl) : null;
const returnForm = document.getElementById('returnForm');

async function loadReturns() {
    const tbody = document.getElementById('returnsTableBody');
    if (!tbody) return;

    try {
        // We reuse the borrow index logic but filter client side for approved items
        const res = await fetchAPI('/borrow/index');
        tbody.innerHTML = '';
        const approved = res.data.filter(r => r.status === 'approved');

        if (approved.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted small py-3">No pending returns.</td></tr>';
            return;
        }

        approved.forEach(req => {
            tbody.innerHTML += `
                <tr>
                    <td>${req.user_name}</td>
                    <td>${req.asset_name}</td>
                    <td class="small text-danger">${req.end_date}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary py-0 px-2 small" onclick="openReturnModal(${req.id})">Restock</button>
                    </td>
                </tr>
            `;
        });
    } catch (err) { console.error(err); }
}

function openReturnModal(id) {
    document.getElementById('returnRequestId').value = id;
    if (bsReturnModal) bsReturnModal.show();
}

if (returnForm) {
    returnForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            id: document.getElementById('returnRequestId').value,
            condition_note: document.getElementById('conditionNote').value
        };

        try {
            await fetchAPI('/borrow/returnAsset', 'POST', data);
            bsReturnModal.hide();
            loadReturns();
            alert('Asset Returned');
        } catch (err) { alert(err.message); }
    });
}
