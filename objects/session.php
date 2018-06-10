<?php
class Session {
 
    private $conn;
    private $table_name = "sessions";
 
    public $id;
    public $user_id;
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
     
        foreach ($fields as $id => $value) {
            $stmt->bindParam($id, $value);
        }
     
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
     
        $this->id = $row['id'];
        $this->user_id = $row['user_id'];
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
        $query = "INSERT INTO " . $this->table_name . " SET date_read=:date_red, user_id=:user_id, date_added=:date_added, date_changed=:date_changed";

        $stmt = $this->conn->prepare($query);
     
        $stmt->bindParam(":date_read", htmlspecialchars(strip_tags($this->date_read)));
        $stmt->bindParam(":account_id", htmlspecialchars(strip_tags($this->account_id)));
        $stmt->bindParam(":date_added", htmlspecialchars(strip_tags($this->date_added)));
        $stmt->bindParam(":date_changed", htmlspecialchars(strip_tags($this->date_changed)));
     
        if ($stmt->execute()) {
            return true;
        }
     
        return false;
    }
}