<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");
require_once(dirname(dirname(__FILE__)) . "/models/Relationship.php");


class User extends Model {

	public $sunetid;
	public $first_name;
	public $last_name;
	public $display_name;
	public $id;

	public static function from_id($id){
		$instance = new self();
		$instance->id = $id;
		$db = Database::getConnection();
		
		$query = "SELECT FirstName, LastName, DisplayName, SUNetID FROM People WHERE ID = :id";
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":sunetid" => $sunetid));
			if($rows = $sth->fetch()) {
				$instance->first_name = $rows['FirstName'];
				$instance->last_name = $rows['LastName'];
				$instance->display_name = $rows['DisplayName'];
				$instance->sunetid = $rows['SUNetID'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
		return $instance;
	}

	public static function from_sunetid($sunetid){
		$instance = new self();
		$instance->sunetid = $sunetid;
		$db = Database::getConnection();
		
		$query = "SELECT ID, FirstName, LastName, DisplayName FROM People WHERE SUNetID = :sunetid";
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":sunetid" => $sunetid));
			if($rows = $sth->fetch()) {
				$instance->first_name = $rows['FirstName'];
				$instance->last_name = $rows['LastName'];
				$instance->display_name = $rows['DisplayName'];
				$instance->id = $rows['ID'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
		return $instance;
	}

	// public function __construct($sunetid) {
	// 	parent::__construct();
	// 	$this->sunetid = $sunetid;
	// 	
	// 	$query = "SELECT ID, FirstName, LastName, DisplayName FROM People WHERE SUNetID = :sunetid";
	// 	try {
	// 		$sth = $this->conn->prepare($query);
	// 		$sth->execute(array(":sunetid" => $sunetid));
	// 		if($rows = $sth->fetch()) {
	// 			$this->first_name = $rows['FirstName'];
	// 			$this->last_name = $rows['LastName'];
	// 			$this->display_name = $rows['DisplayName'];
	// 			$this->id = $rows['ID'];
	// 		}
	// 	} catch(PDOException $e) {
	// 		echo $e->getMessage(); // TODO log this error instead of echoing
	// 	}
	// }
	
	/*
	 * Return all of the classes that this user has belonged to.
	 * For example, they may have been a student in cs106a in quarter 90,
	 * a student in 106b in quarter 92, and a section leader in cs106b in quarter 94
	 *
     * We get all the relationships for a user. A relationship has a (course, role),
     * and a course knows its quarter.
     */
	public function get_all_relationships(){
		$query = "SELECT Class, Quarter, Position FROM CourseRelations WHERE Person = :pid ORDER BY Quarter DESC";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":pid" => $this->id));
			
			$relationships = array();
			while($row = $sth->fetch()) {
				$relationships []=  Relationship::from_row($row);
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
		return $relationships;
	}
	
	/*
	 * Given a course object, return a user's role in that course.
	 * @param $course, the Course object
	 * @return $role, the role of the user in the course.
	 */
	public function get_role_for_course($course){
		// if($user == "unknown") return POSITION_SECTION_LEADER;
		
		$db = Database::getConnection();
		$query = "SELECT Position FROM CourseRelations INNER JOIN State
					WHERE 
						Person = :pid
					AND
						Class = :cid
					AND
						Quarter = :qid";
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":pid" => $this->id, ":cid" => $course->id, ":qid" => $course->quarter->id));
			if($rows = $sth->fetch()) {
				return $rows['Position'];      
			}else{
				return POSITION_NOT_A_MEMBER;
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
}
?>