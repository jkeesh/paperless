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
}
