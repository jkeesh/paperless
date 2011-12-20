<?php
require_once("permissions.php");
require_once('models/User.php');
require_once('models/SectionLeader.php');

	
	function studentSort($a, $b){
		return $a['DisplayName'] > $b['DisplayName'];
	}
	
	class SectionLeaderHandler extends ToroHandler {
		
		function sortAll($students){
			if($students)
				uasort($students, 'studentSort');
			return $students;
		}
		
		public function get($qid, $class, $sectionleader) {
			
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
			
			$user = new User(USERNAME);
			$course = Course::from_name_and_quarter_id($class, $qid);
			$this->smarty->assign("course", $course);
			$the_SL = new SectionLeader($sectionleader, $course);
			//print_r($the_SL);
			
			$course_base = $course->get_base_directory();
			echo $course_base;
			$sls = $this->sortAll($this->getDirEntries($course_base));

			$sl_base = $the_SL->get_base_directory();
						
			$assns = $this->getDirEntries($sl_base);
			if(empty($assns) || strlen($assns[0]) == 0){
				$this->smarty->assign("no_assns", 1);
			}

			$the_students = $the_SL->get_students();
			$this->smarty->assign("students", $the_students);

			// assign template variables
			$this->smarty->assign("sls", $sls);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", htmlentities($class));
			$this->smarty->assign("sl", $sectionleader);
			
			// display the template
			$this->smarty->display('index.html');
		}
	}
?>