<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My History - Member Portal</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
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

    <?php include '../partials/sidebar-member.php'; ?>

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
                        <span class="text-slate-900">History</span>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Borrow History</h1>
                    <p class="text-slate-500 mt-1 font-medium">Your past borrowing activity.</p>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Activity Log</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Item Name</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Duration</th>
                                <th
                                    class="text-left py-4 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="text-left py-4 px-8 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Admin Comments</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody" class="divide-y divide-slate-50">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
                <div id="noHistoryMsg" class="hidden p-12 text-center">
                    <div
                        class="inline-flex items-center justify-center size-12 rounded-xl bg-slate-50 text-slate-300 mb-4">
                        <span class="material-symbols-outlined text-2xl">history_toggle_off</span>
                    </div>
                    <p class="text-slate-500 font-medium text-sm">No historical records found</p>
                </div>
                <!-- Pagination Footer Style match -->
                <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">End of History</p>
                </div>
            </div>
        </div>

        <div class="py-6 text-center">
            <?php include '../footer.php'; ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="/asset_management/public/assets/js/auth.js"></script>
    <script>
        async function loadHistory() {
            try {
                const res = await fetchAPI('/borrow/index');
                if (res.status === 'success') {
                    // Filter for past/completed states
                    const history = res.data.filter(req => ['returned', 'rejected', 'cancelled'].includes(req.status));
                    const tbody = document.getElementById('historyTableBody');

                    if (history.length === 0) {
                        document.getElementById('noHistoryMsg').classList.remove('hidden');
                        return;
                    }

                    history.forEach(req => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50/50 transition-colors group';

                        let statusBadge = '';
                        let badgeStyle = '';

                        switch (req.status) {
                            case 'rejected':
                                badgeStyle = 'bg-red-50 text-red-700';
                                statusBadge = 'Rejected';
                                break;
                            case 'returned':
                                badgeStyle = 'bg-emerald-50 text-emerald-700';
                                statusBadge = 'Returned';
                                break;
                            case 'cancelled':
                                badgeStyle = 'bg-slate-100 text-slate-600';
                                statusBadge = 'Cancelled';
                                break;
                            default:
                                badgeStyle = 'bg-slate-100 text-slate-600';
                                statusBadge = req.status;
                        }

                        tr.innerHTML = `
                            <td class="py-4 px-8">
                                <span class="block text-sm font-bold text-slate-900 group-hover:text-primary transition-colors">${req.asset_name}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-xs font-medium text-slate-600">
                                    <span class="block text-slate-900 mb-0.5">${req.start_date}</span>
                                    <span class="text-slate-400">to</span> ${req.end_date}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider ${badgeStyle}">
                                    <span class="size-1.5 rounded-full bg-current opacity-50"></span>
                                    ${statusBadge}
                                </span>
                            </td>
                            <td class="py-4 px-8 text-xs text-slate-500 font-medium italic">
                                ${req.admin_remarks ? `"${req.admin_remarks}"` : '<span class="text-slate-300">No remarks</span>'}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            } catch (e) { console.error(e); }
        }
        document.addEventListener('DOMContentLoaded', loadHistory);
    </script>
</body>

</html>