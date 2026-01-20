/**
 * public/assets/js/superadmin/dashboard.js
 * Handles stats loading for the Superadmin Dashboard.
 */

document.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
});

async function loadDashboardStats() {
    try {
        const res = await fetchAPI('/dashboard/getSuperAdminStats');
        if (res.status === 'success') {
            const data = res.data;
            animateCounter('totalOrgs', data.total_orgs);
            animateCounter('activeOrgs', data.active_orgs);
            animateCounter('totalUsers', data.total_users);

            renderRecentOrgs(data.recent_orgs);
        }
    } catch (err) {
        console.error('Error loading stats:', err);
    }
}

function renderRecentOrgs(orgs) {
    const list = document.getElementById('recentOrgsList');
    if (!list) return;
    list.innerHTML = '';

    if (orgs.length === 0) {
        list.className = "text-center py-4 text-slate-400 text-sm";
        list.textContent = "No recent activity";
        return;
    }

    orgs.forEach(org => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0';

        const date = new Date(org.created_at).toLocaleDateString();
        const statusColor = org.status === 'active' ? 'bg-emerald-400' : 'bg-red-400';

        div.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="size-2 rounded-full ${statusColor}"></div>
                <div>
                    <h4 class="text-sm font-bold text-slate-700">${org.name}</h4>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">${date}</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-500">${org.status === 'active' ? 'Active' : 'Suspended'}</span>
        `;
        list.appendChild(div);
    });
}

function animateCounter(id, target) {
    const el = document.getElementById(id);
    if (!el) return;

    let current = 0;
    const step = Math.ceil(target / 20) || 1;
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            el.textContent = target;
            clearInterval(timer);
        } else {
            el.textContent = current;
        }
    }, 30);
}
