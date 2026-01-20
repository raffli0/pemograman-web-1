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
    <title>Role Control - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#7e22ce", "background-light": "#f5f8f8" },
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
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Role Definitions</h1>
                <p class="text-slate-500 mt-1 font-medium">View and configure system roles.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Role Card: Member -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-lg text-slate-800">Member</h3>
                    <p class="text-sm text-slate-500 mt-2">Standard user with borrow privileges.</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400">Fixed Role</div>
                </div>
                <!-- Role Card: Org Admin -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-lg text-slate-800">Org Admin</h3>
                    <p class="text-sm text-slate-500 mt-2">Manages assets and members within an organization.</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-400">Fixed Role</div>
                </div>
                <!-- Role Card: Super Admin -->
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm border-purple-200 bg-purple-50/50">
                    <h3 class="font-bold text-lg text-purple-900">Super Admin</h3>
                    <p class="text-sm text-slate-500 mt-2">Full system access and configuration control.</p>
                    <div class="mt-4 pt-4 border-t border-purple-100 text-xs text-purple-400">System Role</div>
                </div>
            </div>
        </div>
    </main>
    <script src="/ukm/public/assets/js/auth.js"></script>
</body>

</html>