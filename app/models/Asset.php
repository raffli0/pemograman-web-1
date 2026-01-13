<?php
require_once __DIR__ . '/../config/database.php';

class Asset
{
    private $conn;
    private $table_name = "assets";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function readAll($organization_id)
    {
        // Now returns active/maintenance/lost assets
        $query = "SELECT * FROM " . $this->table_name . " WHERE organization_id = :org_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $organization_id);
        $stmt->execute();
        return $stmt;
    }

    public function create($organization_id, $name, $description, $quantity, $condition)
    {
        $query = "INSERT INTO " . $this->table_name . " SET organization_id=:org_id, name=:name, description=:description, quantity=:quantity, condition_note=:condition, status='active'";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":org_id", $organization_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":condition", $condition);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return ID for logging
        }
        return false;
    }

    public function update($organization_id, $id, $name, $description, $quantity, $condition, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET name=:name, description=:description, quantity=:quantity, condition_note=:condition, status=:status WHERE id=:id AND organization_id=:org_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":condition", $condition);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);

        return $stmt->execute();
    }

    public function delete($organization_id, $id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND organization_id = :org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);

        return $stmt->execute();
    }

    public function getById($organization_id, $id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND organization_id = :org_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":org_id", $organization_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
