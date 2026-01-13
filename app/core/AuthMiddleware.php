<?php
require_once __DIR__ . '/JWTService.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthMiddleware
{
    public static function authenticate()
    {
        if (!isset($_COOKIE['auth_token'])) {
            // For API requests, return 401. For page loads, redirect to login.
            if (self::isApiRequest()) {
                Response::json('error', 'Unauthorized access', [], 401);
            } else {
                header("Location: /ukm/public/pages/login.php");
                exit;
            }
        }

        $token = $_COOKIE['auth_token'];
        $payload = JWTService::validate($token);

        if (!$payload) {
            if (self::isApiRequest()) {
                Response::json('error', 'Invalid token', [], 401);
            } else {
                setcookie("auth_token", "", time() - 3600, "/"); // clear cookie
                header("Location: /ukm/public/pages/login.php");
                exit;
            }
        }

        return $payload;
    }

    private static function isApiRequest()
    {
        return (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (isset($_GET['api']) && $_GET['api'] == 'true');
    }
}
