<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class PaperlessAssignment extends Model {

	private $ID;
	private $Quarter;
	private $Class;
	private $DirectoryName;
	private $Name;
	private $DueDate;

	public function __construct() {
		parent::__construct();
	}

	/*
		* Saves the current assignment files's state to 
		* the database.
		*/
	public function save() {
		$query = "REPLACE INTO PaperlessAssignments ". 
			" VALUES(:ID, :Quarter, :Class, :DirectoryName, :Name, :DueDate);";

		try {
			$sth = $this->conn->prepare($query);
			$rows = $sth->execute(array(":ID" => $this->ID,
				":Quarter" => $this->Quarter,
				":Class" => $this->Class,
				":DirectoryName" => $this->DirectoryName,
				":Name" => $this->Name,
				":DueDate" => $this->DueDate
				));
			if(!$this->ID) {
				$this->ID = $this->conn->lastInsertId();
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}

	//this is really a static method..?
	public static function deleteID($id) {
		$instance = new self();
		$query = "DELETE FROM PaperlessAssignments " .
			" WHERE ID=:ID;";
		try {
			$sth = $instance->conn->prepare($query);
			$sth->execute(array(":ID" => $id));
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}

	public static function create($class, $dir, $name, $due) {
		$instance = new self();
		$quarter_id = Model::getQuarterID();
		$class_id = Model::getClassID($class);
		$instance->fill(array(0, $quarter_id, $class_id, $dir, $name, $due));
		return $instance;
	}

	public static function update($id, $class, $dir, $name, $due) {
		
		$instance = new self();
		$query = "REPLACE INTO PaperlessAssignments VALUES(:ID, :Quarter, :Class, :DirectoryName, :Name, :DueDate);";
		$quarter_id = Model::getQuarterID();
		$class_id = Model::getClassID($class);
		try {
			$sth = $instance->conn->prepare($query);
			$rows = $sth->execute(array(
				":ID" => $id,
				":Quarter" => $quarter_id,
				":Class" => $class_id,
				":DirectoryName" => $dir,
				":Name" => $name,
				":DueDate" => $due
				));
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
	}

	/*
	 * Get the due date for a given assignment.
	 */
	public static function get_due_date($course, $assn_dir){
		$db = Database::getConnection();
		$query = "SELECT DueDate FROM PaperlessAssignments WHERE Class=:Class AND Quarter=:Quarter AND DirectoryName LIKE :Dir";
		try {
			$sth = $db->prepare($query);
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			$sth->execute(array(":Class" => $course->id, ":Quarter" => $course->quarter->id, ":Dir" => $assn_dir));
			if($row = $sth->fetch()) {
				return $row['DueDate'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
		return null;
	}

	public static function getDueDate($class, $assn_dir){
		$instance = new self();	
		$query = "SELECT DueDate FROM PaperlessAssignments WHERE Class=:Class AND Quarter=:Quarter AND DirectoryName LIKE :Dir";
		$quarter_id = Model::getQuarterID();
		$class_id = Model::getClassID($class);
		try {
			$sth = $instance->conn->prepare($query);
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			$sth->execute(array(":Class" => $class_id, ":Quarter" => $quarter_id, ":Dir" => $assn_dir));
			if($row = $sth->fetch()) {
				return $row['DueDate'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
		return null;
	}
	
	
	//PaperlessAssignment::getID($qid, $class, $student);
	public static function getID($qid, $class, $dir){
		$instance = new self();	
		$query = "SELECT ID FROM PaperlessAssignments WHERE Class=:Class AND Quarter=:qid AND DirectoryName LIKE :Dir";		
		$class_id = Model::getClassID($class);
		
		try {
			$sth = $instance->conn->prepare($query);
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			$sth->execute(array(":Class" => $class_id,":Dir" => $dir, ":qid" => $qid));
			if($row = $sth->fetch()) {
				return $row['ID'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
		return null;
		
	}

	/*
		* Load from an id
		*/
	public static function loadForClass($class) {
		$query = "SELECT * FROM PaperlessAssignments WHERE Class=:Class AND Quarter=:Quarter";
		$instance = new self();
		$quarter_id = Model::getQuarterID();

		try {
			$sth = $instance->conn->prepare($query);
			$class_id = Model::getClassID($class);
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			$sth->execute(array(":Class" => $class_id, ":Quarter" => $quarter_id));
			if($rows = $sth->fetchAll()) {
				//print_r($rows);
				return $rows;
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echo
		}
		return null;
	}
	
	/*
	 * Load all of the PaperlessAssignments for a given Course object.
	 * 
	 * @param	$course		the Course object
	 *
	 * @author	Jeremy Keeshin	December 23, 2011
	 */
	public static function load_for_course($course) {
		$query = "SELECT * FROM PaperlessAssignments WHERE Class=:Class AND Quarter=:Quarter";
		$db = Database::getConnection();
		try {
			$sth = $db->prepare($query);
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			$sth->execute(array(":Class" => $course->id, ":Quarter" => $course->quarter->id));
			if($rows = $sth->fetchAll()) {
				return $rows;
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); 
		}
		return null;
	}
	
	

	/*
		* Getters and Setters
		*/

	public function fill(array $row) { 
		$this->ID = $row[0];
		$this->Quarter = $row[1];
		$this->Class = $row[2];
		$this->DirectoryName = $row[3];
		$this->Name = $row[4];
		$this->DueDate = $row[5];
	}

}
?>