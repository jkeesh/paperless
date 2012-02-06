<?php

	/**
	 */
	class AdminSearchHandler extends ToroHandler {
		
		public function get($qid, $class) {
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_TEACHING_ASSISTANT, $this->role);			
			
			echo "SEARCH";
			print_r($_GET);
			
			$dirname = $this->course->get_base_directory();
			$sls = $this->getDirEntries($dirname);
			if($sls){
				sort($sls);
			}
				
			// assign template variables
			$this->smarty->assign("sls", $sls);
			$this->smarty->display('admin.html');
		}
	}
	?>