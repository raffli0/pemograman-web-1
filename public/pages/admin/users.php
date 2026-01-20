<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../../../app/core/RoleMiddleware.php';
$user = AuthMiddleware::authenticate();
RoleMiddleware::authorize(['org_admin']);
$isAdmin = ($user['role'] === 'org_admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - Admin</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Standardized Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden">

    <?php include '../partials/sidebar-admin.php'; ?>

    <main class="flex-1 overflow-y-auto h-screen flex flex-col">
        <!-- Sticky Header -->
        <?php include '../header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto w-full">
            <!-- Page Heading -->
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <nav
                        class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                        <a href="/asset_management/public/pages/admin/dashboard.php"
                            class="hover:text-primary transition-colors">Admin</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-slate-900">Members</span>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Member Management</h1>
                    <p class="text-slate-500 mt-1 font-medium">Manage organization members and access.</p>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="flex gap-3">
                        <button onclick="document.getElementById('userModal').classList.remove('hidden')"
                            class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-lg">person_add</span>
                            <span>Add Member</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-primary">group</span>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Total Members</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="totalMembers">--</h3>
                    <div class="mt-4 flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-full uppercase tracking-wider">Active
                            Directory</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-primary">admin_panel_settings</span>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Administrators</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="adminCount">--</h3>
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 font-medium">
                        System level access
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-primary">history</span>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Recently Joined</p>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight" id="recentJoins">--</h3>
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 font-medium">
                        Last 30 days enrollment
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Member Directory</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Registered Users</p>
                    </div>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">filter_list</span>
                        <input type="text" id="tableSearchInput" placeholder="Filter by name..."
                            class="pl-9 pr-4 py-1.5 bg-slate-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Email Address</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Joining Date</th>
                                <th
                                    class="text-right py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-slate-50">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                    <p id="paginationInfo" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Showing 0 entries</p>
                    <div id="paginationButtons" class="flex gap-2">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6 text-center">
            <?php include 'footer.php'; ?>
        </div>
    </main>

    <!-- User Modal -->
    <div id="userModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 text-center">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Enroll New Member</h3>
                <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Staff Access Provisioning
                </p>
            </div>
            <form id="userForm" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Full Name</label>
                    <input type="text" id="userName" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="e.g. Travis Baker">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Email
                        Address</label>
                    <input type="email" id="userEmail" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="travis@example.com">
                </div>
                <div class="space-y-2 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Password</label>
                    <input type="password" id="userPassword" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="******">
                </div>
                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">Grant
                        System Access</button>
                    <button type="button" onclick="document.getElementById('userModal').classList.add('hidden')"
                        class="w-full py-3 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">Abort
                        Enrollment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/asset_management/public/assets/js/auth.js"></script>
    <script src="/asset_management/public/assets/js/admin/users.js?v=<?php echo time(); ?>"></script>

</body>

</html>