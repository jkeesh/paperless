<?php
require_once(dirname(dirname(__FILE__)) . "/models/User.php");

class Student extends User {
	
	private $class;

	public function __construct($sunetid, $class) {
		parent::__construct($sunetid);
		
		$this->class = $class;
	}	
	
	public function get_section_leader(){
		$db = Database::getConnection();	
		$classID = Model::getClassID($this->class);

		$query = "(SELECT SectionLeader FROM Sections 
					WHERE ID IN 
					(SELECT Section FROM SectionAssignments WHERE Person = :uid)
						AND Quarter = (SELECT DefaultQuarter FROM State)
						AND Class = :class
				  )";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":uid" => $this->id, ":class" => $classID));
			if($row = $sth->fetch()) {
				return Model::getSUID($row['SectionLeader']);
			}else{
				return "unknown";
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
}
?>