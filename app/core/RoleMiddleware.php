<?php
require_once __DIR__ . '/../helpers/Response.php';

class RoleMiddleware
{
    public static function authorize($allowed_roles = [])
    {
        global $user; // Assume $user is set by AuthMiddleware in the router scope

        if (!$user || !in_array($user['role'], $allowed_roles)) {
            Response::json('error', 'Forbidden: You do not have permission', [], 403);
        }
    }
}
