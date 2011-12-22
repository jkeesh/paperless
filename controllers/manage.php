<?php

	require_once("models/PaperlessAssignment.php");

	/**
	 * Lets the Class admins modify class configurations
	 */
	class ManageHandler extends ToroHandler {
		
		public function post($class){
			
			
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);
			if($_POST['action'] == "Update"){
				PaperlessAssignment::update($_POST['id'], $class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
			}else if($_POST['action'] == "Delete"){
				PaperlessAssignment::deleteID($_POST['id']);
			}else{
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
		
		public function get($qid, $class) {
			$quarter = Quarter::current();
			$course = Course::from_name_and_quarter_id($class, $qid);
			$this->smarty->assign("course", $course);
			
			if($quarter->id != $qid){
				$this->smarty->assign("old_quarter", true);
			}
			
			$role = Permissions::require_role(POSITION_TEACHING_ASSISTANT, $this->user, $course);
			$this->smarty->assign("role", $role);
			$assns = PaperlessAssignment::loadForClass($class);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('manage.html');
		}
	}
	?>