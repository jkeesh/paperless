<?php
require_once(dirname(dirname(__FILE__)) . "/models/User.php");

class Student extends User {
	
	private $course;

	// public function __construct($sunetid, $course) {
	// 	parent::__construct($sunetid);
	// 	
	// 	$this->course = $course;
	// }
	
	public function set_course($course){
		$this->course = $course;
	}	
	
	public function get_link(){
		return ROOT_URL . $this->course->quarter->id . '/' . $this->course->name . '/student/' . $this->sunetid;
	}
	
	public static function from_sunetid_and_course($sunetid, $course){
		$instance = new self();
		$instance->load_by_sunetid($sunetid);
		$instance->course = $course;
	}
	
	public static function from_row($row){
		$instance = new self();
		$instance->sunetid = $row['SUNetID'];
		$instance->first_name = $row['FirstName'];
		$instance->last_name = $row['LastName'];
		$instance->display_name = $row['DisplayName'];
		$instance->id = $row['ID'];
		return $instance;
	}
	
	public function get_section_leader(){
		$query = "(SELECT SectionLeader FROM Sections 
					WHERE ID IN 
					(SELECT Section FROM SectionAssignments WHERE Person = :uid)
						AND Quarter = :quarter
						AND Class = :class
				  )";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":uid" => $this->id, ":class" => $this->course->id, ":quarter" => $this->course->quarter->id));
			if($row = $sth->fetch()) {
				return SectionLeader::from_id_and_course($row['SectionLeader'], $this->course);
			}else{
//				return "unknown";
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
}
?>