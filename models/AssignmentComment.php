<?php
require_once("../config.php");
require_once("Model.php");
require_once("AssignmentFile.php");

class AssignmentComment extends Model {
  
  private $ID;
  private $AssignmentFile;
  private $StartLine;
  private $EndLine;
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
              mysql_real_escape_string($this->StartLine) . ", " . 
              mysql_real_escape_string($this->EndLine) . ", '" . 
              mysql_real_escape_string($this->CommentText) . "');";
    try {
      $rows = $this->conn->exec($query);
      if($rows <= 0) {
        echo "FAILED TO SAVE!"; // TODO log this error instead of echo
        return;
      }
      if(!$this->ID) {
        $this->ID = $this->conn->lastInsertId();
      }
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
    }
  }
  
  public function delete() {
    $query = "DELETE FROM " . ASSIGNMENT_COMMENT_TABLE .
              " WHERE ID = " . mysql_real_escape_string($this->ID) . ";";
    try {
      $rows = $this->conn->exec($query);
      if($rows <= 0) {
        echo "RECORD NOT FOUND IN DATABASE!"; // TODO log this error instead of echo
      }
    } catch(PDOException $e) {
      echo $e->getMessage(); // TODO log this error instead of echo
    }
  }
  
  public static function create($AssignmentFile, $StartLine, $EndLine, $CommentText) {
    $instance = new self();
    $instance->fill(array(0, $AssignmentFile, $StartLine, $EndLine, $CommentText));
    return $instance;
  }
  
  /*
  * Load from an id
  */
  public static function load($ID) {
    
    $query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE .
              " WHERE ID=" . mysql_real_escape_string($ID);
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
    $this->setStartLine($row[2]);
    $this->setEndLine($row[3]);
    $this->setCommentText($row[4]);
  }
  
  public function setID($ID) { $this->ID = $ID; }
  public function getID() { return $this->ID; }
  
  public function setAssignmentFile($AssignmentFile) { $this->AssignmentFile = $AssignmentFile; }
  public function getAssignmentFile() { return $this->AssignmentFile; }
  
  public function setStartLine($StartLine) { $this->StartLine = $StartLine; }
  public function getStartLine() { return $this->StartLine; }
  
  public function setEndLine($EndLine) { $this->EndLine = $EndLine; }
  public function getEndLine() { return $this->EndLine; }
  
  public function setCommentText($CommentText) { $this->CommentText = $CommentText; }
  public function getCommentText() { return $CommentText; }
}
?>