<?php
	require_once('models/Model.php');
	
	class AssignmentHandler extends ToroHandler {
		
		public function get($class, $sectionleader, $assignment) {

			Permissions::requireRole(POSITION_SECTION_LEADER, $class);
			
			
			
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			echo $dirname;
//			$dirname = SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
			$students = $this->getDirEntries($dirname . $assignment);
			
			$this->smarty->assign("students", $students);
			$this->smarty->assign("assignment", $assignment);
			$this->smarty->assign("class", $class);
			
			// display the template
			$this->smarty->display('assignment.html');
		}
	}
?>