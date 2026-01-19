<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Organization.php';
require_once __DIR__ . '/../core/JWTService.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthController
{

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->email) || !isset($data->password)) {
            Response::json('error', 'Email and Password required', [], 400);
        }

        $user = new User();
        if ($user->login($data->email, $data->password)) {
            // SaaS Hardening: Check Organization Status (Skip for Super Admin)
            if ($user->role !== 'super_admin') {
                $orgModel = new Organization();
                $org = $orgModel->getById($user->organization_id);

                if (!$org || $org['status'] !== 'active') {
                    Response::json('error', 'Organization is suspended or inactive. Contact Support.', [], 403);
                }
            }

            $token_payload = [
                'id' => $user->id,
                'org_id' => $user->organization_id, // Important for SaaS
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ];

            $jwt = JWTService::generate($token_payload);
            setcookie("auth_token", $jwt, time() + (60 * 60 * 24), "/", "", false, true);
            Response::json('success', 'Login successful', ['role' => $user->role, 'org_id' => $user->organization_id]);
        } else {
            Response::json('error', 'Invalid credentials', [], 401);
        }
    }

    public function registerOrg()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->org_name) || !isset($data->admin_name) || !isset($data->email) || !isset($data->password)) {
            Response::json('error', 'All fields required', [], 400);
        }

        // 1. Create Organization
        $org = new Organization();
        try {
            $org_id = $org->create($data->org_name);
            if (!$org_id) {
                Response::json('error', 'Organization name likely exists', [], 409);
            }

            // 2. Create Org Admin
            $user = new User();
            if ($user->create($org_id, $data->admin_name, $data->email, $data->password, 'org_admin')) {
                Response::json('success', 'Organization registered successfully');
            } else {
                Response::json('error', 'Failed to create admin user', [], 500);
            }

        } catch (Exception $e) {
            Response::json('error', $e->getMessage(), [], 500);
        }
    }

    public function createUser()
    {
        // Manual Auth since controller is excluded from global middleware
        $user = AuthMiddleware::authenticate();

        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        $newUser = new User();

        // Default to member role for internally created users
        if ($newUser->create($user['org_id'], $data->name, $data->email, $data->password, 'member')) {
            Response::json('success', 'Member added successfully');
        } else {
            Response::json('error', 'Failed to add member', [], 500);
        }
    }

    public function getUsers()
    {
        $user = AuthMiddleware::authenticate();
        if (!in_array($user['role'], ['org_admin', 'member'])) {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $userModel = new User();
        $stmt = $userModel->getByOrganization($user['org_id']);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Users retrieved', $users);
    }

    public function logout()
    {
        setcookie("auth_token", "", time() - 3600, "/");
        Response::json('success', 'Logged out successfully');
    }
}
