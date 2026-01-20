<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../core/RoleMiddleware.php';
require_once __DIR__ . '/../helpers/Response.php';

class UserController
{
    // Superadmin: Get ALL users across the system
    public function indexGlobal()
    {
        $user = AuthMiddleware::authenticate();

        // Strict Role Check
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized access', [], 403);
        }

        $userModel = new User();
        $stmt = $userModel->getAllGlobal();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::json('success', 'Global user registry retrieved', $users);
    }

    public function updateGlobal()
    {
        $admin = AuthMiddleware::authenticate();
        if ($admin['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->name) || !isset($data->email) || !isset($data->role)) {
            Response::json('error', 'Missing required fields', [], 400);
        }

        $userModel = new User();
        // Optional password handling
        $password = isset($data->password) && !empty($data->password) ? $data->password : null;

        if ($userModel->update($data->id, $data->name, $data->email, $data->role, $password)) {
            Response::json('success', 'User updated successfully');
        } else {
            Response::json('error', 'Failed to update user', [], 500);
        }
    }

    public function deleteGlobal()
    {
        $admin = AuthMiddleware::authenticate();
        if ($admin['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id)) {
            Response::json('error', 'User ID required', [], 400);
        }

        // Prevent deleting self
        if ($data->id == $admin['id']) {
            Response::json('error', 'Cannot delete your own account', [], 400);
        }

        $userModel = new User();
        if ($userModel->delete($data->id)) {
            Response::json('success', 'User deleted successfully');
        } else {
            Response::json('error', 'Failed to delete user', [], 500);
        }
    }
}
