<?php
	class SectionLeaderHandler extends ToroHandler {
		
		public function get($class, $sectionleader) {
				
			//TODO bring this into permissions, and a wrapper possibly that gives a redirect url if they dont have permissions
			$role = Model::getRoleForClass(USERNAME, $class);
			echo $role;
			
			if($role < POSITION_SECTION_LEADER){
				echo "No permission in SECTIONLEADER";
				return;
			}
						
			$studentdir = DUMMYDIR;
			$dirname = SUBMISSIONS_DIR . "/" . $sectionleader ."/";			
			$assns = $this->getDirEntries($dirname);
			
			$this->smarty->assign("students", Model::getStudentsForSectionLeader($sectionleader));
			
			// assign template variables
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", htmlentities($class));
			$this->smarty->assign("sl", $sectionleader);
			
			// display the template
			$this->smarty->display('index.html');
		}
	}
	?>