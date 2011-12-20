<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class Quarter extends Model {

	public $id;
	public $year;
	public $quarter;
	
	public static $quarter_strings = array("Winter", "Spring", "Summer", "Fall");
	
	private function quarter_string(){
		return Quarter::$quarter_strings[$this->quarter - 1];
	}
	
	public function __construct($qid) {
		parent::__construct();

		$query = "SELECT ID, Year, Quarter FROM Quarters WHERE ID = :id;";
	  	try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":id" => $qid));
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			if($row = $sth->fetch()) {
			  $this->id = $row['ID'];
			  $this->year = $row['Year'];
			  $this->quarter = $row['Quarter'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
	public function __toString(){
		return $this->quarter_string() . ", " . $this->year;
	}
}
?>