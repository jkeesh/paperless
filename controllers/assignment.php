<?php
	require_once('models/Model.php');
	
	class AssignmentHandler extends ToroHandler {
		
		public function get($class, $sectionleader, $assignment) {
			$status = Model::getRoleForClass($sectionleader, $class); //sanity check: make sure they are visiting an sl for this class
			if($status < POSITION_SECTION_LEADER){
				Header("Location: " . ROOT_URL);
			}

			$role = Permissions::requireRole(POSITION_SECTION_LEADER, $class);
						
			if($role == POSITION_SECTION_LEADER){
				$this->smarty->assign("sl_class", $class);
			}
			if($role > POSITION_SECTION_LEADER){
				$this->smarty->assign("admin_class", $class);
			}
						
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			$students = $this->getDirEntries($dirname . $assignment);
			

			sort($students);
			
			if(count($students[0]) > 0)
				$this->smarty->assign("students", $students);
			else
				$this->smarty->assign("nothing", 1);
			$this->smarty->assign("assignment", $assignment);
			$this->smarty->assign("class", $class);
			
			// display the template
			$this->smarty->display('assignment.html');
		}
	}
?>