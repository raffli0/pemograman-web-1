<?php
require_once __DIR__ . '/../../app/core/Session.php';
Session::init();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Asset Responsibility System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/asset_management/public/assets/css/tailwind-custom.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-50 font-sans antialiased">

    <!-- Clean background -->

    <div class="relative min-h-screen flex">

        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 gradient-animate opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-10"></div>

            <div class="relative z-10 flex flex-col justify-center items-start px-16 py-12 text-white">
                <div class="mb-8 animate-slide-up">
                    <h1 class="text-5xl font-bold mb-4 leading-tight">Asset Responsibility<br>System</h1>
                    <p class="text-xl text-white/80 font-light">Streamline your borrowing management with confidence</p>
                </div>

                <div class="mt-12 space-y-6 animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-start space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Track Everything</h3>
                            <p class="text-white/70 text-sm">Monitor all borrowed assets in real-time with detailed logs
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Team Collaboration</h3>
                            <p class="text-white/70 text-sm">Manage users and assign responsibilities seamlessly</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Powerful Analytics</h3>
                            <p class="text-white/70 text-sm">Get insights with comprehensive reports and dashboards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative">
            <div class="w-full max-w-md animate-fade-in">

                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div
                        class="inline-flex w-14 h-14 bg-teal-600 rounded-2xl items-center justify-center mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Asset Responsibility System</h2>
                </div>

                <!-- Login Card - Solid Background -->
                <div class="bg-white rounded-3xl p-8 shadow-2xl border border-slate-200">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                        <p class="text-gray-600">Sign in to your account to continue</p>
                    </div>

                    <form id="loginForm" class="space-y-6">
                        <!-- Flash Messages -->
                        <?php if (Session::hasFlash('success')): ?>
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl text-sm">
                                <?php echo Session::getFlash('success'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (Session::hasFlash('error')): ?>
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl text-sm">
                                <?php echo Session::getFlash('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email
                                Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                        </path>
                                    </svg>
                                </div>
                                <input type="email" id="email" required placeholder="name@company.com"
                                    class="input-focus w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password"
                                class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                                <input type="password" id="password" required placeholder="Enter your password"
                                    class="input-focus w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" id="loginBtn"
                            class="btn-hover w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                            <span class="relative z-10">Sign In</span>
                        </button>

                        <!-- Error Message -->
                        <div id="error-msg" class="text-red-600 text-sm text-center font-medium"></div>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an organization?
                            <a href="register_org.php"
                                class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                Register here
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Auth JS -->
    <script src="/asset_management/public/assets/js/auth.js"></script>
</body>

</html>