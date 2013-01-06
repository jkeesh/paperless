<?php

require_once('utils.php');
require_once("models/PaperlessAssignment.php");

class DragDropSubmitHandler extends ToroHandler {

	// Delete a file
	public function post_xhr() {
		$this->basic_setup(func_get_args());

		/* If the action was delete_file, delete the file from the submission */
		if(array_key_exists('action', $_POST) && $_POST['action'] == 'delete_file'){
			$success = Utilities::delete_code_file($_POST['assn'], $_POST['file'], $this);
			echo json_encode(array('status' => 'ok', 'remove' => $success));
			return;
		}

		echo json_encode(array('status' => 'ok'));
	}

	/*
	 * Get the upload directory for this course.
	 */
	function get_assn_dir($assn){				
		return  $assn . '/';
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

		// display the template
		$this->smarty->display('ddsubmit.html');
	}
}
?>