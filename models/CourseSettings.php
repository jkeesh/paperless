<?php

class CourseSettings{

	// In the database, course settings have a ID, class, and quarter
	private $id;
	
	// We keep track of the related course object
	public $course;
	
	// The configuration array.
	private $config;
	
	public static $defaults = array(	
									'use_submitter' => 'yes',
								 	'file_types' => 'txt, cpp, java, c, cc'
								 	);

	/*
	 * Return the settings value for a user for this key.
	 */
	public function get_value($key){
		return $this->config[$key];
	}

	/*
	 * Set the value for the user for this key.
	 */
	public function set_value($key, $value){
		$this->config[$key] = $value;
	}

	
	// Save the configuration for a user.
	public function save(){
		$encoded = json_encode($this->config);
		$query = "UPDATE PaperlessCourseConfig SET Config = :Config WHERE Quarter = :qid AND Class = :class_id";
		$db = Database::getConnection();
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(':Config' => $encoded, ":qid" => $this->course->quarter->id, ":class_id" => $this->course->id));			
		} catch(PDOException $e) {
		}	
	}
	
	/*
	 * Create a new entry in the db for this user.
	 */
	private static function create($course){
		$db = Database::getConnection();		
		$query = "INSERT INTO PaperlessCourseConfig (Quarter, Class, Config) Values (:qid, :class_id, :config)";
		try {
			$sth = $db->prepare($query);
			$result = $sth->execute(array(":qid" => $course->quarter->id, ":class_id" => $course->id, ":config" => ''));

		} catch(PDOException $e) {

		}				
	}
	
	
	public static function get_for_course($course){
		$instance = new self();
		$instance->course = $course;
		$db = Database::getConnection();		
		$query = "SELECT ID, Config FROM PaperlessCourseConfig WHERE Quarter = :qid AND Class = :class_id";
		try {
			$sth = $db->prepare($query);
			$sth->execute(array(":qid" => $course->quarter->id, ":class_id" => $course->id));
			$sth->setFetchMode(PDO::FETCH_ASSOC);
			if($row = $sth->fetch()) {
				$instance->id = $row['ID'];
				$instance->config = json_decode($row['Config'], true); // turn into assoc. array.	
			}else{
				$instance->config = CourseSettings::$defaults; // empty for now
				CourseSettings::create($course);
			}
		} catch(PDOException $e) {

		}
		
		$instance->save();		
		return $instance;
	}
	
	

}

?>