<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $user ?? AuthMiddleware::authenticate();
?>
<aside class="w-64 flex-shrink-0 flex flex-col border-r border-slate-200 bg-white sticky top-0 h-screen">
    <div class="p-6 flex items-center gap-3">
        <div>
            <h1 class="text-primary font-bold text-lg leading-tight">Asset System</h1>
            <p
                class="text-xs text-purple-600 font-bold bg-purple-50 px-2 py-0.5 rounded uppercase tracking-wider inline-block mt-1">
                Super Admin</p>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
        <!-- Dashboard -->
        <a href="/ukm/public/pages/admin/dashboard.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">dashboard</span>
            <span class="text-sm">System Overview</span>
        </a>

        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">System Config</p>
        </div>

        <!-- Organizations -->
        <a href="/ukm/public/pages/superadmin/organizations.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'organizations.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'organizations.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">domain</span>
            <span class="text-sm">Organizations</span>
        </a>

        <!-- Users -->
        <a href="/ukm/public/pages/superadmin/users.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">manage_accounts</span>
            <span class="text-sm">User Registry</span>
        </a>

        <!-- Roles -->
        <a href="/ukm/public/pages/superadmin/roles.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'roles.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'roles.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">badge</span>
            <span class="text-sm">Role Control</span>
        </a>
    </nav>

    <div class="p-4 mt-auto border-t border-slate-100">
        <div class="bg-slate-50 rounded-xl p-4 mb-4">
            <p class="text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wider">Root Access</p>
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