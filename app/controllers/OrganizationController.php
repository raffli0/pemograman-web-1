<?php
require_once __DIR__ . '/../models/Organization.php';
require_once __DIR__ . '/../helpers/Response.php';

class OrganizationController
{
    private $orgModel;

    public function __construct()
    {
        $this->orgModel = new Organization();
    }

    public function index()
    {
        global $user;
        if ($user['role'] !== 'super_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $stmt = $this->orgModel->readAll();
        $orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Organizations retrieved', $orgs);
    }

    public function updateStatus()
    {
        global $user;
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
}
