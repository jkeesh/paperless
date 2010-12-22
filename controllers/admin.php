<?php
	class AdminHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				echo "Error page";
				return;
			}
			
			if(!IS_ADMIN){
				echo "Not an admin";
				return;
			}
			
			echo "hello admin for ". $class;

			$studentdir = DUMMYDIR;
			$dirname = SUBMISSIONS_DIR;
			
			$sls = $this->getDirEntries($dirname);
			
			// assign template variables
			$this->smarty->assign("sls", $sls);
			$this->smarty->assign("class", htmlentities($class));
			
			// display the template
			$this->smarty->display('admin.html');
		}
	}
	?>