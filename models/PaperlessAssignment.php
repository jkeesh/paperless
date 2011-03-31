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

	public function delete() {
		// delete the file object
		$query = "DELETE FROM PaperlessAssignments " .
			" WHERE ID=:ID;";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":ID" => $this->ID));
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

	/*
		* Load from an id
		*/
	public static function load($args) {
		// extract($args);
		// 
		// $query = "SELECT * FROM " . ASSIGNMENT_FILE_TABLE . " WHERE File=:FilePath;";
		// $instance = new self();
		// 
		// try {
		// 	$sth = $instance->conn->prepare($query);
		// 	$sth->execute(array(":FilePath" => $FilePath));
		// 
		// 	$sth->setFetchMode(PDO::FETCH_NUM);
		// 	if($row = $sth->fetch()) {
		// 		$instance->fill($row);
		// 		return $instance;
		// 	}
		// } catch(PDOException $e) {
		// 	echo $e->getMessage(); // TODO log this error instead of echo
		// }
		// return null;
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