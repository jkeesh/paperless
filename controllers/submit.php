<?php
	
	class SubmitHandler extends ToroHandler {
		
		private function getAssns($assignments_file) {
			$assn_data = fopen($assignments_file, "r");
			
			$arr = array();
			
			while (! feof($assn_data)) {
				$info = fgetcsv($assn_data);
				if ($info == NULL) {
					continue;
				}
				$arr[$info[0]] = array("Name" => $info[1], "DueDate" => date_create($info[2]));
			}
			return $arr;
		}
		
		public function get($class) {
			
			
			$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";
			
			//use the hardcoded assignments file for now
			$assignments_file = ROOT_URL. "controllers/assignments.csv";
			$assns = $this->getAssns($assignments_file);

			// assign template variables
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("name", Model::getDisplayName(USERNAME));
			
			$sourcelist = ".java";
			if($class == "cs106x" || $class == "cs106b") $sourcelist = ".cpp or .h";
			$this->smarty->assign("sourcelist", $sourcelist);
			
			
			// display the template
			$this->smarty->display('submit.html');
		}
	}
	?>