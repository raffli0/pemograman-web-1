<?php
require_once __DIR__ . '/../../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();

if ($user['role'] !== 'org_admin') {
    header("Location: ../member/assets.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet" />
    <!-- Tailwind CSS -->
    <link href="../../assets/css/styles.css" rel="stylesheet">
</head>

<body class="bg-background-light text-slate-900 h-screen flex overflow-hidden">
    <?php include '../partials/sidebar-admin.php'; ?>
    <main class="flex-1 overflow-y-auto h-full flex flex-col">
        <?php include '../header.php'; ?>
        <div class="p-8 max-w-7xl mx-auto w-full">
            <div class="mb-8">
                <nav
                    class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                    <a href="dashboard.php" class="hover:text-primary transition-colors">Admin</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-slate-900">Maintenance</span>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Asset Maintenance</h1>
                <p class="text-slate-500 mt-1 font-medium">Manage assets undergoing repairs or service.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900">Maintenance Queue</h3>
                </div>
                <div class="p-8 text-center text-slate-400 text-sm">
                    Maintenance features coming soon.
                </div>
            </div>
        </div>
        <div class="text-center py-6">
            <?php include '../footer.php'; ?>
        </div>
    </main>

    <!-- Auth JS -->
    <script src="../../assets/js/auth.js"></script>
</body>

</html>