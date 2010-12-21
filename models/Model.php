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


  public static function getQuarterID(){
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

  public static function getUserID($sunetid){
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

  public static function getQuarterName(){
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

  public static function getSectionIDForSectionLeader($sl_sunetid){
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

  public static function getStudentsForSectionLeader($sl_sunetid){
	$db = Database::getConnection();
	$section_db_id = Model::getSectionIDForSectionLeader($sl_sunetid);
	
	$query = "SELECT ID, DisplayName, SUNetID FROM People WHERE ID IN (SELECT Person FROM SectionAssignments WHERE Section = :sectionID)";

	try {
      $sth = $db->prepare($query);
      $sth->execute(array(":sectionID" => $section_db_id));
      if($rows = $sth->fetchAll()) {
        return $rows;
      }
    } catch(PDOException $e) {
        echo $e->getMessage(); // TODO log this error instead of echoing
    }
  }

  public static function getDisplayName($sunetid){
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
  
  /*
  * Tests whether given sunet id is a section leader
  * @param sunet id of user to test
  * @return true if user is an SL, false otherwise
  */
  public static function isSectionLeader($sunetid) {
    $db = Database::getConnection();
    
    $query = "SELECT (SELECT ID FROM People WHERE SUNetID = :sunetid) IN" .
              "(SELECT SectionLeader FROM Sections" .
              " WHERE Quarter = (SELECT DefaultQuarter FROM State)) AS IsSL;";
    try {
      $sth = $db->prepare($query);
      $sth->execute(array(":sunetid" => $sunetid));
      if($row = $sth->fetch()) {
        if($row['IsSL']) return true;
      }
    } catch(PDOException $e) {
        echo $e->getMessage(); // TODO log this error instead of echoing
    }
    return false;
  }


  public static function isStudent($the_id) {
    $db = Database::getConnection();
 //	return $the_id;
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
}
