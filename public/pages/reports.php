<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();

// Ensure only admins can access
if ($user['role'] !== 'org_admin' && $user['role'] !== 'super_admin') {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Campus Admin</title>

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

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto h-screen">
        <!-- Top Navigation Bar -->
        <?php include 'header.php'; ?>

        <div class="p-8 pb-24">
            <!-- Page Heading & Breadcrumbs -->
            <div class="mb-8">
                <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
                    <a class="hover:text-primary transition-colors" href="dashboard.php">Admin Hub</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-slate-600">Reports</span>
                </nav>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Reports Center</h2>
                </div>
                <p class="text-slate-500 mt-2">Generate and export insights about assets and borrowing activity.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Report Card: Asset Inventory -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="size-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <span class="material-symbols-outlined text-2xl">inventory_2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Asset Inventory</h3>
                            <p class="text-sm text-slate-500">Full list of assets with status and location.</p>
                        </div>
                    </div>

                    <form id="inventoryForm" class="space-y-4">
                        <input type="hidden" name="type" value="inventory">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">From
                                    Added Date</label>
                                <input type="date" name="start_date"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">To
                                    Added Date</label>
                                <input type="date" name="end_date"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" onclick="generateReport('inventory', 'csv')"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-lg">download</span>
                                Download CSV
                            </button>
                            <button type="button" onclick="generateReport('inventory', 'print')"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-lg">print</span>
                                Print / PDF
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Report Card: Borrowing History -->
                <div
                    class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="size-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100">
                            <span class="material-symbols-outlined text-2xl">history_edu</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Borrowing History</h3>
                            <p class="text-sm text-slate-500">Log of all borrow requests and returns.</p>
                        </div>
                    </div>

                    <form id="borrowForm" class="space-y-4">
                        <input type="hidden" name="type" value="borrowing">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">From
                                    Date</label>
                                <input type="date" name="start_date"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">To
                                    Date</label>
                                <input type="date" name="end_date"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" onclick="generateReport('borrowing', 'csv')"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-lg">download</span>
                                Download CSV
                            </button>
                            <button type="button" onclick="generateReport('borrowing', 'print')"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-lg">print</span>
                                Print / PDF
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <script>
        function generateReport(type, format) {
            const formId = type === 'inventory' ? 'inventoryForm' : 'borrowForm';
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            if (format === 'csv') {
                // Direct link to API endpoint triggers download
                window.location.href = `/ukm/public/api/report/exportCsv?${params.toString()}`;
            } else if (format === 'print') {
                // Open print view in new tab
                window.open(`print_report.php?${params.toString()}`, '_blank');
            }
        }
    </script>
</body>

</html>