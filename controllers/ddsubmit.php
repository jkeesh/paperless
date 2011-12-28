<?php

require_once('utils.php');
require_once("models/PaperlessAssignment.php");

class DragDropSubmitHandler extends ToroHandler {


	/*
	 * Get the upload directory for this course.
	 */
	function get_assn_dir($assn){			
		$the_student = new Student;
		$the_student->from_sunetid_and_course(USERNAME, $this->course);				
		$sl = $the_student->get_section_leader();
		return $sl->get_base_directory() . "/" . $assn . '/';
	}

	public function post($qid, $class){
		$this->basic_setup(func_get_args());
		if($this->role != POSITION_STUDENT){
			Header("Location: " . ROOT_URL);
		}
		
		$assn = $_POST['assignment'];
		$this->smarty->assign("dragdrop", 1);

		$dirname = $this->get_assn_dir($assn);
	
		$target_dir = $dirname;
		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		/* append index (submission number) */
		$idx = 1;
		do {
			$dest_dir = $target_dir . USERNAME . "_" . $idx;
			$cur_submission = USERNAME . "_" . $idx;
			if(Utilities::isEmptyDir($dest_dir)) break;
			$idx++;
		} while (file_exists($dest_dir));
		$assn_dir = $assn . "/". $cur_submission;
		$this->smarty->assign("assndir", $assn_dir);

		if (!file_exists($dest_dir)) {
			mkdir($dest_dir, 0777, true);
		}

		$this->smarty->assign("name", Model::getDisplayName(USERNAME));
		$this->smarty->assign("cur_submission", $cur_submission);

		$filetypes = $this->course->get_file_types();
		$this->smarty->assign("filetypes", $filetypes);
		$this->smarty->display('ddsubmit.html');
	}


	public function get($qid, $class) {
		$this->basic_setup(func_get_args());
		if($this->role != POSITION_STUDENT){
			Header("Location: " . ROOT_URL);
		}
		
		// Uncomment this to close the submitter
		// if(!array_key_exists('open', $_GET)){
		// 	$this->smarty->assign("message", "The submitter is not yet open for this quarter. Check back soon.");
		// 	$this->smarty->display("message.html");
		// 	return;
		// }

		$sectionleader = Model::getSectionLeaderForStudent(USERNAME, $class);
		$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";

		$assns = PaperlessAssignment::load_for_course($this->course);

		// assign template variables
		$this->smarty->assign("assignments", $assns);
		$this->smarty->assign("class", $class);
		$this->smarty->assign("name", Model::getDisplayName(USERNAME));

		$sourcelist = ".java";
		if($class == "cs106x" || $class == "cs106b" || $class == "cs106l") $sourcelist = ".cpp or .h";
		if($class == "cs109l") $sourcelist = ".r";
		$this->smarty->assign("sourcelist", $sourcelist);

		// display the template
		$this->smarty->display('ddsubmit.html');
	}
}
?>