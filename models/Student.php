<?php
require_once(dirname(dirname(__FILE__)) . "/models/User.php");

class Student extends User {
	
	private $course;

	public function __construct($sunetid, $course) {
		parent::__construct($sunetid);
		
		$this->course = $course;
	}	
	
	public function get_section_leader(){
		$query = "(SELECT SectionLeader FROM Sections 
					WHERE ID IN 
					(SELECT Section FROM SectionAssignments WHERE Person = :uid)
						AND Quarter = (SELECT DefaultQuarter FROM State)
						AND Class = :class
				  )";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":uid" => $this->id, ":class" => $this->course->id));
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