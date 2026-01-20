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
    <title>Organization Management - Super Admin</title>
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
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Organizations</h1>
                    <p class="text-slate-500 mt-1 font-medium">Manage universities and organizations.</p>
                </div>
                <button onclick="openOrgModal()"
                    class="px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/25 hover:bg-primary/90 hover:shadow-primary/40 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">add</span>
                    New Organization
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Organization List</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50/50">
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Name</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Owner</th>
                                <th
                                    class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Created</th>
                                <th
                                    class="text-right py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orgTableBody">
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400">Loading organizations...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="orgModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-300"
            id="orgModalContent">
            <div
                class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-slate-800" id="modalTitle">New Organization</h3>
                <button onclick="closeOrgModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="orgForm" class="p-8 space-y-5">
                <input type="hidden" id="orgId">
                <div>
                    <label for="orgName"
                        class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Organization
                        Name</label>
                    <input type="text" id="orgName"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        required>
                </div>

                <div id="adminFields" class="space-y-5 border-t border-slate-100 pt-5">
                    <h4 class="text-sm font-bold text-slate-800">Administrator Account</h4>
                    <div>
                        <label for="adminName"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Admin
                            Name</label>
                        <input type="text" id="adminName"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label for="adminEmail"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Admin
                            Email</label>
                        <input type="email" id="adminEmail"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                    <div>
                        <label for="adminPassword"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" id="adminPassword"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" onclick="closeOrgModal()"
                        class="flex-1 px-6 py-3 text-slate-600 font-bold hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/25 hover:bg-primary/90 transition-all">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="/ukm/public/assets/js/auth.js?v=<?php echo time(); ?>"></script>
    <script src="/ukm/public/assets/js/superadmin/organizations.js?v=<?php echo time(); ?>"></script>
</body>

</html>