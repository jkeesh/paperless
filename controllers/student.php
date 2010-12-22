<?php
	require_once('index.php');
	require_once('models/Model.php');
	
	class StudentHandler extends ToroHandler {
		
		public function get($class, $student) {
			
			$string = explode("_", $student); // if it was student_1 just take student
			$student = $string[0];
			
			$role = Model::getRoleForClass(USERNAME, $class);
			echo $role;
			
			if($role < POSITION_SECTION_LEADER && $student != USERNAME){
				echo "You don't have permission to view this buddy.";
				return;
			}
						
		    $sl = Model::getSectionLeaderForStudent($student);
			
			//TODO make sure submissions dir takes into account class
			$dirname = SUBMISSIONS_DIR . "/" . $sl ."/";
			$assns = $this->getDirEntries($dirname);
			
			//information will be an associative array where index i holds
			//the assignment and student directory information as keys
			$information = array();
			
			$i = 0;
			//for every assignment, go find ones that belong to the student
			//we will save the submission with the highest number.
			foreach($assns as $assn) {
				$dir = $dirname . $assn ."/";
				$student_submissions = $this->getDirEntries($dir);
				
				$information[$i]['assignment'] = $assn;
				foreach($student_submissions as $submission) {
					if(strpos($submission, $student) !== false) {
						$information[$i]['studentdir'] = $submission;
					}
				}
				$i++;
			}
			
			// assign template vars
			$this->smarty->assign("information", $information);
			$this->smarty->assign("class", htmlentities($class));
			
			// display the template
			$this->smarty->display("student.html");
		}
	}
	?>
