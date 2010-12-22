<?php
	class AdminHandler extends ToroHandler {
		
		public function get($class) {
			$role = Model::getRoleForClass(USERNAME, $class);
			echo $role;
			
			if($role < POSITION_TEACHING_ASSISTANT && !(USERNAME == "jkeeshin" || USERNAME == "econner") ){
				echo "No permission";
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