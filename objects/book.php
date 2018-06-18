<?php
class Book {
 
    private $conn;
    private $table_name = "books";
 
    public $id;
    public $account_id;

    public $name;
    public $author;

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

        if ($this->id != null && $this->account_id != null) {
            $where[] = "T.id = :id";
            $fields[':id'] = $this->id;

            $where[] = "T.account_id = :account_id";
            $fields[':account_id'] = $this->account_id;
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
        $this->account_id = $row['account_id'];
        $this->name = $row['name'];
        $this->author = $row['author'];
        $this->date_added = $row['date_added'];
        $this->date_changed = $row['date_changed'];
        $this->date_deleted = $row['date_deleted'];

        return true;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET account_id=:account_id, date_added=:date_added, date_changed=:date_changed";
        $values = array(
            ":account_id" => $this->account_id,
            ":date_added" => $this->date_added,
	        ":date_changed" => $this->date_changed
	    );

        $stmt = $this->conn->prepare($query);
     
        if ($stmt->execute($values)) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
     
        return false;
    }

    function update() {
        $fields = array(
            "name=:name",
            "author=:author",
            "date_changed = :date_changed",
            "date_deleted = :date_deleted"
        );

        $values = array(
            ":name" => $this->name,
            ":author" => $this->author,
            ":date_deleted" => $this->date_deleted,
            ":date_changed" => date('Y-m-d H:i:s'),
          
            ":account_id" => $this->account_id,
            ":id" => $this->id
        );

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE account_id=:account_id AND id=:id";

        $stmt = $this->conn->prepare($query);
     
        if ($stmt->execute($values)) {
            $this->date_changed = $values[':date_changed'];
            return true;
        }
     
        return false;
    }
}