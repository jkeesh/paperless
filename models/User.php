<?php
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/models/Model.php");

class User extends Model {

	public $sunetid;
	public $first_name;
	public $last_name;
	public $display_name;
	public $id;

	public function __construct($sunetid) {
		parent::__construct();
		$this->sunetid = $sunetid;
		
		$query = "SELECT ID, FirstName, LastName, DisplayName FROM People WHERE SUNetID = :sunetid";
		try {
			$sth = $this->conn->prepare($query);
			$sth->execute(array(":sunetid" => $sunetid));
			if($rows = $sth->fetch()) {
				$this->first_name = $rows['FirstName'];
				$this->last_name = $rows['LastName'];
				$this->display_name = $rows['DisplayName'];
				$this->id = $rows['ID'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage(); // TODO log this error instead of echoing
		}
	}
	
}
?>