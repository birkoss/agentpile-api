<?php
class Session {
 
    private $conn;
    private $table_name = "sessions";
 
    public $id;
    public $user_id;
    public $archive_id;

    public $date_read;
    public $date_added;
    public $date_changed;
    public $date_deleted;

    public $bookName;
    public $bookAuthor;

    public $isCompleted = false;

    public $pageBookmark;
 
    public function __construct($db){
        $this->conn = $db;
    }

    function get() {
        $query = "SELECT T.* FROM " . $this->table_name . " T";

        $where = array();
        $fields = array();

        if ($this->id != null) {
            $where[] = "T.id = :id";
            $fields[':id'] = $this->id;
        }

        if ($this->user_id != null) {
            $where[] = "T.user_id = :user_id";
            $fields[':user_id'] = $this->user_id;
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
        $this->archive_id = $row['archive_id'];

        $this->date_read = $row['date_read'];
        $this->date_added = $row['date_added'];
        $this->date_changed = $row['date_changed'];
        $this->date_deleted = $row['date_deleted'];
        
        $this->isCompleted = ($row['isCompleted'] == 1 ? true : false);
        
        $this->bookName = $row['bookName'];
        $this->bookAuthor = $row['bookAuthor'];
        $this->pageBookmark = $row['pageBookmark'];

        return true;
    }

    function create() {
        $fields = array(
            "date_read=:date_read",
            "user_id=:user_id",
            "date_added=:date_added",
            "date_changed=:date_changed"
        );

        $values = array(
            ":date_read" => $this->date_read,
            ":user_id" => $this->user_id,
            ":date_added" => $this->date_added,
            ":date_changed" => $this->date_changed
        );

        $query = "INSERT INTO " . $this->table_name . " SET ".implode(", ", $fields);

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute($values)) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
     
        return false;
    }

    function update() {
        $fields = array(
            "isCompleted=:isCompleted",
            "pageBookmark=:pageBookmark",
            "bookName=:bookName",
            "bookAuthor=:bookAuthor",
            "date_changed = :date_changed",
            "date_deleted = :date_deleted"
        );

        $values = array(
            ":isCompleted" => $this->isCompleted,
            ":pageBookmark" => $this->pageBookmark,
            ":bookName" => $this->bookName,
            ":bookAuthor" => $this->bookAuthor,
            ":date_deleted" => $this->date_deleted,
            ":date_changed" => date('Y-m-d H:i:s'),
          
            ":user_id" => $this->user_id,
            ":id" => $this->id
        );

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE user_id=:user_id AND id=:id";

        $stmt = $this->conn->prepare($query);
     
        if ($stmt->execute($values)) {
            $this->date_changed = $values[':date_changed'];
            return true;
        }
     
        return false;
    }
}