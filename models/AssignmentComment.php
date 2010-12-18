<?php
require_once("../config.php");
require_once("Model.php");
require_once("AssignmentFile.php");

class AssignmentComment extends Model {
  
  private $ID;
  private $AssignmentFile;
  private $LineNumber;
  private $CommentText;
  
  public function __construct() {
    parent::__construct();
  }
  
  /*
  * Saves the current assignment comment's state to 
  * the database.
  */
  public function save() {
    $query = "REPLACE INTO " . ASSIGNMENT_COMMENT_TABLE . 
              " VALUES(" . mysql_real_escape_string($this->ID) . ", " . 
              mysql_real_escape_string($this->AssignmentFile) . ", " . 
              mysql_real_escape_string($this->LineNumber) . ", '" . 
              mysql_real_escape_string($this->CommentText) . "');";
    try {
      $this->conn->exec($query);
      if(!$this->ID) {
        $this->ID = $this->conn->lastInsertId();
      }
    } catch(PDOException $e) {
      echo $e->getMessage();
    }
  }
  
  public static function create($AssignmentFile, $LineNumber, $CommentText) {
    $instance = new self();
    $instance->fill(array(0, $AssignmentFile, $LineNumber, $CommentText));
    return $instance;
  }
  
  /*
  * Load from an id
  */
  public static function load($ID) {
    $query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE .
              " WHERE ID = " . mysql_real_escape_string($ID) . ";";
    $instance = new self();
    $sth = $instance->conn->query($query);

    $sth->setFetchMode(PDO::FETCH_NUM);
    if($row = $sth->fetch()) {
      $instance->fill($row);
      return $instance;
    }
    return null;
  }
  
  /*
  * Getters and Setters
  */
  
  public function fill(array $row) { 
    $this->setID($row[0]);
    $this->setAssignmentFile($row[1]);
    $this->setLineNumber($row[2]);
    $this->setCommentText($row[3]);
  }
  
  public function setID($ID) { $this->ID = $ID; }
  public function getID() { return $this->ID; }
  
  public function setAssignmentFile($AssignmentFile) { $this->AssignmentFile = $AssignmentFile; }
  public function getAssignmentFile() { return AssignmentFile::load($this->AssignmentFile); }
  
  public function setLineNumber($LineNumber) { $this->LineNumber = $LineNumber; }
  public function getLineNumber() { return $this->LineNumber; }
  
  public function setCommentText($CommentText) { $this->CommentText = $CommentText; }
  public function getCommentText() { return $CommentText; }
}
?>