<?php
require_once("../config.php");
require_once("Model.php");

class AssignmentComment extends Model {
  
  private $ID;
  private $AssignmentFile;
  private $LineNumber;
  private $CommentText;
  
  public function __construct($ID, $AssignmentFile, $LineNumber, $CommentText) {
    parent::__construct();
    $this->ID = $ID;
    $this->AssignmentFile = $AssignmentFile;
    $this->LineNumber = $LineNumber;
    $this->CommentText = $CommentText;
  }
  
  public function save() {
    $query = "REPLACE INTO " . ASSIGNMENT_COMMENT_TABLE . 
              " VALUES(" . $this->ID . ", " . 
              $this->AssignmentFile . ", " . 
              $this->LineNumber . ", '" . 
              $this->CommentText . "');";
    $this->db->exec($query);
  }
  
}

$ac = new AssignmentComment(0, 1, 23, "test test");
$ac->save();


?>