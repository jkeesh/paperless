<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class Relationship extends Model {
	
	public static $role_strings = array("Student", "Applicant", "Course Helper", "Section Leader", "Lecturer", "Coordinator");

	private $course;
	private $role;
	
	/*
	 * We should not display this relationship if they are an applicant
	 */
	public function should_show(){
		return $this->role != POSITION_APPLICANT;
	}
	
	private function get_role_url_component(){
		if($this->role == POSITION_LECTURER || 
		   $this->role == POSITION_TEACHING_ASSISTANT){
			return "admin";
		}
		if($this->role == POSITION_SECTION_LEADER){
			return "sectionleader";
		}
		return "student";
	}
	
	/*
	 * Return the link to direct the user to the proper page for their role
	 * in this class in the current quarter.
	 */
	public function get_link(){
		$qid = $this->course->quarter->id;
		$course_name = strtolower($this->course->name);
		$role_component = $this->get_role_url_component();
		return ROOT_URL . $qid . '/' . $course_name . '/' . $role_component . '/' . USERNAME;
	}
	
	public function __construct() {
		//parent::__construct();
		//$this->role = $position;
		//$
	}
	
	public static function from_row($row){
		$instance = new self();
		$instance->role = $row['Position'];
		$instance->course = Course::from_row($row);
		return $instance;
	}
	
	public function __toString(){
		return Relationship::$role_strings[$this->role-1]. " in ". $this->course;
	}
		
}
?>