<?php
require_once __DIR__ . '/../models/Organization.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';

class OrganizationController
{
    private $orgModel;

    public function __construct()
    {
        $this->orgModel = new Organization();
    }

    public function index()
    {
        $user = AuthMiddleware::authenticate();
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $stmt = $this->orgModel->readAll();
        $orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Organizations retrieved', $orgs);
    }

    public function updateStatus()
    {
        $user = AuthMiddleware::authenticate();
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id) || !isset($data->status)) {
            Response::json('error', 'Missing parameters', [], 400);
        }

        if ($this->orgModel->updateStatus($data->id, $data->status)) {
            Response::json('success', 'Organization status updated');
        } else {
            Response::json('error', 'Update failed', [], 500);
        }
    }

    public function create()
    {
        $user = AuthMiddleware::authenticate();
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->name) || !isset($data->admin_name) || !isset($data->email) || !isset($data->password)) {
            Response::json('error', 'All fields (Organization Name, Admin Name, Email, Password) are required', [], 400);
        }

        // 1. Create Organization
        try {
            $org_id = $this->orgModel->create($data->name);
            if (!$org_id) {
                Response::json('error', 'Organization name likely exists', [], 409);
                return;
            }

            // 2. Create Org Admin
            $userModel = new User();
            // Assuming User model is available. We need to require it.
            if ($userModel->create($org_id, $data->admin_name, $data->email, $data->password, 'org_admin')) {
                Response::json('success', 'Organization and Admin created successfully');
            } else {
                // Ideally rollback org creation here, but for simplicity
                Response::json('error', 'Organization created but failed to create Admin user', [], 500);
            }

        } catch (Exception $e) {
            Response::json('error', $e->getMessage(), [], 500);
        }
    }

    public function update()
    {
        $user = AuthMiddleware::authenticate();
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id) || !isset($data->name)) {
            Response::json('error', 'ID and Name are required', [], 400);
        }

        if ($this->orgModel->update($data->id, $data->name)) {
            Response::json('success', 'Organization updated successfully');
        } else {
            Response::json('error', 'Update failed', [], 500);
        }
    }

    public function delete()
    {
        $user = AuthMiddleware::authenticate();
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id)) {
            Response::json('error', 'ID is required', [], 400);
        }

        if ($this->orgModel->delete($data->id)) {
            Response::json('success', 'Organization deleted successfully');
        } else {
            Response::json('error', 'Delete failed', [], 500);
        }
    }
}