<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class AssignmentFile extends Model {
  
  private $ID;
  private $GradedAssignment;
  private $FilePath;
  private $AssignmentComments;
  
  public function __construct() {
    parent::__construct();
    $this->AssignmentComments = array( );
  }
  
  /*
  * Saves the current assignment files's state to 
  * the database.
  */
  public function save() {
    $query = "REPLACE INTO " . ASSIGNMENT_FILE_TABLE . 
              " VALUES(:ID, :GradedAssignment, :File);";

    try {
      $sth = $this->conn->prepare($query);
      $rows = $sth->execute(array(":ID" => $this->ID,
                                  ":GradedAssignment" => $this->GradedAssignment,
                                  ":File" => $this->FilePath));
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
              " WHERE ID=:ID;";
    // delete the comments associated with this object
    $queryComments = "DELETE FROM " . ASSIGNMENT_COMMENT_TABLE . 
                      " WHERE AssignmentFile=:ID;";
    try {
      $sth = $this->conn->prepare($query);
      $sth->execute(array(":ID" => $this->ID));
      
      $sth = $this->conn->prepare($queryComments);
      $sth->execute(array(":ID" => $this->ID));
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
    $query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE . " WHERE AssignmentFile=:AssignmentFile";
    try {
      $sth = $this->conn->prepare($query);
      $sth->execute(array(":AssignmentFile" => $this->ID));
      
      $sth->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $sth->fetch()) {
        $curComment = AssignmentComment::create($row['AssignmentFile'], $row['StartLine'], $row['EndLine'], $row['CommentText']);
        $curComment->setID($row['ID']);
        $this->AssignmentComments[] = $curComment;
      }
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
    }
  }
  
  /*
  * Load from an id
  */
  public static function load($args) {
    extract($args);
    
    $query = "SELECT * FROM " . ASSIGNMENT_FILE_TABLE . " WHERE File=:FilePath;";
    $instance = new self();
    
    try {
      $sth = $instance->conn->prepare($query);
      $sth->execute(array(":FilePath" => $FilePath));
      
      $sth->setFetchMode(PDO::FETCH_NUM);
      if($row = $sth->fetch()) {
        $instance->fill($row);
        $instance->loadComments();
        return $instance;
      }
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
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