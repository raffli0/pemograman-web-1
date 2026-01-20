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
            <a href="/ukm/public/pages/admin/dashboard.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/dashboard.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/dashboard.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>

            <!-- Admin Inventory Link -->
            <a href="/ukm/public/pages/admin/assets.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/assets.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/assets.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">inventory_2</span>
                <span class="text-sm">Inventory</span>
            </a>
        <?php else: ?>
            <!-- Member Catalog Link -->
            <a href="/ukm/public/pages/member/assets.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/member/assets.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/member/assets.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">inventory_2</span>
                <span class="text-sm">Asset Catalog</span>
            </a>
        <?php endif; ?>

        <?php if ($user['role'] === 'super_admin' || $user['role'] === 'org_admin'): ?>
            <a href="/ukm/public/pages/admin/borrow.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/borrow.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/borrow.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">swap_horiz</span>
                <span class="text-sm">Requests</span>
            </a>
        <?php else: ?>
            <a href="/ukm/public/pages/member/borrow-requests.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/member/borrow-requests.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/member/borrow-requests.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">swap_horiz</span>
                <span class="text-sm">My Borrowing Requests</span>
            </a>
        <?php endif; ?>

        <?php if ($user['role'] == 'org_admin'): ?>
            <div class="pt-4 pb-2 px-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Administration</p>
            </div>

            <a href="/ukm/public/pages/admin/users.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/users.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/users.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">group</span>
                <span class="text-sm">Members</span>
            </a>

            <a href="/ukm/public/pages/admin/returns.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/returns.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/returns.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">fact_check</span>
                <span class="text-sm">Verification</span>
            </a>

            <a href="/ukm/public/pages/admin/reports.php"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <span class="material-symbols-outlined"
                    style="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">description</span>
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