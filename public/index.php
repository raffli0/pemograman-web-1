<?php
// Simple Router
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query strings
$uri = strtok($uri, '?');

// Define API routes
if (strpos($uri, '/api/') !== false) {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../app/core/AuthMiddleware.php';
    require_once __DIR__ . '/../app/core/RoleMiddleware.php';

    // Parse resource from URI (e.g., /ukm/public/api/auth/login -> auth, login)
    // Structure: /ukm/public/api/{controller}/{action}
    $parts = explode('/', trim($uri, '/'));
    // Parts: [ukm, public, api, controller, action]
    // Index: 0    1       2    3           4

    // Adjust indices based on actual path. Assuming /ukm/public/api/
    $controllerName = isset($parts[3]) ? ucfirst($parts[3]) . 'Controller' : null;
    $actionName = isset($parts[4]) ? $parts[4] : 'index';

    if ($controllerName) {
        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();

            // Apply Middleware based on Controller/Action
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

// Frontend Routing is handled by .htaccess rewriting everything to index.php if not an API call
// But wait, .htaccess redirects non-files to index.php. 
// If it is a page request, we likely want to serve the specific PHP file in pages/
// However, direct access to /pages/login.php is possible.
// Let's make a simple catch-all for cleanliness if users type /dashboard instead of /pages/dashboard.php

// Re-map clean URLs to pages
if (strpos($uri, '/dashboard') !== false) {
    require_once 'pages/dashboard.php';
} elseif (strpos($uri, '/login') !== false) {
    require_once 'pages/login.php';
} elseif (strpos($uri, '/assets') !== false) {
    require_once 'pages/assets.php';
} elseif (strpos($uri, '/borrow') !== false) {
    require_once 'pages/borrow.php';
} elseif (strpos($uri, '/returns') !== false) {
    require_once 'pages/returns.php';
} elseif ($uri == '/ukm/public/') {
    // Check if logged in, else login
    header("Location: /ukm/public/pages/login.php");
} else {
    // If specific file requested (like assets/css...), standard server handling applies.
    // If we are here, it found nothing.
    http_response_code(404);
    echo "404 Not Found";
}
