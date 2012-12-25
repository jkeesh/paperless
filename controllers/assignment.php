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
		public function post_xhr($qid, $class, $sectionleader, $assignment){
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_SECTION_LEADER, $this->role);

			//print_r($_POST);
			$student = $_POST['student'];
			$parts = explode("_", $student); // if it was student_1 just take student
			$suid = $parts[0];
			$submission_number = $parts[1];

			$the_student = new Student;
			$the_student->from_sunetid_and_course($suid, $this->course);

			$dirname = $this->course->get_base_directory() . '/' . $assignment . '/' . $student .'/';
			
			if($_POST['action'] == "release"){
				if($_POST['release'] == "create"){
					Utilities::create_release($dirname);
				}else{
					Utilities::delete_release($dirname);
				}
			}
	
			echo json_encode(array("status" => "ok")); //This is not needed. But remember to echo a result in JSON format.
		}
		
		function get_id($student){
			return $student->sunetid;
		}
		
		public function get($qid, $class, $sectionleader, $assignment) {
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_SECTION_LEADER, $this->role);
			Permissions::verify(POSITION_SECTION_LEADER, $sectionleader, $this->course);
			
			$sl = new SectionLeader;
			$sl->from_sunetid_and_course($sectionleader, $this->course);
			
			$assn = new PaperlessAssignment;
			$assn->from_course_and_assignment($this->course, $assignment);

			
			$dirname = $this->course->get_base_directory() .'/' . $assignment;
						
			$all_students = $this->getDirEntries($dirname);
			
			$student_ids = array_map("get_id", $sl->get_students_for_assignment($assn));
			
			$student_dirs = array();
			foreach($all_students as $student){
				$split = splitDirectory($student);
				if(in_array($split[0], $student_ids)){
					$student_dirs []= $student;
				}
			}
						
			sort($student_dirs);
			
			$info = array();
			$greatest = array(); // an array mapping from student => greatest submission number (most recent)
			
			$i = 0;
			foreach($student_dirs as $student){
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
				
				if(!array_key_exists($sunetid, $greatest) ||
				 (array_key_exists($sunetid, $greatest) && $submissionNumber > $greatest[$sunetid] )){
					$greatest[$sunetid] = $submissionNumber;
				}
				
				$i++;
			}
						
			if(count($students[0]) > 0){
				$this->smarty->assign("info", $info);
				$this->smarty->assign("greatest", $greatest);
			}else{
				$this->smarty->assign("nothing", 1);
			}
				
			$this->smarty->assign("assignment", $assignment);
			$this->smarty->assign("class", $class);
			
			$this->smarty->display('assignment.html');
		}
	}
?>