<?php
	
	require_once('utils.php');
	
	class SubmitHandler extends ToroHandler {
				
		public function get($class) {
			
			
			$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
			$assns = getAssnsForClass($class);

			// assign template variables
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("name", Model::getDisplayName(USERNAME));
			
			$sourcelist = ".java";
			if($class == "cs106x" || $class == "cs106b") $sourcelist = ".cpp or .h";
			$this->smarty->assign("sourcelist", $sourcelist);
			
			
			// display the template
			$this->smarty->display('submit.html');
		}
	}
	?>