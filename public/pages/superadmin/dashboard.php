<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();

if ($user['role'] !== 'super_admin') {
    header("Location: /asset_management/public/pages/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Super Admin Dashboard - System Overview</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden" data-role="super_admin">

    <?php include '../partials/sidebar-superadmin.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto h-screen">
        <!-- Top Navigation Bar (Inline or Include) -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-slate-700">System Control</h2>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full pb-24">
            <!-- Page Heading -->
            <div class="mb-8">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Platform Status</h2>
                <p class="text-slate-500">Manage organizations and system-wide settings.</p>
            </div>

            <!-- Super Admin View: Registered Organizations -->
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Orgs -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                                <span class="material-symbols-outlined">domain</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Total Orgs</h3>
                        </div>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900 tracking-tight" id="totalOrgs">0</span>
                        <p class="text-xs font-bold text-slate-400 mt-1">Registered Organizations</p>
                    </div>
                </div>

                <!-- Active Orgs -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <span class="material-symbols-outlined">verified</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Live Systems</h3>
                        </div>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900 tracking-tight" id="activeOrgs">0</span>
                        <p class="text-xs font-bold text-slate-400 mt-1">Operational Systems</p>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Total Accounts</h3>
                        </div>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900 tracking-tight" id="totalUsers">0</span>
                        <p class="text-xs font-bold text-slate-400 mt-1">Across All Organizations</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity / Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Recent Orgs -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Recent Registrations</h3>
                    <div id="recentOrgsList" class="space-y-2">
                        <!-- Populated by JS -->
                        <div class="text-center py-4 text-slate-400 text-sm">Loading...</div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-50 text-center">
                        <a href="/asset_management/public/pages/superadmin/organizations.php"
                            class="text-sm font-bold text-primary hover:text-primary/80 transition-colors">
                            View All Organizations &rarr;
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Administrative Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="/asset_management/public/pages/superadmin/organizations.php"
                            class="p-4 bg-slate-50 rounded-xl hover:bg-purple-50 hover:text-purple-700 transition-all border border-slate-100 group text-center">
                            <span
                                class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-purple-600 mb-2">add_business</span>
                            <p class="text-xs font-bold uppercase tracking-wider">New Organization</p>
                        </a>
                        <a href="/asset_management/public/pages/superadmin/users.php"
                            class="p-4 bg-slate-50 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition-all border border-slate-100 group text-center">
                            <span
                                class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-blue-600 mb-2">person_add</span>
                            <p class="text-xs font-bold uppercase tracking-wider">User Lookup</p>
                        </a>
                    </div>
                </div>
            </div>

            <script src="/asset_management/public/assets/js/auth.js"></script>
            <script src="/asset_management/public/assets/js/superadmin/dashboard.js"></script>
        </div>

        <div class="py-6 text-center">
            <?php include '../footer.php'; ?>
        </div>
    </main>
</body>

</html>