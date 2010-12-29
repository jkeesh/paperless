<?php
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