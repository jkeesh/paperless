<?php
require_once("../config.php");
require_once("Model.php");

class AssignmentFile extends Model {
  
  private $ID;
  private $GradedAssignment;
  private $FilePath;
  
  public function __construct() {
    parent::__construct();
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
    $query = "DELETE FROM " . ASSIGNMENT_FILE_TABLE .
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
  
  public static function create($GradedAssignment, $FilePath) {
    $instance = new self();
    $instance->fill(array(0, $GradedAssignment, $FilePath));
    return $instance;
  }
  
  
  /*
  * Load from an id
  */
  public static function load($ID) {
    $query = "SELECT * FROM " . ASSIGNMENT_FILE_TABLE .
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
    $this->setGradedAssignment($row[1]);
    $this->setFilePath($row[2]);
  }
  
  public function setID($ID) { $this->ID = $ID; }
  public function getID() { return $this->ID; }
  
  public function setGradedAssignment($GradedAssignment) { $this->GradedAssignment = $GradedAssignment; }
  public function getGradedAssignment() { return $this->GradedAssignment; }
  
  public function setFilePath($FilePath) { $this->FilePath = $FilePath; }
  public function getFilePath() { return $this->FilePath; }
}
?>