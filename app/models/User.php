<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $organization_id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $password)
    {
        $query = "SELECT id, organization_id, name, email, password, role FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->organization_id = $row['organization_id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }

    public function create($organization_id, $name, $email, $password, $role)
    {
        $query = "INSERT INTO " . $this->table_name . " SET organization_id=:org_id, name=:name, email=:email, password=:password, role=:role";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindParam(":org_id", $organization_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // For Org Admin to list members
    public function getByOrganization($org_id)
    {
        $query = "SELECT id, name, email, role, created_at FROM " . $this->table_name . " WHERE organization_id = :org_id ORDER BY role ASC, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":org_id", $org_id);
        $stmt->execute();
        return $stmt;
    }
}
