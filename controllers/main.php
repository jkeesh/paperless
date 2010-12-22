<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
    		if(IS_STUDENT_ONLY){
    		  Header("Location: " .  ROOT_URL ."$class/student/" . USERNAME);
				  return;
    		}
			
			if(IS_SECTION_LEADER){
			  Header("Location: " .  ROOT_URL ."$class/sectionleader/" . USERNAME);
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