<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");
require_once(dirname(dirname(__FILE__)) . "/models/AssignmentFile.php");

class AssignmentComment extends Model {

	private $ID;
	private $AssignmentFile;
	private $StartLine;
	private $EndLine;
	private $CommentText;
	private $Commenter;
	private $Student;
	private $Time;

	public function __construct() {
		parent::__construct();
	}

	/*
		* Saves the current assignment comment's state to 
		* the database.
		*/
	public function save() {
		$query = "REPLACE INTO " . ASSIGNMENT_COMMENT_TABLE . 
			" VALUES(:ID, :AssignmentFile, :StartLine, :EndLine, :CommentText, :Commenter, :Student, CURRENT_TIMESTAMP);";
		try {
			$sth = $this->conn->prepare($query);
			$rows = $sth->execute(array(":ID" => $this->ID,
				":AssignmentFile" => $this->AssignmentFile,
				":StartLine" => $this->StartLine,
				":EndLine" => $this->EndLine,
				":CommentText" => $this->CommentText,
				":Commenter" => $this->Commenter,
				":Student" => $this->Student,
				));

			if(!$this->ID) {
				$this->ID = $this->conn->lastInsertId();
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}

	public function delete() {
		$query = "DELETE FROM " . ASSIGNMENT_COMMENT_TABLE .
			" WHERE ID = :ID;";
		try {
			$sth = $this->conn->prepare($query);
			$rows = $sth->execute(array(":ID" => $this->ID));
			// TODO test if query successful
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}

	public static function create($AssignmentFile, $StartLine, $EndLine, $CommentText, $Commenter, $Student) {
		$instance = new self();
		$instance->fill(array(0, $AssignmentFile, $StartLine, $EndLine, $CommentText, $Commenter, $Student, time()));
		return $instance;
	}

	/*
		* Load from an id
		*/
	public static function load($ID) {

		$query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE .
			" WHERE ID = :ID;";
		$instance = new self();

		try {
			$sth = $instance->conn->prepare($query);
			$sth->execute(array(":ID" => $ID));

			$sth->setFetchMode(PDO::FETCH_NUM);
			if($row = $sth->fetch()) {
				$instance->fill($row);
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
		$this->setAssignmentFile($row[1]);
		$this->setStartLine($row[2]);
		$this->setEndLine($row[3]);
		$this->setCommentText($row[4]);
		$this->setCommenter($row[5]);
		$this->setStudent($row[6]);
		$this->setTime($row[7]);
	}
	
	public function setCommenter($Commenter){ $this->Commenter = $Commenter; }
	public function getCommenter(){ return $this->getDisplayName($this->getSUID($this->Commenter)); }
	
	public function setStudent($Student){ $this->Student = $Student; }
	public function getStudent(){ return $this->Student; }

	public function setID($ID) { $this->ID = $ID; }
	public function getID() { return $this->ID; }

	public function setAssignmentFile($AssignmentFile) { $this->AssignmentFile = $AssignmentFile; }
	public function getAssignmentFile() { return $this->AssignmentFile; }

	public function setStartLine($StartLine) { $this->StartLine = $StartLine; }
	public function getStartLine() { return $this->StartLine; }

	public function setEndLine($EndLine) { $this->EndLine = $EndLine; }
	public function getEndLine() { return $this->EndLine; }

	public function setCommentText($CommentText) { $this->CommentText = $CommentText; }
	public function getCommentText() { return $this->CommentText; }
	
	public function setTime($Time){ $this->Time = $Time; }
	public function getTime(){ return $this->Time; }
}
?>