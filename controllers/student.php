<?php
	require_once('index.php');
	require_once('models/Model.php');
	require_once('permissions.php');
	
	function cmp($a, $b){
		$pos_a = strpos($a, "_");
		$pos_b = strpos($b, "_");
		$num_a = substr($a, $pos_a + 1);
		$num_b = substr($b, $pos_b + 1);
		if($num_a > $num_b) return 1;
		return -1;
	}
	
	class StudentHandler extends ToroHandler {
		

		
		function sortArr($arr){
			uasort($arr, 'cmp');
			$result = array();
			foreach($arr as $item){
				$result []= $item;
			}
			return $result;
		}
		
		public function get($class, $student) {
			
			$string = explode("_", $student); // if it was student_1 just take student
			$student = $string[0];
			
			// If the user is not the current student, require that they be a section
			// leader for this class to be able to view the code
			if($student != USERNAME) {
				$role = Permissions::requireRole(POSITION_SECTION_LEADER, $class);
			}else{
			// Otherwise if the usernames match, require that this student be enrolled
			// in the current class
				$role = Permissions::requireRole(POSITION_STUDENT, $class);
			}
						
			if($role == POSITION_SECTION_LEADER){
				$this->smarty->assign("sl_class", $class);
			}
			if($role == POSITION_STUDENT){
				$this->smarty->assign("student_class", $class);
			}
			if($role > POSITION_SECTION_LEADER){
				$this->smarty->assign("admin_class", $class);
			}
			
			
			$sl = Model::getSectionLeaderForStudent($student, $class);	
			
			if($sl == "unknown"){
				$this->smarty->assign("nosl", 1);
			}
			
			$this->smarty->assign("sl", $sl);
			
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl . "/";
			
			//echo $dirname;
			$assns = $this->getDirEntries($dirname);
			
			//information will be an associative array where index i holds
			//the assignment and student directory information as keys
			$information = array();
			
			$i = 0;
			//for every assignment, go find ones that belong to the student
			//we will save the submission with the highest number.
			
			if($assns){
				foreach($assns as $assn) {
					$dir = $dirname . $assn ."/";
					$student_submissions = $this->getDirEntries($dir);
					
					$information[$i]['assignment'] = $assn;
					$information[$i]['all'] = array();
					foreach($student_submissions as $submission) {
						if(strpos($submission, $student) !== false) {
							array_push($information[$i]['all'],$submission);
						}
					}
					
					$all = $information[$i]['all'];
					$all = $this->sortArr($all);
									
					$information[$i]['all'] = $all;
					if(count($all) > 0)
						$information[$i]['studentdir'] = $all[count($all)-1];
					
					if(count($all) == 0){
						//they have no submissions for this assignment so remove that
						array_splice($information, $i);
						$i--;
					}
					
					$i++;
				}
			}else{
				$this->smarty->assign("nofiles", 1);
			}
			if(count($information) == 0){
				$this->smarty->assign("nofiles", 1);
			}			
			//print_r($information);
			
			// assign template vars
			$this->smarty->assign("information", $information);
			$this->smarty->assign("class", htmlentities($class));
			$this->smarty->assign("student", Model::getDisplayName($student));
			
			// display the template
			$this->smarty->display("student.html");
		}
	}
	?>
