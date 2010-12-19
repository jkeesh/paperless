<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class AssignmentFile extends Model {
  
  public $ID;
  public $GradedAssignment;
  public $FilePath;
  public $AssignmentComments;
  
  public function __construct() {
    parent::__construct();
    $AssignmentComments = array( );
  }
  
  /*
  * Saves the current assignment files's state to 
  * the database.
  */
  public function save() {
    $query = "REPLACE INTO " . ASSIGNMENT_FILE_TABLE . 
              " VALUES(" . mysql_real_escape_string($this->ID) . ", '" . 
              mysql_real_escape_string($this->GradedAssignment) . "', '" . 
              mysql_real_escape_string($this->FilePath) . "');";

    try {
      $this->conn->exec($query);
      if(!$this->ID) {
        $this->ID = $this->conn->lastInsertId();
      }
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
    }
  }
  
  public function delete() {
    // delete the file object
    $query = "DELETE FROM " . ASSIGNMENT_FILE_TABLE .
              " WHERE ID = " . mysql_real_escape_string($this->ID) . ";";
    // delete the comments associated with this object
    $queryComments = "DELETE FROM " . ASSIGNMENT_COMMENT_TABLE . 
                      " WHERE AssignmentFile=" . mysql_real_escape_string($this->ID) . ";";
    try {
      $rows = $this->conn->exec($query);
      if($rows <= 0) {
        echo "RECORD NOT FOUND IN DATABASE!"; // TODO log this error instead of echo
        return;
      }
      $rows = $this->conn->exec($queryComments);
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
    }
  }
  
  public static function create($GradedAssignment, $FilePath) {
    $instance = new self();
    $instance->fill(array(0, $GradedAssignment, $FilePath));
    return $instance;
  }
  
  public function loadComments() {
    $query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE . " WHERE AssignmentFile=" . $this->ID;
    $sth = $this->conn->query($query);
    $sth->setFetchMode(PDO::FETCH_ASSOC);
    $ids = array();
    while($row = $sth->fetch()) {
      $curComment = AssignmentComment::create($row['AssignmentFile'], $row['StartLine'], $row['EndLine'], $row['CommentText']);
      $curComment->setID($row['ID']);
      $this->AssignmentComments[] = $curComment;
    }
  }
  
  
  /*
  * Load from an id
  */
  public static function load($args) {
    extract($args);
    
    $where = null;
    if(isset($ID)) $where = "ID=".mysql_real_escape_string($ID);
    if(isset($FilePath)) $where = "File='".mysql_real_escape_string($FilePath)."'";
    if($where == null) return null;
    
    $query = "SELECT * FROM " . ASSIGNMENT_FILE_TABLE . " WHERE $where;";
    $instance = new self();
    $sth = $instance->conn->query($query);

    $sth->setFetchMode(PDO::FETCH_NUM);
    if($row = $sth->fetch()) {
      $instance->fill($row);
      $instance->loadComments();
      return $instance;
    }
    return null;
  }
  
  /*
  * Getters and Setters
  */
  
  public function fill(array $row) { 
    $this->setID($row[0]);
    $this->setGradedAssignment($row[1]);
    $this->setFilePath($row[2]);
  }
  
  public function setID($ID) { $this->ID = $ID; }
  public function getID() { return $this->ID; }
  
  public function setGradedAssignment($GradedAssignment) { $this->GradedAssignment = $GradedAssignment; }
  public function getGradedAssignment() { return $this->GradedAssignment; }
  
  public function setFilePath($FilePath) { $this->FilePath = $FilePath; }
  public function getFilePath() { return $this->FilePath; }
  
  public function getAssignmentComments() { return $this->AssignmentComments; }
}
?>