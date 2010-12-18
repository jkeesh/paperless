<?php
require_once("../config.php");
class Model {
  protected $db;
  
  public function __construct() {
    try {
      $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATABASE, MYSQL_USERNAME, MYSQL_PASSWORD);
      #$this->db = new PDO("sqlite:paperless.db");
    } catch(PDOException $e) {
      echo $e->getMessage();
    }
  }
}
