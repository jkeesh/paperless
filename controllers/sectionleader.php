<?php
require_once("permissions.php");

	class SectionLeaderHandler extends ToroHandler {
		
		public function get($class, $sectionleader) {
			Permissions::requireRole(POSITION_SECTION_LEADER, $class);
									
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
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