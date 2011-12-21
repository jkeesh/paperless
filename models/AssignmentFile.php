<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");
require_once(dirname(dirname(__FILE__)) . "/models/Quarter.php");

class AssignmentFile extends Model {

	private $ID;
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

		// print_r(array(
		// 	":ID" => $this->ID,
		// 	":GradedAssignment" => $this->GradedAssignment,
		// 	":File" => $this->FilePath,
		// 	":PaperlessAssignment" => $this->PaperlessAssignment,
		// 	":Student" => $this->Student,
		// 	":SubmissionNumber" => $this->SubmissionNumber
		// 	));

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

	// public static function create($GradedAssignment, $FilePath) {
		// 	$instance = new self();
		// 	$instance->fill(array(0, $GradedAssignment, $FilePath));
		// 	return $instance;
		// }

		public function loadComments() {
			$query = "SELECT * FROM " . ASSIGNMENT_COMMENT_TABLE . " WHERE AssignmentFile=:AssignmentFile";
			try {
				$sth = $this->conn->prepare($query);
				$sth->execute(array(":AssignmentFile" => $this->ID));

				$sth->setFetchMode(PDO::FETCH_ASSOC);
				while($row = $sth->fetch()) {
					print_r($row);
					$curComment = AssignmentComment::create($row['AssignmentFile'], $row['StartLine'], 
						$row['EndLine'], $row['CommentText'], $row['Commenter'], $row['Student']);
					$curComment->setID($row['ID']);
					$this->AssignmentComments[] = $curComment;
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echo
			}
		}


		//	$assignmentFile = AssignmentFile::loadFile($class, $student, $assignment, $file);
		public static function loadFile($qid, $class, $student, $dir, $file, $number = 0){
			$paperless_assignment_id = PaperlessAssignment::getID($qid, $class, $dir);	
			
			$sunetid = explode("_", $student);
			$sunetid = $sunetid[0];

			$student_id = Model::getUserID($sunetid);
			$query = "SELECT * FROM " . ASSIGNMENT_FILE_TABLE . " WHERE Student=:Student AND PaperlessAssignment=:AssnID AND File=:File AND SubmissionNumber=:Number;";
			$arr = array(":Student" => $student_id, ":AssnID" => $paperless_assignment_id, ":File" => $file, ":Number" => $number);
			$instance = new self();

			try {
				$sth = $instance->conn->prepare($query);
				$sth->execute($arr);
				$sth->setFetchMode(PDO::FETCH_NUM);
				if($row = $sth->fetch()) {
					echo "load file...";
					print_r($row);
					$instance->fill($row);
					$instance->loadComments();
					return $instance;
				}else{

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