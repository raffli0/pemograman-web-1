<?php
// Load Environment Variables
require_once __DIR__ . '/../app/core/Env.php';
Env::load(__DIR__ . '/../.env');

// Configure Error Reporting based on Environment
if (getenv('DISPLAY_ERRORS') === '1') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL); // Log errors, but don't show them
}

// Simple Router
$uri = $_SERVER['REQUEST_URI'];

// Clean URI
$uri = strtok($uri, '?');

// 1. API Routing (Keep strictly as is)
if (strpos($uri, '/api/') !== false) {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../app/core/AuthMiddleware.php';
    require_once __DIR__ . '/../app/core/RoleMiddleware.php';

    $parts = explode('/', trim($uri, '/'));
    // Expected: [ukm, public, api, controller, action]

    // Adjust index based on your URL structure (e.g. localhost/ukm/public/api/...)
    // If localhost/ukm/public/api/asset/create -> parts[3] = asset, parts[4] = create
    $controllerName = isset($parts[3]) ? ucfirst($parts[3]) . 'Controller' : null;
    $actionName = isset($parts[4]) ? $parts[4] : 'index';

    if ($controllerName) {
        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();

            if ($controllerName != 'AuthController') {
                $user = AuthMiddleware::authenticate();
            }

            if (method_exists($controller, $actionName)) {
                $controller->$actionName();
            } else {
                echo json_encode(['error' => 'Action not found']);
            }
        } else {
            echo json_encode(['error' => 'Controller not found']);
        }
    }
    exit;
}

// 2. Page Routing (Legacy support for clean URLs)
if (strpos($uri, '/dashboard') !== false) {
    require_once 'pages/dashboard.php';
    exit;
}
if (strpos($uri, '/login') !== false) {
    require_once 'pages/login.php';
    exit;
}
if (strpos($uri, '/assets') !== false) {
    require_once 'pages/assets.php';
    exit;
}
if (strpos($uri, '/borrow') !== false) {
    require_once 'pages/borrow.php';
    exit;
}
if (strpos($uri, '/returns') !== false) {
    require_once 'pages/returns.php';
    exit;
}
if (strpos($uri, '/users') !== false) {
    require_once 'pages/users.php';
    exit;
}
if (strpos($uri, '/reports') !== false) {
    require_once 'pages/reports.php';
    exit;
}

// 3. Landing Page (Default Route)
// If it's the root, show the landing page instead of redirecting
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Responsibility System</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#006e7a",
                        "primary-dark": "#005a63",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    backgroundImage: {
                        'hero-pattern': "url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')",
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }
    </style>
</head>

<body class="text-slate-900 bg-white">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2 cursor-pointer"
                    onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                    <span class="font-black text-xl tracking-tight text-slate-900">Asset Responsibility System</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features"
                        class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors">Features</a>
                    <a href="#about"
                        class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors">About</a>
                    <div class="flex items-center gap-3 ml-4">
                        <a href="pages/login.php"
                            class="text-sm font-bold text-slate-700 hover:text-primary transition-colors">Log In</a>
                        <a href="pages/register_org.php"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 transform hover:-translate-y-0.5">Get
                            Started</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div
            class="absolute inset-0 z-0 opacity-5 bg-[radial-gradient(#006e7a_1px,transparent_1px)] [background-size:20px_20px]">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="animate-fade-in-up">
                <span
                    class="inline-block py-1 px-3 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-6 border border-primary/20">
                    Operational Excellence
                </span>
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 tracking-tight mb-6 leading-tight">
                    Manage Campus Assets <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-emerald-500">With
                        Precision</span>
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                    The centralized platform for universities to track inventory, manage equipment borrowing, and
                    streamline
                    operational workflows.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="pages/register_org.php"
                        class="w-full sm:w-auto px-8 py-4 bg-primary text-white rounded-xl font-bold text-lg hover:bg-primary-dark transition-all shadow-xl shadow-primary/25 flex items-center justify-center gap-2 transform hover:-translate-y-1">
                        Start Organization
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="pages/login.php"
                        class="w-full sm:w-auto px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-lg hover:bg-slate-50 transition-colors hover:border-slate-300">
                        Member Login
                    </a>
                </div>
            </div>

            <!-- Dashboard Preview Placeholder -->
            <div
                class="mt-20 relative rounded-2xl border border-slate-200 shadow-2xl overflow-hidden bg-slate-50 max-w-5xl mx-auto transform hover:scale-[1.01] transition-transform duration-500">
                <div
                    class="absolute top-0 w-full h-8 bg-slate-100 border-b border-slate-200 flex items-center px-4 gap-2">
                    <div class="size-3 rounded-full bg-red-400"></div>
                    <div class="size-3 rounded-full bg-amber-400"></div>
                    <div class="size-3 rounded-full bg-green-400"></div>
                </div>
                <div class="pt-8 px-2 pb-2">
                    <div
                        class="bg-white rounded-lg p-8 min-h-[400px] flex items-center justify-center text-slate-300 bg-gradient-to-b from-white to-slate-50">
                        <div class="text-center group cursor-default">
                            <span
                                class="material-symbols-outlined text-6xl opacity-20 group-hover:opacity-30 transition-opacity text-primary">dashboard</span>
                            <p class="font-medium mt-4 opacity-50 group-hover:opacity-70 transition-opacity">Intelligent
                                Dashboard Interface</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-slate-900 mb-4">Everything You Need</h2>
                <p class="text-slate-500 max-w-xl mx-auto">Complete toolkit for administrative efficiency.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div
                        class="size-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Asset Registry</h3>
                    <p class="text-slate-500 leading-relaxed">Track every piece of equipment with detailed metadata,
                        location tracking, and real-time status updates.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div
                        class="size-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">schedule</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Smart Borrowing</h3>
                    <p class="text-slate-500 leading-relaxed">Streamlined requisition process with automated stock
                        checks, approval workflows, and return management.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div
                        class="size-14 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">analytics</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Insightful Reports</h3>
                    <p class="text-slate-500 leading-relaxed">Generate comprehensive PDF & CSV reports on inventory
                        utilization, loss prevention, and activity logs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- Footer -->
    <footer class="bg-white py-12 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-center gap-6">
            <?php include 'pages/footer.php'; ?>
        </div>
    </footer>

</body>

</html>