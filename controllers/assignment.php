<?php
	require_once('models/Model.php');
	require_once('utils.php');
	
	class AssignmentHandler extends ToroHandler {
		
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