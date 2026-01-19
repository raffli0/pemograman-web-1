<?php
// Shared sidebar logic
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $user ?? AuthMiddleware::authenticate(); // Ensure user is available
?>

<!-- Sidebar Navigation -->
<aside class="w-64 flex-shrink-0 flex flex-col border-r border-slate-200 bg-white sticky top-0 h-screen">
    <div class="p-6 flex items-center gap-3">
        <div>
            <h1 class="text-primary font-bold text-lg leading-tight">Asset Responsibility System</h1>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
        <?php if ($user['role'] === 'org_admin' || $user['role'] === 'super_admin'): ?>
            <a href="dashboard.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'dashboard.php' ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'dashboard.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
        <?php endif; ?>

        <?php if ($user['role'] !== 'super_admin'): ?>
            <a href="assets.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'assets.php' ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'assets.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">inventory_2</span>
                <span class="text-sm">Inventory</span>
            </a>

            <a href="borrow.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'borrow.php' ? 'bg-primary/10 text-primary font-semibold count-badge-container' : 'text-slate-600 hover:bg-slate-50 relative'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'borrow.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">move_to_inbox</span>
                <span class="text-sm">Requests</span>
                <!-- We can add a dynamic badge here if needed via JS -->
            </a>
        <?php endif; ?>

        <?php if ($user['role'] == 'org_admin'): ?>
            <div class="pt-4 pb-2 px-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Administration</p>
            </div>

            <a href="users.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'users.php' ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'users.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">group</span>
                <span class="text-sm">Members</span>
            </a>

            <a href="returns.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'returns.php' ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'returns.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">fact_check</span>
                <span class="text-sm">Verification</span>
            </a>

            <a href="reports.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $currentPage == 'reports.php' ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo $currentPage == 'reports.php' ? "font-variation-settings: 'FILL' 1" : ''; ?>">description</span>
                <span class="text-sm">Reports</span>
            </a>
        <?php endif; ?>
    </nav>

    <div class="p-4 mt-auto border-t border-slate-100">
        <div class="bg-slate-50 rounded-xl p-4 mb-4">
            <p class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">System Status</p>
            <div class="flex items-center gap-2">
                <div class="size-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-medium text-slate-700">All systems online</span>
            </div>
        </div>

        <button onclick="logout()"
            class="w-full flex items-center justify-center gap-2 text-slate-500 hover:text-red-500 hover:bg-red-50 py-2.5 rounded-lg font-bold text-sm transition-all mb-2">
            <span class="material-symbols-outlined text-sm">logout</span>
            <span>Sign Out</span>
        </button>
    </div>
</aside>
<script src="/ukm/public/assets/js/ui.js?v=<?php echo time(); ?>"></script>