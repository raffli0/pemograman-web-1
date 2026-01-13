<?php
require_once __DIR__ . '/../config/database.php';

class ActivityLog
{
    private $conn;
    private $table_name = "activity_logs";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function log($org_id, $user_id, $action, $details)
    {
        $query = "INSERT INTO " . $this->table_name . " SET organization_id=:org_id, user_id=:user_id, action=:action, details=:details";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":org_id", $org_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":details", $details);

        return $stmt->execute();
    }

    public function getLogs($org_id, $limit = 20)
    {
        $query = "SELECT al.*, u.name as user_name 
                  FROM " . $this->table_name . " al
                  LEFT JOIN users u ON al.user_id = u.id
                  WHERE al.organization_id = :org_id
                  ORDER BY al.created_at DESC
                  LIMIT " . intval($limit);

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $org_id);
        $stmt->execute();
        return $stmt;
    }
}
