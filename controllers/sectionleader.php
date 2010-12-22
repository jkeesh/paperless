<?php
	class SectionLeaderHandler extends ToroHandler {
		
		public function get($class, $sectionleader) {
				
			//TODO replace with call to has permission 
    		if(IS_STUDENT_ONLY){
				echo "Redirect to error page";
				return;
    		}
			
			$studentdir = DUMMYDIR;
			$dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
			
			$assns = $this->getDirEntries($dirname);
			
			// assign template variables
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", htmlentities($class));
			
			// display the template
			$this->smarty->display('index.html');
		}
	}
	?>