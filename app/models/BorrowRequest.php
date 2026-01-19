<?php
require_once __DIR__ . '/../config/database.php';

class BorrowRequest
{
    private $conn;
    private $table_name = "borrow_requests";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($organization_id, $user_id, $asset_id, $start_date, $end_date)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET organization_id=:org_id, user_id=:user_id, asset_id=:asset_id, start_date=:start_date, end_date=:end_date, status='pending'";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":org_id", $organization_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":asset_id", $asset_id);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll($organization_id, $user_id = null)
    {
        $query = "SELECT b.*, u.name as user_name, a.name as asset_name 
                  FROM " . $this->table_name . " b
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN assets a ON b.asset_id = a.id
                  WHERE b.organization_id = :org_id";

        if ($user_id) {
            $query .= " AND b.user_id = :user_id";
        }

        $query .= " ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $organization_id);

        if ($user_id) {
            $stmt->bindParam(":user_id", $user_id);
        }

        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($organization_id, $id, $status)
    {
        // Enforce org_id
        $query = "UPDATE " . $this->table_name . " SET status=:status WHERE id=:id AND organization_id=:org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function decrementAssetStock($organization_id, $asset_id)
    {
        $query = "UPDATE assets SET quantity = quantity - 1 WHERE id = :id AND organization_id = :org_id AND quantity > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $asset_id);
        $stmt->bindParam(":org_id", $organization_id);
        return $stmt->execute();
    }

    public function incrementAssetStock($organization_id, $asset_id)
    {
        $query = "UPDATE assets SET quantity = quantity + 1 WHERE id = :id AND organization_id = :org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $asset_id);
        $stmt->bindParam(":org_id", $organization_id);
        return $stmt->execute();
    }

    public function getById($organization_id, $id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND organization_id = :org_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAssetStatus($organization_id, $asset_id, $status)
    {
        $query = "UPDATE assets SET status = :status WHERE id = :id AND organization_id = :org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $asset_id);
        $stmt->bindParam(":org_id", $organization_id);
        return $stmt->execute();
    }

    public function updateReturnDetails($organization_id, $id, $path, $note)
    {
        $query = "UPDATE " . $this->table_name . " SET return_proof_image = :path, return_note = :note WHERE id = :id AND organization_id = :org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":path", $path);
        $stmt->bindParam(":note", $note);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);
        return $stmt->execute();
    }
}
