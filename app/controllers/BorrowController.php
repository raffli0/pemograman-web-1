<?php
require_once __DIR__ . '/../models/BorrowRequest.php';
require_once __DIR__ . '/../models/ReturnLog.php';
require_once __DIR__ . '/../helpers/Response.php';

class BorrowController
{
    private $borrow;
    private $returnLog;

    public function __construct()
    {
        $this->borrow = new BorrowRequest();
        $this->returnLog = new ReturnLog();
    }

    public function index()
    {
        global $user;

        $filter_user_id = ($user['role'] === 'member') ? $user['id'] : null;

        $stmt = $this->borrow->getAll($user['org_id'], $filter_user_id);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Requests retrieved', $requests);
    }

    public function request()
    {
        global $user;
        $data = json_decode(file_get_contents("php://input"));

        // Refactor: Validate Asset Status & Ownership First
        require_once __DIR__ . '/../models/Asset.php';
        $assetModel = new Asset();
        $asset = $assetModel->getById($user['org_id'], $data->asset_id);

        if (!$asset) {
            Response::json('error', 'Asset not found', [], 404);
        }

        if ($asset['status'] !== 'active') {
            Response::json('error', 'Asset is currently unavailable (' . $asset['status'] . ')', [], 400);
        }

        // Optional: Check Stock at request time (though approval is the real reservation)
        if ($asset['quantity'] < 1) {
            Response::json('error', 'Asset is out of stock', [], 400);
        }

        if ($this->borrow->create($user['org_id'], $user['id'], $data->asset_id, $data->start_date, $data->end_date)) {
            // Log Action
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'BORROW_REQUEST', "Requested: " . $asset['name']);

            Response::json('success', 'Borrow request submitted');
        } else {
            Response::json('error', 'Unable to submit request', [], 503);
        }
    }

    public function approve()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));

        // Check request ownership via org_id
        $req = $this->borrow->getById($user['org_id'], $data->id);
        if (!$req)
            Response::json('error', 'Request not found', [], 404);
        if ($req['status'] !== 'pending')
            Response::json('error', 'Request already processed', [], 400);

        // Atomic-like check: Decrement only if stock > 0
        if ($this->borrow->decrementAssetStock($user['org_id'], $req['asset_id'])) {
            if ($this->borrow->updateStatus($user['org_id'], $data->id, 'approved')) {
                // Log Action
                require_once __DIR__ . '/../models/ActivityLog.php';
                $logger = new ActivityLog();
                $logger->log($user['org_id'], $user['id'], 'BORROW_APPROVE', "Approved request for " . $req['asset_id']); // Ideally fetch name

                Response::json('success', 'Request approved');
                return;
            }
            // Rollback if status update fails? (In simple PHP/MySQL without Transaction blocks, complex. We assume success)
        }
        Response::json('error', 'Failed to approve: Out of Stock or System Error', [], 500);
    }

    public function reject()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));

        // Ownership check
        $req = $this->borrow->getById($user['org_id'], $data->id);
        if (!$req)
            Response::json('error', 'Request not found', [], 404);

        if ($this->borrow->updateStatus($user['org_id'], $data->id, 'rejected')) {
            // Log Action
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'BORROW_REJECT', "Rejected request ID: " . $data->id);

            Response::json('success', 'Request rejected');
        } else {
            Response::json('error', 'Failed to reject', [], 500);
        }
    }

    public function returnAsset()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));

        $req = $this->borrow->getById($user['org_id'], $data->id);
        if ($req && $req['status'] == 'approved') {
            if ($this->borrow->updateStatus($user['org_id'], $data->id, 'returned')) {
                $this->borrow->incrementAssetStock($user['org_id'], $req['asset_id']);
                $this->returnLog->create($data->id, date('Y-m-d'), $data->condition_note);

                // Log Action
                require_once __DIR__ . '/../models/ActivityLog.php';
                $logger = new ActivityLog();
                $logger->log($user['org_id'], $user['id'], 'BORROW_RETURN', "Returned ID: " . $data->id . ". Cond: " . $data->condition_note);

                Response::json('success', 'Asset returned successfully');
                return;
            }
        }
        Response::json('error', 'Failed to return asset', [], 500);
    }
}
