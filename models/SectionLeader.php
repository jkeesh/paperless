<?php
require_once(dirname(dirname(__FILE__)) . "/models/User.php");
require_once(dirname(dirname(__FILE__)) . "/models/Student.php");

class SectionLeader extends User {
	
	public $section_id;
	
	/*
	 * Return an object that represnts the unknown section leader
	 */
	public function unknown($course){
		$this->course = $course;
		$this->sunetid = "unknown";
	}

	/*
	 * Static factory constructor to make a SectionLeader from a sunetid
	 * and Course object.
	 */
	public function from_sunetid_and_course($sunetid, $course){
		$this->from_sunetid($sunetid);
		$this->course = $course;
		
		$db = Database::getConnection();
		$query = "SELECT ID FROM Sections WHERE SectionLeader = :uid AND Quarter = :qid AND Class = :class_id";		
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":uid" => $this->id, 
								":qid" => $this->course->quarter->id, 
								":class_id" => $this->course->id));
			if($row = $sth->fetch()) {
				$this->section_id = $row['ID'];				
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
	public function from_id_and_course($id, $course){
		$this->from_id($id);
		$this->course = $course;		
		$db = Database::getConnection();
		$query = "SELECT ID FROM Sections WHERE SectionLeader = :uid AND Quarter = :qid AND Class = :class_id";		
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":uid" => $id, 
								":qid" => $course->quarter->id, 
								":class_id" => $course->id));
			if($row = $sth->fetch()) {
				$this->section_id = $row['ID'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
	public function get_students(){
		$db = Database::getConnection();
		
		// if($sl_sunetid == "unknown"){
		// 	return Model::getAllStudentsForClass($class);
		// }
		
		$query = "SELECT ID, DisplayName, SUNetID, FirstName, LastName FROM People WHERE ID IN 
					(SELECT Person FROM SectionAssignments WHERE Section = :section_id)";
					
		$students = array();
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":section_id" => $this->section_id));
			while($row = $sth->fetch()){
				$student = Student::from_row($row);
				$student->set_course($this->course);
				$students []= $student;
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
		
		return $students;
	}	
	
	// The section leader's base directory is the course base directory + the SL name
	public function get_base_directory(){
		return $this->course->get_base_directory() . '/' . $this->sunetid;
	}
	
}
?>