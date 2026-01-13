<?php
require_once __DIR__ . '/../models/Asset.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../helpers/Response.php';

class AssetController
{
    private $asset;
    private $logger;

    public function __construct()
    {
        $this->asset = new Asset();
        $this->logger = new ActivityLog();
    }

    public function index()
    {
        global $user;
        if (!$user['org_id']) {
            Response::json('success', 'Super Admin view not implemented for assets', []);
            return;
        }

        $stmt = $this->asset->readAll($user['org_id']);
        $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Assets retrieved', $assets);
    }

    public function store()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));

        $newId = $this->asset->create($user['org_id'], $data->name, $data->description, $data->quantity, $data->condition_note);
        if ($newId) {
            $this->logger->log($user['org_id'], $user['id'], 'ASSET_CREATE', "Created asset: {$data->name}");
            Response::json('success', 'Asset created');
        } else {
            Response::json('error', 'Unable to create asset', [], 503);
        }
    }

    public function update()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));
        $status = isset($data->status) ? $data->status : 'active';

        if ($this->asset->update($user['org_id'], $data->id, $data->name, $data->description, $data->quantity, $data->condition_note, $status)) {
            $this->logger->log($user['org_id'], $user['id'], 'ASSET_UPDATE', "Updated asset: {$data->name} (Status: $status)");
            Response::json('success', 'Asset updated');
        } else {
            Response::json('error', 'Unable to update asset', [], 503);
        }
    }

    public function delete()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));

        if ($this->asset->delete($user['org_id'], $data->id)) {
            $this->logger->log($user['org_id'], $user['id'], 'ASSET_DELETE', "Deleted asset ID: {$data->id}");
            Response::json('success', 'Asset deleted');
        } else {
            Response::json('error', 'Unable to delete asset', [], 503);
        }
    }
}
