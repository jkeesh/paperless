<?php
require_once("../config.php");

/*
* Singleton factory for producing a db connection
* Rationale behind this / credit here:
* http://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection
*/
class ConnectionFactory
{
  private static $factory;
  public static function getFactory()
  {
    if (!self::$factory)
      self::$factory = new ConnectionFactory();
    return self::$factory;
  }

  private $db;
  public function getConnection() {
    if (!$this->db) {
      try {
        $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
      } catch(PDOException $e) {
        echo $e->getMessage(); // TODO log this error instead of echoing
        return null;
      }
    }
    return $this->db;
  }
}


class Model {
  public $conn;
  
  public function __construct() {
    $this->conn = ConnectionFactory::getFactory()->getConnection();
  }
}
