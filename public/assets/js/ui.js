/**
 * ui.js
 * Global UI helpers for toasts and modals
 */

// Create toast container if it doesn't exist
const toastContainerId = 'global-toast-container';
if (!document.getElementById(toastContainerId)) {
    const container = document.createElement('div');
    container.id = toastContainerId;
    container.className = 'fixed bottom-5 right-5 z-[100] flex flex-col gap-2';
    document.body.appendChild(container); // Append to body to ensure it's always available
}

/**
 * Show a toast notification
 * @param {string} message 
 * @param {'success'|'error'|'info'} type 
 */
function showToast(message, type = 'info') {
    const container = document.getElementById(toastContainerId);
    if (!container) return;

    const toast = document.createElement('div');

    // Colors based on type
    let colorClass = 'bg-slate-800 text-white';
    let icon = 'info';

    if (type === 'success') {
        colorClass = 'bg-emerald-500 text-white';
        icon = 'check_circle';
    } else if (type === 'error') {
        colorClass = 'bg-rose-500 text-white';
        icon = 'error';
    } else if (type === 'warning') {
        colorClass = 'bg-amber-500 text-white';
        icon = 'warning';
    }

    toast.className = `min-w-[300px] max-w-sm px-4 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-10 opacity-0 flex items-center gap-3 ${colorClass}`;

    toast.innerHTML = `
        <span class="material-symbols-outlined text-lg">${icon}</span>
        <span class="text-xs font-bold uppercase tracking-wide flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    `;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Override default alert if desired, but better to call explicitly
// window.alert = (msg) => showToast(msg, 'info'); 
