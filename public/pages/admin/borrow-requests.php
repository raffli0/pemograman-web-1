<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../../../app/core/RoleMiddleware.php';
$user = AuthMiddleware::authenticate();
RoleMiddleware::authorize(['org_admin']);
$isAdmin = true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests - Campus Admin</title>

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
                        <a href="dashboard.php" class="hover:text-primary transition-colors">Transactions</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-slate-900">Borrow Requests</span>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Borrow Requests</h1>
                    <p class="text-slate-500 mt-1 font-medium">Review and process new asset requests.</p>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Request Queue</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Pending items
                            needing review</p>
                    </div>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">filter_list</span>
                        <input type="text" id="tableFilter" placeholder="Filter by status..."
                            class="pl-9 pr-4 py-1.5 bg-slate-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Borrower</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Asset Item</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Duration</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Status</th>
                                <?php if ($isAdmin): ?>
                                    <th
                                        class="text-right py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Review Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="borrowTableBody" class="divide-y divide-slate-50">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                    <p id="paginationInfo" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Showing 0 Requests</p>
                    <div id="paginationButtons" class="flex gap-2">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <div class="py-6 text-center">
            <?php include '../footer.php'; ?>
        </div>
    </main>

    </div>

    <!-- Admin Verify Modal -->
    <div id="verifyReturnModal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">

                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-900">Verify Asset Return</h3>
                        <button onclick="document.getElementById('verifyReturnModal').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <input type="hidden" id="verifyRequestId">

                        <!-- Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Borrower</span>
                                <span id="verifyBorrowerName" class="font-bold text-slate-800 text-sm">-</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Asset</span>
                                <span id="verifyAssetName" class="font-bold text-slate-800 text-sm">-</span>
                            </div>
                            <div class="col-span-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Scheduled
                                    Duration</span>
                                <span id="verifyDuration" class="font-bold text-slate-800 text-sm font-mono">-</span>
                            </div>
                        </div>

                        <!-- User Note -->
                        <div>
                            <span
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">User's
                                Condition Report</span>
                            <div id="verifyUserNote"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm text-slate-600 italic">
                                "No remarks."
                            </div>
                        </div>

                        <!-- Proof Image -->
                        <div>
                            <span
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Proof
                                of Condition</span>
                            <div id="verifyProofContainer"
                                class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center min-h-[150px]">
                                <span class="text-xs text-slate-400 font-medium">No image uploaded</span>
                            </div>
                        </div>

                        <!-- Admin Note -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Admin
                                Remarks (Optional)</label>
                            <textarea id="verifyAdminNote" rows="2"
                                class="w-full px-4 py-3 bg-white border-2 border-slate-100 focus:border-primary/20 focus:ring-0 rounded-xl text-sm transition-all"
                                placeholder="Any internal notes..."></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button onclick="submitVerification()" id="btnVerifyParams"
                            class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto transition-all">
                            Verify & Restock
                        </button>
                        <button onclick="document.getElementById('verifyReturnModal').classList.add('hidden')"
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/asset_management/public/assets/js/auth.js"></script>
    <script src="/asset_management/public/assets/js/admin/borrow-approval.js"></script>
    <script>
        window.isAdmin = <?php echo json_encode($isAdmin); ?>;
        document.addEventListener('DOMContentLoaded', () => loadRequests(window.isAdmin));
    </script>

</body>

</html>