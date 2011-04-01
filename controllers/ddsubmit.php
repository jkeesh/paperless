<?php
	
	require_once('utils.php');
	require_once("models/PaperlessAssignment.php");
		
	class DragDropSubmitHandler extends ToroHandler {
				
		public function get($class) {
		
			if(array_key_exists('assignment', $_GET)){
				//echo "drag and drop page";
				$this->smarty->assign("dragdrop", 1);
				$this->smarty->assign("assndir", $_GET['assignment']);
			}else{
				if(!array_key_exists('open', $_GET)){
					$this->smarty->assign("message", "The submitter is not yet open for this quarter. Check back soon.");
					$this->smarty->display("message.html");
					return;
				}
			}
			
			
			$role = Model::getRoleForClass(USERNAME, $class);	
					
			if($role == POSITION_STUDENT){
				$this->smarty->assign("student_class", $class);
			}else{
				Header("Location: " . ROOT_URL);
			}
						
			$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";

			$assns = PaperlessAssignment::loadForClass($class);
			//print_r($assns);

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