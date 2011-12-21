<?php

	/**
	 * This class is the handler for the Administrative view. The Admin view is seen
	 * by the head TA of a class as well as the Professor for the class. It is a basic
	 * view which includes a listing of all of the section leaders in the class, as 
	 * determined by the class submissions directory, since each SL has his own folder.
	 */
	class AdminHandler extends ToroHandler {
		
		public function get($qid, $class) {
			$course = Course::from_name_and_quarter_id($class, $qid);
			$this->smarty->assign("course", $course);
			
			$role = Permissions::require_role(POSITION_TEACHING_ASSISTANT, $this->user, $course);
			
			$dirname = $course->get_base_directory();
			echo $dirname;
			// $dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR;			
			$sls = $this->getDirEntries($dirname);
			print_r($sls);
			if($sls){
				sort($sls);
			}
				
			// assign template variables
			$this->smarty->assign("sls", $sls);
			$this->smarty->assign("class", htmlentities($class));
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('admin.html');
		}
	}
	?>