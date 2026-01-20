<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();

// AUTHORIZATION CHECK (Opposite of Admin)
// Strictly speaking, admins *could* view this page, but the design requirement says:
// "Member is a consumer". Admins might want to test it.
// However, the Sidebar logic will route them to Admin view.
// We won't block Admins, but we ensure the View is purely Consumer-oriented.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Catalog - Browse & Borrow</title>

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
    </style>
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden" data-role="member">

    <?php include '../partials/sidebar-member.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto h-screen">
        <!-- Top Navigation Bar -->
        <?php include '../header.php'; ?>

        <div class="p-8 pb-24">
            <!-- Page Heading & Breadcrumbs -->
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <!-- MEMBER VIEW IDENTITY -->
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Asset Catalog</h2>
                    <p class="text-slate-500 mt-1 font-medium">Browse and borrow available organizational assets</p>
                </div>
                <div class="flex gap-2">
                    <!-- No Admin Buttons for Members -->
                </div>
            </div>

            <!-- Content Body -->
            <div class="space-y-6">

                <!-- Member Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="size-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">check_circle</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Items Ready to Borrow</p>
                        <p id="memberAvailableCount" class="text-3xl font-black mt-1 text-emerald-600">-</p>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="size-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">shopping_bag</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">My Active Borrows</p>
                        <p id="memberActiveBorrows" class="text-3xl font-black mt-1">-</p>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="size-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">schedule</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">My Pending Requests</p>
                        <p id="memberPendingRequests" class="text-3xl font-black mt-1">-</p>
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
                            placeholder="Find items...">
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <select id="filterCategory"
                            class="bg-slate-50 border-slate-200 py-2 pl-3 pr-8 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                            <option value="">All Categories</option>
                        </select>
                        <select id="filterStatus"
                            class="bg-slate-50 border-slate-200 py-2 pl-3 pr-8 rounded-lg text-sm font-medium focus:ring-primary focus:border-primary cursor-pointer">
                            <option value="">Availability</option>
                            <option value="active">Ready to Borrow</option>
                            <option value="in_use">Currently Borrowed</option>
                        </select>
                    </div>
                </div>

                <!-- Member Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Catalog Items</h3>
                            <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Browse items
                                available for borrowing</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Item Name</th>
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
                                        Units Available</th>
                                    <th
                                        class="text-left py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Last Updated</th>
                                    <th
                                        class="text-right py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody id="assetTableBody" class="divide-y divide-slate-100">
                                <!-- Populated by member_asset.js -->
                            </tbody>
                        </table>
                    </div>

                    <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                        <p id="paginationInfo" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Showing 0 entries</p>
                        <div id="paginationButtons" class="flex gap-2">
                            <!-- Populated by member_asset.js -->
                        </div>
                    </div>
                </div>

            </div>
            <div class="py-6 text-center">
                <?php include '../footer.php'; ?>
            </div>
    </main>

    <!-- Borrow Modal (Member) -->
    <div id="borrowModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center text-center">
                <div class="w-full">
                    <h3 class="text-xl font-bold text-slate-900">Request Item</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Borrow Request Form</p>
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

    <!-- Auth & Logic -->
    <script src="../../assets/js/auth.js"></script>
    <script src="../../assets/js/member/assets.js?v=<?php echo time(); ?>"></script>

</body>

</html>