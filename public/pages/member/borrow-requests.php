<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowing Requests - Member</title>

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

    <?php include '../sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto h-screen flex flex-col">
        <!-- Sticky Header -->
        <?php include '../header.php'; ?>

        <div class="p-8 max-w-7xl mx-auto w-full">
            <!-- Page Heading -->
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <nav
                        class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                        <a href="assets.php" class="hover:text-primary transition-colors">My Account</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-slate-900">Borrowing Requests</span>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">My Borrowing Requests</h1>
                    <p class="text-slate-500 mt-1 font-medium">Track the status of your asset borrowing activity.</p>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">My Requests</h3>
                    </div>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">filter_list</span>
                        <input type="text" id="tableFilter" placeholder="Search my requests..."
                            class="pl-9 pr-4 py-1.5 bg-slate-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Asset Name</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Requested Duration</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Request Date</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="text-right py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Action</th>
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

    <!-- Return Asset Modal -->
    <div id="returnSubmitModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
            <div class="px-8 pt-8 pb-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="size-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">assignment_return</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Return Asset</h3>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Confirm the asset
                            condition before completing the return
                        </p>
                    </div>
                </div>

                <form id="returnSubmitForm" class="space-y-5">
                    <input type="hidden" id="returnBorrowId">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Asset
                            Condition Notes</label>
                        <textarea id="returnConditionNote" required rows="4"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent focus:border-primary/20 focus:bg-white rounded-2xl text-sm transition-all focus:ring-0 placeholder:text-slate-400"
                            placeholder="Describe the asset condition..."></textarea>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Upload
                            Condition Proof (optional)</label>
                        <input type="file" id="returnProofImage" accept="image/png, image/jpeg, application/pdf"
                            class="w-full bg-slate-50 border-slate-200 focus:ring-2 focus:ring-primary/20 rounded-lg px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button"
                            onclick="document.getElementById('returnSubmitModal').classList.add('hidden')"
                            class="flex-1 px-6 py-3 border-2 border-slate-100 text-slate-500 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all">Cancel</button>
                        <button type="submit" id="submitReturnBtn"
                            class="flex-3 px-8 py-3 bg-primary text-white rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all">
                            Submit Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/ukm/public/assets/js/auth.js"></script>
    <script src="/ukm/public/assets/js/member_borrow.js"></script>
</body>

</html>