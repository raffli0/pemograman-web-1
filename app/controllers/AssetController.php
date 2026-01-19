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

    public function create()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
            return;
        }

        $input = file_get_contents("php://input");
        $data = json_decode($input);

        if (!$data) {
            Response::json('error', 'Invalid JSON input', [], 400);
            return;
        }

        // Auto-generate Asset Code (AST-YYYYMMDD-XXXX)
        $code = 'AST-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $category = isset($data->category) ? $data->category : null;
        $location = isset($data->location) ? $data->location : null;
        // Fix duplication: condition usually comes from form, if not, use empty string or description? 
        // Form doesn't seem to send 'condition', it sends 'description' as main description.
        // Asset.php expects condition as arg 5.
        // Let's use description for condition if available, or just empty.
        // Looking at asset.js, it collects description.
        // Asset Table has 'description' AND 'condition_note'.
        // We will map description to description, and leave condition_note empty for now or same.
        $condition = isset($data->condition) ? $data->condition : '';

        try {
            $newId = $this->asset->create($user['org_id'], $data->name, $data->description, $data->quantity, $condition, $location, $code, $category);
            if ($newId) {
                $this->logger->log($user['org_id'], $user['id'], 'ASSET_CREATE', "Created asset: {$data->name} ({$code})");
                Response::json('success', 'Asset created');
            } else {
                Response::json('error', 'Database insert failed (check logs)', [], 503);
            }
        } catch (Exception $e) {
            Response::json('error', 'Exception: ' . $e->getMessage(), [], 500);
        }
    }

    public function update()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
            return;
        }

        $input = file_get_contents("php://input");
        $data = json_decode($input);

        if (!$data || !isset($data->id)) {
            // Fallback: Check if ID is in URL (for cached frontend clients)
            if (preg_match('/\/asset\/update\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
                if (!$data)
                    $data = new stdClass();
                $data->id = $matches[1];
            } else {
                Response::json('error', 'Invalid JSON input or missing ID. Input: ' . $input, [], 400);
                return;
            }
        }

        $status = isset($data->status) ? $data->status : 'active';
        $code = isset($data->code) ? $data->code : null; // Usually preserves existing if null
        $category = isset($data->category) ? $data->category : null;
        $location = isset($data->location) ? $data->location : null;
        $desc = isset($data->description) ? $data->description : '';

        try {
            if ($this->asset->update($user['org_id'], $data->id, $data->name, $desc, $data->quantity, $desc, $status, $location, $code, $category)) {
                $this->logger->log($user['org_id'], $user['id'], 'ASSET_UPDATE', "Updated asset: {$data->name}");
                Response::json('success', 'Asset updated');
            } else {
                Response::json('error', 'Unable to update asset', [], 503);
            }
        } catch (Exception $e) {
            Response::json('error', 'Exception: ' . $e->getMessage(), [], 500);
        }
    }

    public function delete()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
            return;
        }

        $input = file_get_contents("php://input");
        $data = json_decode($input);

        if (!$data || !isset($data->id)) {
            Response::json('error', 'Invalid JSON input or missing ID', [], 400);
            return;
        }

        try {
            if ($this->asset->delete($user['org_id'], $data->id)) {
                $this->logger->log($user['org_id'], $user['id'], 'ASSET_DELETE', "Deleted asset ID: {$data->id}");
                Response::json('success', 'Asset deleted');
            } else {
                Response::json('error', 'Unable to delete asset (Database error)', [], 503);
            }
        } catch (Exception $e) {
            Response::json('error', 'Exception: ' . $e->getMessage(), [], 500);
        }
    }
}
