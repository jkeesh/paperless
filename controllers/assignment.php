<?php
	
	require_once('models/Model.php');
	
	class AssignmentHandler extends ToroHandler {
		
		public function get($class, $sectionleader, $assignment) {
			
			$role = Model::getRoleForClass(USERNAME, $class);
			echo $role;
			
			if($role < POSITION_SECTION_LEADER){
				echo "No permission IN ASSIGNMENT";
				return;
			}
			
			
			$dirname = SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
			$students = $this->getDirEntries($dirname . $assignment);
			
			$this->smarty->assign("students", $students);
			$this->smarty->assign("assignment", $assignment);
			$this->smarty->assign("class", $class);
			
			// display the template
			$this->smarty->display('assignment.html');
		}
	}
	?>