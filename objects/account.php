<?php
class Account {
 
    private $conn;
    private $table_name = "accounts";
 
    public $id;
    public $token;
    public $platform;
    public $date_added;
    public $date_changed;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function get() {
        $query = "SELECT T.* FROM " . $this->table_name . " T";

        $fields = array();
        $where = array();

        if ($this->token != null && $this->platform != null) {
            $where[] = "T.token = :token";
            $fields[':token'] = $this->token;           

            $where[] = "T.platform = :platform";
            $fields[':platform'] = $this->platform;
        }

        if ($this->id != null) {
            $where[] = "u.id = :id";
            $fields[':id'] = $this->id;
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
        $this->token = $row['token'];
        $this->platform = $row['platform'];
        $this->date_added = $row['date_added'];
        $this->date_changed = $row['date_changed'];

        return true;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET token=:token, platform=:platform, date_added=:date_added, date_changed=:date_changed";

        $stmt = $this->conn->prepare($query);
     
        $stmt->bindParam(":token", htmlspecialchars(strip_tags($this->token)));
        $stmt->bindParam(":platform", htmlspecialchars(strip_tags($this->platform)));
        $stmt->bindParam(":date_added", htmlspecialchars(strip_tags($this->date_added)));
        $stmt->bindParam(":date_changed", htmlspecialchars(strip_tags($this->date_changed)));
     
        if ($stmt->execute()) {
            return true;
        }
     
        return false;
    }
}