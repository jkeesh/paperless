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
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_SECTION_LEADER, $this->role);	
			Permissions::verify(POSITION_SECTION_LEADER, $sectionleader, $this->course);
			
			$the_SL = new SectionLeader;
			$the_SL->from_sunetid_and_course($sectionleader, $this->course);						
			$course_base = $this->course->get_base_directory();
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