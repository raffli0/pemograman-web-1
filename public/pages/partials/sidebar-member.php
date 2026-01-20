<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $user ?? AuthMiddleware::authenticate();
?>
<aside class="w-64 flex-shrink-0 flex flex-col border-r border-slate-200 bg-white sticky top-0 h-screen">
    <div class="p-6 flex items-center gap-3">
        <div>
            <h1 class="text-primary font-bold text-lg leading-tight">Asset System</h1>
            <p class="text-xs text-slate-400 font-medium">Member Portal</p>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
        <!-- Asset Catalog -->
        <a href="/ukm/public/pages/member/assets.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'assets.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'assets.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">inventory_2</span>
            <span class="text-sm">Asset Catalog</span>
        </a>

        <!-- My Borrowing Requests -->
        <a href="/ukm/public/pages/member/borrow-requests.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'borrow-requests.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'borrow-requests.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">swap_horiz</span>
            <span class="text-sm">My Borrowing Requests</span>
        </a>

        <!-- My History -->
        <a href="/ukm/public/pages/member/history.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo strpos($_SERVER['PHP_SELF'], 'history.php') !== false ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50'; ?>">
            <span class="material-symbols-outlined"
                style="<?php echo strpos($_SERVER['PHP_SELF'], 'history.php') !== false ? "font-variation-settings: 'FILL' 1" : ''; ?>">history</span>
            <span class="text-sm">My History</span>
        </a>

    </nav>

    <div class="p-4 mt-auto border-t border-slate-100">
        <div class="bg-slate-50 rounded-xl p-4 mb-4">
            <p class="text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wider">Logged in as</p>
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