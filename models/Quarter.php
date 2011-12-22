<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class Quarter extends Model {

	public $id;
	public $year;
	public $quarter;
	
	public static $quarter_strings = array("Winter", "Spring", "Summer", "Fall");
	public static $people_soft_quarters = array('Fall' => 2, 'Winter' => 4, 'Spring' => 6, 'Summer' => 8);

	private function quarter_string(){
		return Quarter::$quarter_strings[$this->quarter - 1];
	}
	
	public static function current(){
		$db = Database::getConnection();
		$query = "SELECT DefaultQuarter FROM State;";
		try {
			$sth = $db->prepare($query);
			$sth->execute(array());
			if($rows = $sth->fetch()) {
				return new self($rows['DefaultQuarter']);      
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
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
	
	/* 
	 * The People Soft Quarter code is a very stupid code that tells us where the
	 * afs mount point is.
	 * The format is CYYQ, where 
	 * C	= century code = 1
	 * YY 	= year code = the last two digits of calendar year when academic year ends
	 * Q	= quarter code , where 2 = Fall, 4 = Winter, 6 = Spring, 8 = Summer
	 */
	public function get_people_soft_code(){
		if($this->quarter == 4){ // People soft year is one greater for Fall			
			$people_soft_year = $this->year + 1;
		}else{
			$people_soft_year = $this->year;
		}
		$people_soft_year = substr($people_soft_year, 2, 2);
		$people_soft_quarter = Quarter::$people_soft_quarters[$this->quarter_string()];
		return '1'. $people_soft_year . $people_soft_quarter;
	}
}
?>