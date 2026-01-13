<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../helpers/Response.php';

class DashboardController
{
    private $conn;
    private $logger;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new ActivityLog();
    }

    public function getStats()
    {
        global $user;
        // Basic role check
        if (!$user['org_id']) {
            Response::json('success', 'Stats', ['total_assets' => 0]);
            return;
        }

        $org_id = $user['org_id'];

        // 1. Total Assets
        $query = "SELECT COUNT(*) as count FROM assets WHERE organization_id = :org_id AND status='active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $org_id);
        $stmt->execute();
        $assets = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 2. Active Borrows (Approved but not Returned)
        $query = "SELECT COUNT(*) as count FROM borrow_requests WHERE organization_id = :org_id AND status='approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $org_id);
        $stmt->execute();
        $active_borrows = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 3. Pending Requests
        $query = "SELECT COUNT(*) as count FROM borrow_requests WHERE organization_id = :org_id AND status='pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $org_id);
        $stmt->execute();
        $pending = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 4. Activity Logs (Recent 5)
        $logs = [];
        if ($user['role'] == 'org_admin') {
            $stmt = $this->logger->getLogs($org_id, 5);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        Response::json('success', 'Dashboard stats', [
            'total_assets' => $assets,
            'active_borrows' => $active_borrows,
            'pending_requests' => $pending,
            'logs' => $logs
        ]);
    }
}
