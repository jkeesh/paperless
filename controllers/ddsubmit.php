<?php
	
	require_once('utils.php');
	
	class DragDropSubmitHandler extends ToroHandler {
				
		public function get($class) {
			print_r($_GET);
			$selected = $_GET['assignment'];
			if($selected){
				echo "drag and drop page";
				$this->smarty->assign("dragdrop", 1);
				$this->smarty->assign("assndir", $selected);
			}else{
				echo "first page";
			}
			
			
			$role = Model::getRoleForClass(USERNAME, $class);	
					
			if($role == POSITION_STUDENT){
				$this->smarty->assign("student_class", $class);
			}else{
				Header("Location: " . ROOT_URL);
			}
						
			$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
			$assns = getAssnsForClass($class);

			// assign template variables
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("name", Model::getDisplayName(USERNAME));
			
			$sourcelist = ".java";
			if($class == "cs106x" || $class == "cs106b") $sourcelist = ".cpp or .h";
			if($class == "cs109l") $sourcelist = ".r";
			$this->smarty->assign("sourcelist", $sourcelist);
				
			// display the template
			$this->smarty->display('ddsubmit.html');
		}
	}
	?>