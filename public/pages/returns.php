<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../../app/core/RoleMiddleware.php';
$user = AuthMiddleware::authenticate();
RoleMiddleware::authorize(['org_admin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns - Campus Admin</title>

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

<body class="bg-background-light text-slate-900 min-h-screen flex overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto h-full flex flex-col">
        <!-- Sticky Header -->
        <?php include 'header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto w-full">
            <!-- Page Heading -->
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <nav
                        class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                        <a href="dashboard.php" class="hover:text-primary transition-colors">Logistics</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-slate-900">Asset Verification</span>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Verification Registry</h1>
                    <p class="text-slate-500 mt-1 font-medium">Formally verify condition reports and restock assets.</p>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Pending Verification</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Asset Restocking
                            Queue
                        </p>
                    </div>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">filter_alt</span>
                        <input type="text" id="tableFilter" placeholder="Quick filter..."
                            class="pl-9 pr-4 py-1.5 bg-slate-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Borrower Identity</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Asset Description</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Maturity Date</th>
                                <th
                                    class="text-right py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody id="returnsTableBody" class="divide-y divide-slate-50">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                    <p id="paginationInfo" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Monitoring returns status...</p>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <!-- Return Modal -->
    <div id="returnModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 text-center">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Finalize Recovery</h3>
                <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Quality Assurance Report</p>
            </div>
            <form id="returnForm" class="p-8 space-y-6">
                <input type="hidden" id="returnRequestId">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Asset Condition
                        Status</label>
                    <textarea id="conditionNote" required rows="3"
                        class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm resize-none"
                        placeholder="Detail any changes in condition or simply 'Perfect Condition'"></textarea>
                </div>
                <div class="flex flex-col gap-2 pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">Submit
                        Inspection & Recover</button>
                    <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')"
                        class="w-full py-3 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel
                        Processing</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/return.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => loadReturns());
    </script>
</body>

</html>