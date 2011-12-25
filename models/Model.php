<?php
	require_once(dirname(dirname(__FILE__)) . "/config.php");
	
	/*
	 * Singleton factory for producing a db connection
	 * Rationale behind this / credit here:
	 * http://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection
	 */
	class Database
	{
		private static $db;
		public function getConnection() {
			if (!self::$db) {
				try {
					self::$db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
				} catch(PDOException $e) {
					echo $e->getMessage(); // TODO log this error instead of echoing
					return null;
				}
			}
			return self::$db;
		}
	}
	
	
	class Model {
		public $conn;
		
		public function __construct() {
			$this->conn = Database::getConnection();
		}
		
		public static function getRoleForClass($user, $class){
			if($user == "unknown") return POSITION_SECTION_LEADER;
			
			$db = Database::getConnection();
			$query = "SELECT Position FROM CourseRelations INNER JOIN State
						WHERE 
							Person IN ( SELECT ID FROM People WHERE SUNetID = :sunetid )
						AND
							Class IN ( SELECT ID FROM Courses WHERE Name LIKE :classname )
						AND
							Quarter = DefaultQuarter";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $user, ":classname" => $class));
				if($rows = $sth->fetch()) {
					return $rows['Position'];      
				}else{
					return POSITION_NOT_A_MEMBER;
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getQuarterID() {
			$db = Database::getConnection();
			
			$query = "SELECT DefaultQuarter FROM State;";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array());
				if($rows = $sth->fetch()) {
					return $rows['DefaultQuarter'];      
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getUserID($sunetid) {
			$db = Database::getConnection();
			$query = "SELECT ID FROM People WHERE SUNetID = :sunetid";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $sunetid));
				if($rows = $sth->fetch()) {
					return $rows['ID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getQuarterName() {
			$db = Database::getConnection();
			$quarterID = Model::getQuarterID();
			$query = "SELECT CONCAT(SUBSTRING('WinterSpringSummerAutumn',(Quarter * 6 - 5),6), ' ', Year) AS Name FROM Quarters WHERE ID = :quarterID";
			
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":quarterID" => $quarterID));
				if($rows = $sth->fetch()) {
					return $rows['Name'];     
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getSectionIDForSectionLeader($sl_sunetid) {
			$db = Database::getConnection();
			$quarterID = Model::getQuarterID();
			$sl_db_ID = Model::getUserID($sl_sunetid);
			$query = "SELECT ID FROM Sections WHERE Quarter = :quarterID AND SectionLeader = :sectionLeaderID";
		
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":quarterID" => $quarterID, ":sectionLeaderID" => $sl_db_ID));
				if($rows = $sth->fetch()) {
					return $rows['ID'];   
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getAllStudentsForClass($class){
			$db = Database::getConnection();	
			$query = "SELECT ID, DisplayName, SUNetID FROM People WHERE ID IN 
						(SELECT Person FROM CourseRelations WHERE Position = 1 
							AND Class = (SELECT ID FROM Courses WHERE Name LIKE :classname) 
							AND Quarter = (SELECT DefaultQuarter FROM State))";		
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":classname" => $class));
				if($rows = $sth->fetchAll()) {
					return $rows;
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getStudentsForSectionLeader($sl_sunetid, $class) {
			$db = Database::getConnection();
			
			if($sl_sunetid == "unknown"){
				return Model::getAllStudentsForClass($class);
			}
			
			$query = "SELECT ID, DisplayName, SUNetID, FirstName, LastName FROM People WHERE ID IN 
						(SELECT Person FROM SectionAssignments WHERE Section IN
							(SELECT ID FROM Sections 
								WHERE SectionLeader IN
									(SELECT ID FROM People WHERE SUNetID LIKE :slid )
								AND Quarter = (SELECT DefaultQuarter FROM State)
									))";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":slid" => $sl_sunetid));
				if($rows = $sth->fetchAll()) {
					return $rows;
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getDisplayName($sunetid) {
			$db = Database::getConnection();
			
			$query = "SELECT DisplayName FROM People WHERE SUNetID = :sunetid";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $sunetid));
				if($row = $sth->fetch()) {
					return $row['DisplayName']; // form NAME (SUID)
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getSectionIDForUserID($user_id) {
			$db = Database::getConnection();
			$query = "SELECT Section FROM SectionAssignments WHERE Person = :userid";
			
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":userid" => $user_id));
				if($row = $sth->fetch()) {
					return $row['Section'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getSectionLeaderForSectionID($section_id) {
			$db = Database::getConnection();
			$query = "SELECT SectionLeader FROM Sections WHERE ID = :sectionid";
			
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sectionid" => $section_id));
				if($row = $sth->fetch()) {
					return $row['SectionLeader'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getSUID($user_db_id){
			$db = Database::getConnection();
			$query = "SELECT SUNetID FROM People WHERE ID = :dbid";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":dbid" => $user_db_id));
				if($row = $sth->fetch()) {
					return $row['SUNetID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getLecturerForClass($class){
			$db = Database::getConnection();
			
			$query = "SELECT SUNetID FROM PEOPLE WHERE ID IN 
					  (SELECT Person FROM CourseRelations WHERE Class = :classID AND Position = :lec)";
			$classID = Model::getClassID($class);
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":classID" => $classID, ":lec" => 6));
				if($row = $sth->fetch()) {
					return $row['SUNetID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
			return false;
		}
		
		public static function getSectionLeaderForStudent($student_suid, $class){
			

			
			$db = Database::getConnection();	
			$classID = Model::getClassID($class);
			$query = "(SELECT SectionLeader FROM Sections 
						WHERE ID IN 
						(SELECT Section FROM SectionAssignments 
							WHERE Person IN 
								(SELECT ID FROM People WHERE SUNetID = :sunetid ))
						AND Quarter = (SELECT DefaultQuarter FROM State)
						AND Class = :class
					  )";
			
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $student_suid, ":class" => $classID));
				if($row = $sth->fetch()) {
					return Model::getSUID($row['SectionLeader']);
				}else{
					return "unknown";
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		

			
		public static function isStudent($the_id) {
			$db = Database::getConnection();
			$query = "SELECT (SELECT ID FROM People WHERE SUNetID = :sunetid) IN
			(SELECT Person FROM CourseRelations
			WHERE Position = 1
			AND Quarter = (SELECT DefaultQuarter FROM State)) AS IsStudent;";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $the_id));
				if($row = $sth->fetch()) {
					return $row['IsStudent'] == 1;
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
			return false;
		}
		
		public static function getClass($sunetid) {
			$db = Database::getConnection();
			$query = "SELECT Name FROM Courses WHERE ID IN
					(SELECT Class FROM CourseRelations WHERE Person IN
					(SELECT ID FROM People WHERE SUNetID = :sunetid) 
					AND Quarter = (SELECT DefaultQuarter FROM State))";
			try {
				$sth = $db->prepare($query);
				$sth->execute(array(":sunetid" => $sunetid));
				if($row = $sth->fetchAll()) {
					return $row;
					//return strtolower($row['Name']);
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
			return null;
		}
		
		public static function getClassID($class) {
		  $db = Database::getConnection();
		  
		  $class = strtolower($class);
		  $query = "SELECT ID FROM Courses WHERE NAME = :class;";
		  try {
				$sth = $db->prepare($query);
				$sth->execute(array(":class" => $class));
				$sth->setFetchMode(PDO::FETCH_ASSOC);
				if($row = $sth->fetch()) {
				  return $row['ID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
		
		public static function getGradedAssignID($class, $sunetid, $assn) {
		  $db = Database::getConnection();
		  
		  $assnID = self::getAssignID($assn, $db);
		  $quarterID = self::getQuarterID();
		  $classID = self::getClassID($class);
		  $userID = self::getUserID($sunetid);
		  
		  $query = "SELECT * FROM GradedAssignments WHERE Criteria = (SELECT ID FROM Criteria" .
		          " WHERE Class = $classID AND Quarter = $quarterID AND Assignment = $assnID) AND" .
		          " QUARTER = $quarterID AND Student = $userID;";
      try {
				$sth = $db->prepare($query);
				$sth->execute();
				$sth->setFetchMode(PDO::FETCH_ASSOC);
				if($row = $sth->fetch()) {
				 return $row['ID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
			return 0; // no id found
		}
		
    /*
		* Gets the assignment id for a given name
		* Note: we do it this way since we use preg_replace which isn't possible 
		* with SQL.
		*/
		private static function getAssignID($assn, $db) {
		  $query = "SELECT ID,Title FROM Assignments;";
		  $assn = strtolower(preg_replace('/\W/', '', $assn));
		  try {
				$sth = $db->prepare($query);
				$sth->execute();
				$sth->setFetchMode(PDO::FETCH_ASSOC);
				while($row = $sth->fetch()) {
				  $curAssn = strtolower(preg_replace('/\W/', '', $row['Title']));
				  if($curAssn == $assn)
				    return $row['ID'];
				}
			} catch(PDOException $e) {
				echo $e->getMessage(); // TODO log this error instead of echoing
			}
		}
	}
