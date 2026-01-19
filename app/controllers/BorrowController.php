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

    public function create()
    {
        global $user;
        $data = json_decode(file_get_contents("php://input"));

        // Refactor: Validate Asset Status & Ownership First
        require_once __DIR__ . '/../models/Asset.php';
        $assetModel = new Asset();
        $asset = $assetModel->getById($user['org_id'], $data->asset_id);

        if (!$asset) {
            Response::json('error', 'Asset not found', [], 404);
            return;
        }

        if ($asset['status'] !== 'active') {
            Response::json('error', 'Asset is currently unavailable (' . $asset['status'] . ')', [], 400);
            return;
        }

        // Optional: Check Stock at request time (though approval is the real reservation)
        if ($asset['quantity'] < 1) {
            Response::json('error', 'Asset is out of stock', [], 400);
            return;
        }

        // Set defaults for missing frontend fields
        $start_date = isset($data->start_date) ? $data->start_date : date('Y-m-d');
        $end_date = isset($data->end_date) ? $data->end_date : date('Y-m-d', strtotime('+7 days'));

        // Note: Quantity is sent by frontend but not supported in DB schema yet. We assume 1 unit per request.

        if ($this->borrow->create($user['org_id'], $user['id'], $data->asset_id, $start_date, $end_date)) {
            // Log Action
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $purpose = isset($data->purpose) ? $data->purpose : 'No purpose specified';
            $logger->log($user['org_id'], $user['id'], 'BORROW_REQUEST', "Requested: " . $asset['name'] . ". Purpose: " . $purpose);

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

    public function submitReturn()
    {
        global $user;
        $data = json_decode(file_get_contents("php://input"));

        // Ownership check: only the borrower can submit a return
        $req = $this->borrow->getById($user['org_id'], $data->id);
        if (!$req || ($user['role'] === 'member' && $req['user_id'] != $user['id'])) {
            Response::json('error', 'Unauthorized or request not found', [], 403);
        }

        if ($req['status'] !== 'approved') {
            Response::json('error', 'Only active borrowings can be returned', [], 400);
        }

        if ($this->borrow->updateStatus($user['org_id'], $data->id, 'returning')) {
            // Log Action
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'RETURN_SUBMIT', "Member submitted return for Request ID: " . $data->id . ". Note: " . ($data->condition_note ?? 'None'));

            // Store the note temporarily in the return_logs or similar? 
            // For now, let's just create the log entry.
            $this->returnLog->create($data->id, date('Y-m-d'), $data->condition_note ?? 'Member reported return');

            Response::json('success', 'Return submitted for verification');
            return;
        }
        Response::json('error', 'Failed to submit return', [], 500);
    }

    public function verifyReturn()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));

        $req = $this->borrow->getById($user['org_id'], $data->id);
        if (!$req) {
            Response::json('error', 'Request not found', [], 404);
        }

        if ($req['status'] !== 'returning') {
            Response::json('error', 'Request must be in returning state for verification', [], 400);
        }

        if ($this->borrow->updateStatus($user['org_id'], $data->id, 'returned')) {
            $this->borrow->incrementAssetStock($user['org_id'], $req['asset_id']);

            // Log Action
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'RETURN_VERIFY', "Admin verified return for Request ID: " . $data->id . ". Final Note: " . ($data->admin_note ?? 'Verified'));

            Response::json('success', 'Return verified and asset restocked');
            return;
        }
        Response::json('error', 'Failed to verify return', [], 500);
    }
}
