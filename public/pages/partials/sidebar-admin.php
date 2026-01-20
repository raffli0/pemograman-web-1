<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $user ?? AuthMiddleware::authenticate();
?>
<aside class="w-64 flex-shrink-0 flex flex-col border-r border-slate-200 bg-white sticky top-0 h-screen">
    <div class="p-6 flex items-center gap-3">
        <div>
            <h1 class="text-primary font-bold text-lg leading-tight">Asset System</h1>
            <p class="text-xs text-slate-400 font-medium tracking-wider uppercase">Administration</p>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
        <!-- Dashboard -->
        <a href="/ukm/public/pages/admin/dashboard.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">dashboard</span>
            <span class="text-sm">Dashboard</span>
        </a>

        <!-- Inventory -->
        <a href="/ukm/public/pages/admin/assets.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'assets.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'assets.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">inventory_2</span>
            <span class="text-sm">Inventory Management</span>
        </a>

        <!-- Requests -->
        <a href="/ukm/public/pages/admin/borrow-requests.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'borrow-requests.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'borrow-requests.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">swap_horiz</span>
            <span class="text-sm">Borrow Request Review</span>
        </a>


        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Management</p>
        </div>

        <!-- Members -->
        <a href="/ukm/public/pages/admin/users.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">group</span>
            <span class="text-sm">Member Management</span>
        </a>

        <!-- Maintenance -->
        <a href="/ukm/public/pages/admin/maintenance.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'maintenance.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'maintenance.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">build</span>
            <span class="text-sm">Maintenance</span>
        </a>

        <!-- Verification/Returns -->
        <a href="/ukm/public/pages/admin/returns.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'returns.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'returns.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">fact_check</span>
            <span class="text-sm">Return Verification</span>
        </a>

        <!-- Reports -->
        <a href="/ukm/public/pages/admin/reports.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'reports.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'reports.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">description</span>
            <span class="text-sm">Reports</span>
        </a>
    </nav>

    <div class="p-4 mt-auto border-t border-slate-100">
        <div class="bg-slate-50 rounded-xl p-4 mb-4">
            <p class="text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wider">Admin Access</p>
            <p class="text-sm font-bold text-slate-900 truncate">
                <?php echo htmlspecialchars($user['name']); ?>
            </p>
        </div>

        <button onclick="logout()"
            class="w-full flex items-center justify-center gap-2 text-slate-500 hover:text-red-500 hover:bg-red-50 py-2.5 rounded-lg font-bold text-sm transition-all mb-2">
            <span class="material-symbols-outlined text-sm">logout</span>
            <span>Sign Out</span>
        </button>
    </div>
</aside>
<script src="/ukm/public/assets/js/ui.js?v=<?php echo time(); ?>"></script>