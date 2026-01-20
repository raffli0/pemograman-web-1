<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
if ($user['role'] !== 'org_admin') {
    if ($user['role'] === 'super_admin') {
        header("Location: ../superadmin/dashboard.php");
    } else {
        header("Location: ../member/assets.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Dashboard Overview - Campus Workflow</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .chart-gradient {
            background: linear-gradient(180deg, rgba(0, 110, 122, 0.1) 0%, rgba(0, 110, 122, 0) 100%);
        }
    </style>
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden">

    <?php include '../partials/sidebar-admin.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto h-screen">
        <!-- Top Navigation Bar -->
        <?php include '../header.php'; ?>

        <div class="p-8 pb-24">
            <!-- Page Heading & Breadcrumbs -->
            <div class="mb-8">
                <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
                    <a class="hover:text-primary transition-colors" href="/asset_management/public/pages/admin/dashboard.php">Admin
                        Overview</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-slate-600">Home</span>
                </nav>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Dashboard Overview</h2>
                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                            <span class="material-symbols-outlined text-lg">calendar_today</span>
                            <span>Last 30 Days</span>
                        </button>
                        <button
                            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:shadow-lg hover:shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-lg">download</span>
                            <span>Export Report</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Assets -->
                <div
                    class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                    <div class="flex items-start justify-between mb-4">
                        <div class="size-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">devices</span>
                        </div>
                        <span class="text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded">+2.5%</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Assets Managed</p>
                    <p class="text-3xl font-black mt-1" id="statAssets">-</p>
                </div>
                <!-- Assets in Use -->
                <div
                    class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm group hover:border-primary/50 transition-colors">
                    <div class="flex items-start justify-between mb-4">
                        <div class="size-12 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <span class="material-symbols-outlined">person_pin_circle</span>
                        </div>
                        <span class="text-slate-400 text-xs font-bold">69% Utilization</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Active Uses</p>
                    <p class="text-3xl font-black mt-1" id="statBorrows">-</p>
                </div>
                <!-- Pending Requests -->
                <div
                    class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-orange-500/10 group hover:border-orange-500/50 transition-colors">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-lg bg-orange-500/10 text-orange-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">priority_high</span>
                        </div>
                        <span class="text-orange-600 text-xs font-bold bg-orange-50 px-2 py-1 rounded">Action
                            Needed</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">To Review</p>
                    <p class="text-3xl font-black mt-1" id="statPending">-</p>
                </div>
                <!-- Verification Needed -->
                <div
                    class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-emerald-500/10 group hover:border-emerald-500/50 transition-colors">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">fact_check</span>
                        </div>
                        <span class="text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded">Asset
                            Recovery</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Returns Pending</p>
                    <p class="text-3xl font-black mt-1" id="statVerifications">-</p>
                </div>
            </div>

            <!-- Main Grid: Chart and Activity -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Chart Section -->
                <div
                    class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">Request Volume</h3>
                            <p class="text-xs text-slate-500 font-medium">Daily asset allocation requests</p>
                        </div>
                        <select
                            class="bg-slate-50 border-none text-xs font-bold rounded-lg focus:ring-0 cursor-pointer">
                            <option>Weekly View</option>
                            <option>Monthly View</option>
                        </select>
                    </div>
                    <div class="flex-1 p-6 flex flex-col justify-end min-h-[300px]">
                        <!-- Visual placeholder for a line chart -->
                        <div class="relative h-64 w-full flex items-end justify-between gap-1">
                            <div class="absolute inset-0 grid grid-rows-4 w-full">
                                <div class="border-t border-slate-100 w-full relative"><span
                                        class="absolute -top-3 right-0 text-[10px] text-slate-400">40</span></div>
                                <div class="border-t border-slate-100 w-full relative"><span
                                        class="absolute -top-3 right-0 text-[10px] text-slate-400">30</span></div>
                                <div class="border-t border-slate-100 w-full relative"><span
                                        class="absolute -top-3 right-0 text-[10px] text-slate-400">20</span></div>
                                <div class="border-t border-slate-100 w-full relative"><span
                                        class="absolute -top-3 right-0 text-[10px] text-slate-400">10</span></div>
                            </div>
                            <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none"
                                viewBox="0 0 100 100">
                                <path d="M 0 80 Q 15 70 30 75 T 60 40 T 80 50 T 100 20 L 100 100 L 0 100 Z"
                                    fill="url(#chartFill)"></path>
                                <path d="M 0 80 Q 15 70 30 75 T 60 40 T 80 50 T 100 20" fill="none" stroke="#006e7a"
                                    stroke-linecap="round" stroke-width="2"></path>
                                <defs>
                                    <linearGradient id="chartFill" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#006e7a" stop-opacity="0.1"></stop>
                                        <stop offset="100%" stop-color="#006e7a" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="w-full flex justify-between mt-auto pt-4 relative z-10">
                                <span class="text-[10px] font-bold text-slate-400">Mon</span>
                                <span class="text-[10px] font-bold text-slate-400">Tue</span>
                                <span class="text-[10px] font-bold text-slate-400 text-primary">Wed</span>
                                <span class="text-[10px] font-bold text-slate-400">Thu</span>
                                <span class="text-[10px] font-bold text-slate-400">Fri</span>
                                <span class="text-[10px] font-bold text-slate-400">Sat</span>
                                <span class="text-[10px] font-bold text-slate-400">Sun</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-lg">Recent Activity</h3>
                        <p class="text-xs text-slate-500 font-medium">Real-time system updates</p>
                    </div>
                    <div id="activityTimeline" class="flex-1 overflow-y-auto max-h-[400px]">
                        <div class="text-slate-400 text-sm py-8 text-center">Loading feed...</div>
                    </div>
                    <button
                        class="w-full py-4 text-xs font-black text-primary hover:bg-primary/5 transition-colors uppercase tracking-widest">
                        View All Activity
                    </button>
                </div>
            </div>

            <script src="/asset_management/public/assets/js/auth.js"></script>
            <script>
                async function loadStats() {
                    try {
                        const res = await fetchAPI('/dashboard/index');
                        if (res.status === 'success') {
                            document.getElementById('statAssets').textContent = res.data.total_assets;
                            document.getElementById('statBorrows').textContent = res.data.active_borrows;
                            document.getElementById('statPending').textContent = res.data.pending_requests;
                            if (document.getElementById('statVerifications')) {
                                document.getElementById('statVerifications').textContent = res.data.pending_verifications;
                            }

                            // Render Timeline
                            const timelineContainer = document.getElementById('activityTimeline');
                            if (timelineContainer && res.data.logs) {
                                timelineContainer.innerHTML = '';
                                if (res.data.logs.length === 0) {
                                    timelineContainer.innerHTML = '<p class="text-slate-400 text-sm py-8 text-center">No recent activity logged.</p>';
                                } else {
                                    res.data.logs.forEach((log, index) => {
                                        // Icon based on action type
                                        let iconColor = 'bg-slate-100 text-slate-500';
                                        let icon = 'notifications';

                                        if (log.action.includes('APPROVE') || log.action.includes('CREATE')) {
                                            iconColor = 'bg-green-100 text-green-600';
                                            icon = 'check_circle';
                                        }
                                        if (log.action.includes('REJECT') || log.action.includes('DELETE')) {
                                            iconColor = 'bg-red-100 text-red-600';
                                            icon = 'cancel';
                                        }
                                        if (log.action.includes('REQUEST')) {
                                            iconColor = 'bg-blue-100 text-blue-600';
                                            icon = 'move_to_inbox';
                                        }

                                        timelineContainer.innerHTML += `
                                            <div class="p-4 border-b border-slate-50 flex gap-4 hover:bg-slate-50 transition-colors">
                                                <div class="size-10 rounded-full ${iconColor} flex-shrink-0 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-xl">${icon}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm">
                                                        <span class="font-bold">${log.action.replace(/_/g, ' ')}</span>
                                                        <span class="text-slate-600">${log.details}</span>
                                                    </p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase">${log.created_at}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }
                            }
                        }
                    } catch (e) { console.error(e); }
                }
                loadStats();
            </script>
        </div>
        <div class="py-6 text-center">
            <?php include '../footer.php'; ?>
        </div>

    </main>
</body>

</html>