<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
$isAdmin = ($user['role'] === 'org_admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssetInventory - Campus Admin</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#006e7a",
                        "status-green": "#339933",
                        "status-orange": "#CC801A",
                        "background-light": "#f5f8f8",
                        "background-dark": "#0f2123",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden"
    data-is-admin="<?php echo $isAdmin ? 'true' : 'false'; ?>">

    <?php include 'sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto h-screen">
        <!-- Top Navigation Bar -->
        <?php include 'header.php'; ?>

        <div class="p-8 pb-24">
            <!-- Page Heading & Breadcrumbs -->
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
                        <a class="hover:text-primary transition-colors" href="dashboard.php">Admin Hub</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-slate-600">Inventory</span>
                    </nav>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Asset Inventory</h2>
                    <p class="text-slate-500 mt-1 font-medium">Manage assets.</p>
                </div>
                <div class="flex gap-2">
                    <?php if ($isAdmin): ?>
                        <button id="addAssetBtn"
                            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                            <span>Add New Asset</span>
                        </button>
                    <?php endif; ?>
                    <button id="exportCsvBtn"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-lg">download</span>
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>

            <!-- Content Body -->
            <div class="space-y-6">

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="size-12 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Assets</p>
                        <p id="totalCount" class="text-3xl font-black mt-1">0</p>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="size-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">task_alt</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Currently In Use</p>
                        <p id="inUseCount" class="text-3xl font-black mt-1">0</p>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="size-12 rounded-lg bg-orange-500/10 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">build</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Needs Maintenance</p>
                        <p id="maintenanceCount" class="text-3xl font-black mt-1">0</p>
                    </div>
                </div>

                <!-- Table Control Bar -->
                <div
                    class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input id="tableSearchInput" type="text"
                            class="block w-full pl-10 pr-3 py-2 border-slate-200 bg-slate-50 rounded-lg focus:ring-primary focus:border-primary text-sm transition-all"
                            placeholder="Search current view...">
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <select id="filterCategory"
                            class="bg-slate-50 border-slate-200 py-2 pl-3 pr-8 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                            <option value="">All Categories</option>
                        </select>
                        <select id="filterStatus"
                            class="bg-slate-50 border-slate-200 py-2 pl-3 pr-8 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                            <option value="">All Statuses</option>
                            <option value="active">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Master Inventory Registry</h3>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Organizational
                                Material Assets</p>
                        </div>
                        <div class="flex gap-2">
                            <span
                                class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-wider cursor-default">Real-time
                                Feed</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Asset Details</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Category</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Location</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Added</th>
                                    <th
                                        class="text-right py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody id="assetTableBody" class="divide-y divide-slate-100">
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

                <!-- Contextual Tip -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-start gap-4">
                    <div
                        class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-xl">lightbulb</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Pro Tip</h4>
                        <p class="text-sm text-slate-500 leading-relaxed mt-1">You can batch-export selected assets by
                            checking the boxes in the leftmost column. Reports will be generated in CSV format based on
                            your system preferences.</p>
                    </div>
                </div>

            </div>
            <div class="py-6 text-center">
                <?php include 'footer.php'; ?>
            </div>
    </main>

    <!-- Asset Modal -->
    <div id="assetModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 text-center">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight" id="modalTitle">Register New Asset</h3>
                <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Asset Inventory Provisioning
                </p>
            </div>
            <form id="assetForm" class="p-8 space-y-5 overflow-y-auto max-h-[70vh]">
                <input type="hidden" id="assetId">

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Asset Name</label>
                    <input type="text" id="name" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="e.g. MacBook Pro M3">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Category</label>
                    <input type="text" id="category" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="e.g. Electronics">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Stock Quantity</label>
                        <input type="number" id="quantity" required
                            class="w-full bg-slate-100 border-slate-200 text-slate-500 rounded-lg px-4 py-2.5 text-sm cursor-not-allowed"
                            value="1" readonly>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Current Status</label>
                        <select id="assetStatus"
                            class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm">
                            <option value="active">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Storage Location</label>
                    <input type="text" id="location" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="e.g. Room 302">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Condition Details</label>
                    <textarea id="description" rows="3"
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm resize-none"
                        placeholder="e.g. New condition, serial numbers..."></textarea>
                </div>

                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">
                        Save Asset Registry
                    </button>
                    <button type="button" onclick="document.getElementById('assetModal').classList.add('hidden')"
                        class="w-full py-3 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">
                        Abort Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Borrow Modal -->
    <div id="borrowModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center text-center">
                <div class="w-full">
                    <h3 class="text-xl font-bold text-slate-900">Asset Requisition</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Request Item Access</p>
                </div>
            </div>
            <form id="borrowForm" class="p-8 space-y-6">
                <input type="hidden" id="borrowAssetId">
                <div class="p-4 bg-primary/5 rounded-xl text-center border border-primary/10">
                    <span class="block text-[10px] font-bold text-primary uppercase tracking-[0.2em] mb-1">Selected
                        Item</span>
                    <strong id="borrowAssetName" class="text-lg text-slate-900 tracking-tight"></strong>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Quantity</label>
                    <input type="number" id="quantity" required
                        class="w-full bg-slate-100 border-slate-200 text-slate-500 rounded-lg px-4 py-2.5 text-sm cursor-not-allowed"
                        value="1" readonly>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Start Date</label>
                        <input type="date" id="startDate" required
                            class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">End Date</label>
                        <input type="date" id="endDate" required
                            class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                            value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Purpose of Use</label>
                    <textarea id="purpose" rows="3" required
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm"
                        placeholder="Briefly describe why you need this..."></textarea>
                </div>
                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">Submit
                        Borrow Request</button>
                    <button type="button" onclick="document.getElementById('borrowModal').classList.add('hidden')"
                        class="w-full py-3 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">Go
                        Back</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/asset.js?v=<?php echo time(); ?>"></script>

</body>

</html>