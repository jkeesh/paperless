<?php

	/**
	 * This class is the handler for the Administrative view. The Admin view is seen
	 * by the head TA of a class as well as the Professor for the class. It is a basic
	 * view which includes a listing of all of the section leaders in the class, as 
	 * determined by the class submissions directory, since each SL has his own folder.
	 */
	class AdminHandler extends ToroHandler {
		
		public function get($class) {
						
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);
			
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR;
			
			$sls = $this->getDirEntries($dirname);
			sort($sls);
			
			// assign template variables
			$this->smarty->assign("sls", $sls);
			$this->smarty->assign("class", htmlentities($class));
			
			// display the template
			$this->smarty->display('admin.html');
		}
	}
	?>