<?php
class Archive {
 
    private $conn;
    private $table_name = "archives";
 
    public $id;
    public $user_id;
    public $date_added;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function get() {
        $query = "SELECT T.* FROM " . $this->table_name . " T";

        $where = array();
        $fields = array();

        if ($this->id != null && $this->user_id != null) {
            $where[] = "T.id = :id";
            $fields[':id'] = $this->id;

            $where[] = "T.user_id = :user_id";
            $fields[':user_id'] = $this->user_id;
        } else {
            return false;
        }

        if (count($where) > 0) {
            $query .= " WHERE " .implode(" AND ", $where) . " ";
        }

        $query .= " LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
     
        $stmt->execute($fields);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
     
        $this->id = $row['id'];
        $this->user_id = $row['user_id'];
        $this->date_added = $row['date_added'];

        return true;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, date_added=:date_added";
        $values = array(
	        ":user_id" => $this->user_id,
	        ":date_added" => $this->date_added
	    );

        $stmt = $this->conn->prepare($query);
     
        if ($stmt->execute($values)) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
     
        return false;
    }
}