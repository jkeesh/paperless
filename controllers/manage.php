<?php

	require_once("models/PaperlessAssignment.php");

	/**
	 * Lets the Class admins modify class configurations
	 */
	class ManageHandler extends ToroHandler {
		
		
		// CREATE TABLE  `paperless5`.`PaperlessAssignments` (
		// `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		// `Quarter` INT NOT NULL ,
		// `Class` INT NOT NULL ,
		// `DirectoryName` VARCHAR( 30 ) NOT NULL ,
		// `Name` VARCHAR( 100 ) NOT NULL ,
		// `DueDate` DATETIME NOT NULL
		// ) ENGINE = MYISAM ;
		
		public function post($class){
			
			
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);

			print_r($_POST);

			if($_POST['action'] == "Update"){
				echo "update";
			}else if($_POST['action'] == "Delete"){
				echo "delete";
				PaperlessAssignment::deleteID($_POST['id']);
			}else{
				echo "add";
				$assn = PaperlessAssignment::create($class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
				$assn->save();
			}

			$assns = PaperlessAssignment::loadForClass($class);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('manage.html');	
		}
		
		public function get($class) {
						
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);
			
			$assns = PaperlessAssignment::loadForClass($class);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('manage.html');
		}
	}
	?>