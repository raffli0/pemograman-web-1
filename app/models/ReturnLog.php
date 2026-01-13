<?php
require_once __DIR__ . '/../config/database.php';

class ReturnLog
{
    private $conn;
    private $table_name = "return_logs";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($borrow_id, $date, $note)
    {
        $query = "INSERT INTO " . $this->table_name . " SET borrow_request_id=:bid, return_date=:rdate, condition_note=:note";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":bid", $borrow_id);
        $stmt->bindParam(":rdate", $date);
        $stmt->bindParam(":note", $note);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
