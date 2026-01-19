<?php
require_once __DIR__ . '/../models/BorrowRequest.php';
require_once __DIR__ . '/../models/ReturnLog.php';
require_once __DIR__ . '/../helpers/Response.php';

/**
 * BorrowController
 * Handles all logic for the Asset Borrowing and Returning Workflow.
 * Includes creating requests, administrator approval/rejection, 
 * processing returns with proof, and final verification.
 */
class BorrowController
{
    private $borrow;
    private $returnLog;

    public function __construct()
    {
        $this->borrow = new BorrowRequest();
        $this->returnLog = new ReturnLog();
    }

    /**
     * Lists borrow requests.
     * Members see only their own, Admins see all for their organization.
     */
    public function index()
    {
        global $user;

        $filter_user_id = ($user['role'] === 'member') ? $user['id'] : null;

        $stmt = $this->borrow->getAll($user['org_id'], $filter_user_id);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Response::json('success', 'Requests retrieved', $requests);
    }

    /**
     * Creates a new borrow request.
     * Validates asset availability (status and stock).
     */
    public function create()
    {
        global $user;

        $input = json_decode(file_get_contents("php://input"), true);

        $asset_id = $input['asset_id'] ?? null;
        $purpose = $input['purpose'] ?? 'No purpose specified';
        $quantity = $input['quantity'] ?? 1;
        $start_date = $input['start_date'] ?? date('Y-m-d');
        $end_date = $input['end_date'] ?? date('Y-m-d', strtotime('+7 days'));

        if (!$asset_id) {
            Response::json('error', 'Asset ID required', [], 400);
            return;
        }

        // --- Validation: Asset Status & Ownership ---
        require_once __DIR__ . '/../models/Asset.php';
        $assetModel = new Asset();
        $asset = $assetModel->getById($user['org_id'], $asset_id);

        if (!$asset) {
            Response::json('error', 'Asset not found', [], 404);
            return;
        }

        if ($asset['status'] !== 'active') {
            Response::json('error', 'Asset is currently unavailable (' . $asset['status'] . ')', [], 400);
            return;
        }

        if ($asset['quantity'] < 1) {
            Response::json('error', 'Asset is out of stock', [], 400);
            return;
        }

        // --- Execution ---
        if ($this->borrow->create($user['org_id'], $user['id'], $asset_id, $start_date, $end_date)) {
            // Log Activity
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'BORROW_REQUEST', "Requested: " . $asset['name'] . ". Purpose: " . $purpose);

            Response::json('success', 'Borrow request submitted');
        } else {
            Response::json('error', 'Unable to submit request', [], 503);
        }
    }

    /**
     * Approve a borrow request (Admin only).
     * Updates request status to 'approved' and marks asset as 'in_use'.
     * Decrements stock count.
     */
    public function approve()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }

        $data = json_decode(file_get_contents("php://input"));

        // Validate Request & Ownership
        $req = $this->borrow->getById($user['org_id'], $data->id);
        if (!$req)
            Response::json('error', 'Request not found', [], 404);
        if ($req['status'] !== 'pending')
            Response::json('error', 'Request already processed', [], 400);

        // --- Execution: Stock & Status Update ---
        // Atomic-like check: Decrement only if stock > 0
        if ($this->borrow->decrementAssetStock($user['org_id'], $req['asset_id'])) {
            if ($this->borrow->updateStatus($user['org_id'], $data->id, 'approved')) {
                // Auto-set Status to 'in_use' to reflect active borrowing
                $this->borrow->updateAssetStatus($user['org_id'], $req['asset_id'], 'in_use');

                // Log Activity
                require_once __DIR__ . '/../models/ActivityLog.php';
                $logger = new ActivityLog();
                $logger->log($user['org_id'], $user['id'], 'BORROW_APPROVE', "Approved request for " . $req['asset_id']);

                Response::json('success', 'Request approved');
                return;
            }
            // Note: If updateStatus fails, we have already decremented stock. 
            // In production, this should be wrapped in a Transaction.
        }
        Response::json('error', 'Failed to approve: Out of Stock or System Error', [], 500);
    }

    /**
     * Reject a borrow request (Admin only).
     */
    public function reject()
    {
        global $user;
        if ($user['role'] !== 'org_admin') {
            Response::json('error', 'Unauthorized', [], 403);
        }
        $data = json_decode(file_get_contents("php://input"));

        // Validate Request & Ownership
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

    /**
     * Submit a Return (Member).
     * Includes uploading proof image and adding condition notes.
     */
    public function submitReturn()
    {
        global $user;

        $id = $_POST['id'] ?? null;
        $note = $_POST['condition_note'] ?? null;

        error_log("SubmitReturn Debug: ID=" . var_export($id, true) . " UserID=" . ($user['id'] ?? 'null') . " UserRole=" . ($user['role'] ?? 'null') . " OrgID=" . ($user['org_id'] ?? 'null'));

        // --- Validation ---
        $req = $this->borrow->getById($user['org_id'], $id);
        // Ensure only the borrower can submit the return
        if (!$req || ($user['role'] === 'member' && $req['user_id'] != $user['id'])) {
            Response::json('error', 'Unauthorized or request not found', [], 403);
        }

        if ($req['status'] !== 'approved') {
            Response::json('error', 'Only active borrowings can be returned', [], 400);
        }

        // --- File Upload Handling ---
        $proof_path = null;
        if (isset($_FILES['return_proof']) && $_FILES['return_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/returns/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['return_proof']['name'], PATHINFO_EXTENSION);
            $fileName = 'return_' . $id . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array(strtolower($fileExtension), $allowedTypes)) {
                Response::json('error', 'Invalid file type', [], 400);
                return;
            }

            if (move_uploaded_file($_FILES['return_proof']['tmp_name'], $targetPath)) {
                $proof_path = 'uploads/returns/' . $fileName;
            } else {
                error_log("Failed to move uploaded file to " . $targetPath);
            }
        }

        // --- Execution ---
        if ($this->borrow->updateStatus($user['org_id'], $id, 'returning')) {
            // Save proof and notes
            $this->borrow->updateReturnDetails($user['org_id'], $id, $proof_path, $note);

            // Log Action and Create Return Record
            require_once __DIR__ . '/../models/ActivityLog.php';
            $logger = new ActivityLog();
            $logger->log($user['org_id'], $user['id'], 'RETURN_SUBMIT', "Member submitted return for Request ID: " . $id . ". Note: " . ($note ?? 'None'));

            $this->returnLog->create($id, date('Y-m-d'), $note ?? 'Member reported return');

            Response::json('success', 'Return submitted for verification');
            return;
        }
        Response::json('error', 'Failed to submit return', [], 500);
    }

    /**
     * Verify Return (Admin).
     * Finalizes the return process, restocks the asset, and sets it back to active.
     */
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

        // --- Execution ---
        if ($this->borrow->updateStatus($user['org_id'], $data->id, 'returned')) {
            // Restock Asset
            $this->borrow->incrementAssetStock($user['org_id'], $req['asset_id']);

            // Set Asset Status back to 'active' (Available)
            $this->borrow->updateAssetStatus($user['org_id'], $req['asset_id'], 'active');

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
