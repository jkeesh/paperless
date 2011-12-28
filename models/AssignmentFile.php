<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");
require_once(dirname(dirname(__FILE__)) . "/models/Quarter.php");

class AssignmentFile extends Model {

	public $ID;
	private $GradedAssignment;
	private $FilePath;
	private $AssignmentComments;
	private $PaperlessAssignment;
	private $Student;
	private $SubmissionNumber;

	public function __construct() {
		parent::__construct();
		$this->AssignmentComments = array( );
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

	public function saveFile() {
		$query = "REPLACE INTO " . ASSIGNMENT_FILE_TABLE . 
			" VALUES(:ID, :GradedAssignment, :File, :PaperlessAssignment, :Student, :SubmissionNumber);";
		try {
			$sth = $this->conn->prepare($query);
			$rows = $sth->execute(array(
				":ID" => $this->ID,
				":GradedAssignment" => $this->GradedAssignment,
				":File" => $this->FilePath,
				":PaperlessAssignment" => $this->PaperlessAssignment,
				":Student" => $this->Student,
				":SubmissionNumber" => $this->SubmissionNumber
				));

			if(!$this->ID) {
				$this->ID = $this->conn->lastInsertId();
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}
	
	public static function create_file($course, $paperless_assignment, $student, $file, $submission_number){
		$instance = new self();
		$instance->ID = 0;
		$instance->GradedAssignment = 0;
		$instance->FilePath = $file;
		if(is_null($paperless_assignment)){
			$instance->PaperlessAssignment = 0;
		}else{
			$instance->PaperlessAssignment = $paperless_assignment->ID;
		}
		$instance->Student = $student->id;
		$instance->SubmissionNumber = $submission_number;		
		return $instance;
	}
	

	public static function createFile($class, $assignment, $student, $file, $submission_number){
		$qid = Quarter::current()->id;
		$PA_id = PaperlessAssignment::getID($qid, $class, $assignment);
		$student_id = Model::getUserID($student);

		$instance = new self();
		$instance->ID = 0;
		$instance->GradedAssignment = 0;
		$instance->FilePath = $file;
		$instance->PaperlessAssignment = $PA_id;
		$instance->Student = $student_id;
		$instance->SubmissionNumber = $submission_number;
		return $instance;
	}


	public function loadComments() {
		$query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE . " WHERE AssignmentFile=:AssignmentFile";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":AssignmentFile" => $this->ID));

			$sth->setFetchMode(PDO::FETCH_ASSOC);
			while($row = $sth->fetch()) {
				$curComment = AssignmentComment::create($row['AssignmentFile'], $row['StartLine'], 
					$row['EndLine'], $row['CommentText'], $row['Commenter'], $row['Student']);
				$curComment->setID($row['ID']);
				$this->AssignmentComments[] = $curComment;
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}
	
		
	/*
	 * This loads an AssignmentFile object.
	 *
	 * @param	$student				{Object}	the Student object
	 * @param	$paperless_assignment	{Object}	the PaperlessAssignment object
	 * @param	$assignment				{string}	the name of the assignment
	 * @param	$file					{string}	the name of the file
	 * @param	$number					{int}		the submission number for this assignment
	 *
	 * @return 	the AssignmentFile object, or null if it is not found
	 *
	 * @author	Jeremy Keeshin	December 25, 2011
	 */ 
	public static function load_file($student, $paperless_assignment, $file, $number){
		if(is_null($paperless_assignment)){
			return null;
		}

		$query = "SELECT * FROM AssignmentFiles 
					WHERE Student = :Student_ID
						AND	SubmissionNumber = :Submission_Number
						AND	PaperlessAssignment = :Assn_ID
						AND File = :File;";
						
		$arr = array(":Student_ID" => $student->id, ":Submission_Number" => $number, 
					 ":Assn_ID" => $paperless_assignment->ID, ":File" => $file );
					
		$instance = new self();
		
		try {
			$sth = $instance->conn->prepare($query);
			$sth->execute($arr);
			$sth->setFetchMode(PDO::FETCH_NUM);
			if($row = $sth->fetch()) {
				$instance->fill($row);
				$instance->loadComments();
				return $instance;
			}
		} catch(PDOException $e) {
		}
		
		return null;				
	}

	/*
	 * Getters and Setters
	 */
	public function fill(array $row) { 
		$this->ID = $row[0];
		$this->GradedAssignment = $row[1];
		$this->FilePath = $row[2];
		$this->PaperlessAssignment = $row[3];
		$this->Student = $row[4];
		$this->SubmissionNumber = $row[5];
	}

	public function setID($ID) { $this->ID = $ID; }
	public function getID() { return $this->ID; }

	public function setGradedAssignment($GradedAssignment) { $this->GradedAssignment = $GradedAssignment; }
	public function getGradedAssignment() { return $this->GradedAssignment; }

	public function setFilePath($FilePath) { $this->FilePath = $FilePath; }
	public function getFilePath() { return $this->FilePath; }

	public function getAssignmentComments() { return $this->AssignmentComments; }

	public function setSubmissionNumber($num){ $this->SubmissionNumber = $num; }
}
?>