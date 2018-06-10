<?php
class User {
 
    private $conn;
    private $table_name = "users";
 
    public $id;
    public $account_id;
    public $date_added;
    public $date_changed;
    public $date_deleted;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function get() {
        $query = "SELECT T.* FROM " . $this->table_name . " T";

        $where = array();
        $fields = array();

        if ($this->id != null) {
            $where[] = "T.id = :id";
            $fields[':id'] = $this->uid;
        }

        if (count($where) > 0) {
            $query .= " WHERE " .implode(" AND ", $where) . " ";
        }

        $query .= " LIMIT 0,1";
     
        $stmt = $this->conn->prepare($query);
     
        foreach ($fields as $id => $value) {
            $stmt->bindParam($id, $value);
        }
     
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
     
        $this->id = $row['id'];
        $this->account_id = $row['account_id'];
        $this->date_added = $row['date_added'];
        $this->date_changed = $row['date_changed'];
        $this->date_deleted = $row['date_deleted'];

        return true;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET account_id=:account_id, date_added=:date_added, date_changed=:date_changed";

        $stmt = $this->conn->prepare($query);
     
        $stmt->bindParam(":account_id", htmlspecialchars(strip_tags($this->account_id)));
        $stmt->bindParam(":date_added", htmlspecialchars(strip_tags($this->date_added)));
        $stmt->bindParam(":date_changed", htmlspecialchars(strip_tags($this->date_changed)));
     
        if ($stmt->execute()) {
            return true;
        }
     
        return false;
    }
}