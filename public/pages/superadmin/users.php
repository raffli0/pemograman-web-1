<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../../../app/core/RoleMiddleware.php';
$user = AuthMiddleware::authenticate();
RoleMiddleware::authorize(['super_admin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global User Registry - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#7e22ce", "background-light": "#f5f8f8" }, // Purple for SuperAdmin
                    fontFamily: { "display": ["Public Sans", "sans-serif"] },
                },
            },
        }
    </script>
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden">
    <?php include '../partials/sidebar-superadmin.php'; ?>
    <main class="flex-1 overflow-y-auto h-full flex flex-col">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-slate-700">System Management</h2>
            </div>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full">
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">User Registry</h1>
                <p class="text-slate-500 mt-1 font-medium">Manage all users across the system.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Global User Registry</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">All System Accounts
                        </p>
                    </div>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                        <input type="text" id="tableSearchInput" placeholder="Search users or orgs..."
                            class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary/20 w-64 transition-all">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50/50">
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    User Details</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Organization</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Role</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Joined</th>
                                <th
                                    class="text-right py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Manage User Modal -->
    <div id="manageUserModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform scale-95 transition-transform duration-300"
            id="manageUserModalContent">
            <div
                class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-slate-800">Manage Account</h3>
                <button onclick="closeManageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="manageUserForm" class="p-8 space-y-5">
                <input type="hidden" id="manageUserId">

                <div class="space-y-4">
                    <div>
                        <label for="manageName"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Member
                            Name</label>
                        <input type="text" id="manageName"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            required>
                    </div>

                    <div>
                        <label for="manageEmail"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Email
                            Address</label>
                        <input type="email" id="manageEmail"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            required>
                    </div>

                    <div>
                        <label for="manageRole"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">System
                            Role</label>
                        <select id="manageRole"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            <option value="member">Member</option>
                            <option value="org_admin">Organization Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div>
                        <label for="managePassword"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">New
                            Password <span class="text-slate-300 font-normal normal-case">(Optional - Leave blank to
                                keep current)</span></label>
                        <input type="password" id="managePassword" placeholder="••••••••"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" onclick="deleteUser()"
                        class="px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        Delete
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" onclick="closeManageModal()"
                        class="px-6 py-3 text-slate-600 font-bold hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/25 hover:bg-primary/90 hover:shadow-primary/40 transform hover:-translate-y-0.5 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="/ukm/public/assets/js/auth.js"></script>
    <script src="/ukm/public/assets/js/superadmin/users.js"></script>
</body>

</html>