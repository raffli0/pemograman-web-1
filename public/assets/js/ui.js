/**
 * ui.js
 * Global UI Helper Library.
 * Provides a standardized Toast Notification system and other shared UI utilities.
 * Included globally via sidebar.php.
 */

// --- Initialization ---
// Auto-creates the toast container in the DOM if it doesn't exist
const toastContainerId = 'global-toast-container';
if (!document.getElementById(toastContainerId)) {
    const container = document.createElement('div');
    container.id = toastContainerId;
    container.className = 'fixed bottom-5 right-5 z-[100] flex flex-col gap-2'; // Fixed position bottom-right
    document.body.appendChild(container); // Append to body to ensure it's always top-level
}

/**
 * Displays a non-blocking toast notification.
 * 
 * @param {string} message - The text to display
 * @param {'success'|'error'|'info'|'warning'} type - Controls color and icon
 */
function showToast(message, type = 'info') {
    const container = document.getElementById(toastContainerId);
    if (!container) return;

    const toast = document.createElement('div');

    // --- Styling Logic ---
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

    // Base Tailwind classes for animation and layout
    toast.className = `min-w-[300px] max-w-sm px-4 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-10 opacity-0 flex items-center gap-3 ${colorClass}`;

    toast.innerHTML = `
        <span class="material-symbols-outlined text-lg">${icon}</span>
        <span class="text-xs font-bold uppercase tracking-wide flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    `;

    container.appendChild(toast);

    // --- Animation Entry ---
    // Use requestAnimationFrame to ensure DOM paint before removing translate/opacity classes
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    });

    // --- Auto Removal ---
    setTimeout(() => {
        // Exit animation
        toast.classList.add('translate-y-10', 'opacity-0');
        // Remove from DOM after animation completes (300ms)
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Optional: Override native alert (Use with caution)
// window.alert = (msg) => showToast(msg, 'info'); 
