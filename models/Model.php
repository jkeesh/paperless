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
