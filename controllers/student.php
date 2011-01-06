<?php
	require_once('index.php');
	require_once('models/Model.php');
	require_once('permissions.php');
	
	class StudentHandler extends ToroHandler {
		
		public function get($class, $student) {

			$string = explode("_", $student); // if it was student_1 just take student
			$student = $string[0];
			
			if($student != USERNAME) {
				$role = Permissions::requireRole(POSITION_SECTION_LEADER, $class);
			}else{
				$role = Model::getRoleForClass(USERNAME, $class);
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
			
			
			$sl = Model::getSectionLeaderForStudent($student);	
			echo "student " . $student;
			echo " sl " . $sl;
			
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl . "/";
			
			echo $dirname;
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
						$information[$i]['studentdir'] = $submission;
						array_push($information[$i]['all'],$submission);
					}
				}
				$i++;
			}
			}else{
				$this->smarty->assign("nofiles", 1);
			}			
			//print_r($information);
			
			// assign template vars
			$this->smarty->assign("information", $information);
			$this->smarty->assign("class", htmlentities($class));
			
			// display the template
			$this->smarty->display("student.html");
		}
	}
	?>
