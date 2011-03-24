<?php
	require_once('models/Model.php');
	require_once('utils.php');
	
	/**
	 * This class handles the logic for a section leader view of a 
	 * single assignment. An assignment view will list all of the submissions
	 * for a particular assignment, and if there are multiple for the same student.
	 * Using the drag-drop submitter, each student should only have 1 submission,
	 * but in the 106A submitter, and older version each submission comes up as
	 * sunetid_#.
	 */
	class AssignmentHandler extends ToroHandler {
		
		/**
		 * This method handles the AJAX request when the Section Leader modifies the check
		 * box which determines whether or not the comments on this code file should be released.
		 * Since we are on an assignment page, we know the SL, class, and assignment. The student
		 * is passed in in the POST array. Based on the POST action, we decide if we should create
		 * or delete the release file.
		 */
		public function post_xhr($class, $sectionleader, $assignment){
			$student = $_POST['student'];
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/" . $assignment . "/". $student . "/";
			
			if($_POST['action'] == "release"){
				if($_POST['release'] == "create"){
					createRelease($dirname);
				}else{
					deleteRelease($dirname);
				}
			}
	
			//echo json_encode("success"); //This is not needed. But remember to echo a result in JSON format.
		}
		
		public function get($class, $sectionleader, $assignment) {
			$status = Model::getRoleForClass($sectionleader, $class); //sanity check: make sure they are visiting an sl for this class
			if($status < POSITION_SECTION_LEADER){
				Header("Location: " . ROOT_URL);
			}

			$role = Permissions::requireRole(POSITION_SECTION_LEADER, $class);
						
			if($role == POSITION_SECTION_LEADER){
				$this->smarty->assign("sl_class", $class);
			}
			if($role > POSITION_SECTION_LEADER){
				$this->smarty->assign("admin_class", $class);
			}
						
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/" . $assignment;
			$students = $this->getDirEntries($dirname);
			

			$info = array();
			
			sort($students);
			
			$greatest = array(); // an array mapping from student => greatest submission number (most recent)
			
			$i = 0;
			foreach($students as $student){
				$info[$i]['dirname'] = $student;
				$releaseCheck = $dirname . "/" .$student."/release";
				$info[$i]['release'] = 0;
				if(is_file($releaseCheck)){
					$info[$i]['release'] = 1;
				}
				
				$split = splitDirectory($student);
				$submissionNumber = $split[1];
				$sunetid = $split[0];
				$info[$i]['num'] = $submissionNumber;
				$info[$i]['student'] = $sunetid;
				
				if($submissionNumber > $greatest[$sunetid]){
					$greatest[$sunetid] = $submissionNumber;
				}
				
				$i++;
			}
			
			//print_r($info);
			//print_r($greatest);
						
			if(count($students[0]) > 0){
				$this->smarty->assign("info", $info);
				$this->smarty->assign("greatest", $greatest);
			}else{
				$this->smarty->assign("nothing", 1);
			}
				
			$this->smarty->assign("assignment", $assignment);
			$this->smarty->assign("class", $class);
			
			// display the template
			$this->smarty->display('assignment.html');
		}
	}
?>