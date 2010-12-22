<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
    		if(IS_STUDENT_ONLY){
				echo "<script type='text/javascript'> window.location = '". ROOT_URL ."$class/student/" . USERNAME ."'</script>";
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