<?php

require_once('utils.php');
require_once("models/PaperlessAssignment.php");

class DragDropSubmitHandler extends ToroHandler {



	public function post($class){

		$assn = $_POST['assignment'];

		$this->smarty->assign("dragdrop", 1);

		$role = Model::getRoleForClass(USERNAME, $class);	

		if($role == POSITION_STUDENT){
			$this->smarty->assign("student_class", $class);
		}else{
			Header("Location: " . ROOT_URL);
		}

		$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
		$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/" . $assn . "/";
		$target_dir = $dirname;
		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		/* append index (submission number) */
		$idx = 1;
		do {
			$dest_dir = $target_dir . USERNAME . "_" . $idx;
			$cur_submission = USERNAME . "_" . $idx;
			if(isEmptyDir($dest_dir)) break;
			$idx++;
		} while (file_exists($dest_dir));
		echo $dest_dir;
		$assn_dir = $assn . "/". $cur_submission;
		echo "<br/>".$assn_dir."<br/>";
		$this->smarty->assign("assndir", $assn_dir);


		echo $cur_submission;
		if (!file_exists($dest_dir)) {
			mkdir($dest_dir, 0777, true);
		}

		// $assns = PaperlessAssignment::loadForClass($class);
		// //print_r($assns);
		// 
		// // assign template variables
		// $this->smarty->assign("assignments", $assns);
		$this->smarty->assign("class", $class);
		$this->smarty->assign("name", Model::getDisplayName(USERNAME));
		$this->smarty->assign("cur_submission", $cur_submission);

		$sourcelist = ".java";
		if($class == "cs106x" || $class == "cs106b" || $class == "cs106l") $sourcelist = ".cpp or .h";
		if($class == "cs109l") $sourcelist = ".r";
		$this->smarty->assign("sourcelist", $sourcelist);

		// display the template
		$this->smarty->display('ddsubmit.html');
	}


	public function get($class) {

		if(array_key_exists('assignment', $_POST)){
			//echo "drag and drop page";
			$this->smarty->assign("dragdrop", 1);
			$this->smarty->assign("assndir", $_GET['assignment']);
		}else{
			if(!array_key_exists('open', $_GET)){
				$this->smarty->assign("message", "The submitter is not yet open for this quarter. Check back soon.");
				$this->smarty->display("message.html");
				return;
			}
		}


		$role = Model::getRoleForClass(USERNAME, $class);	

		if($role == POSITION_STUDENT){
			$this->smarty->assign("student_class", $class);
		}else{
			Header("Location: " . ROOT_URL);
		}

		$sectionleader = Model::getSectionLeaderForStudent(USERNAME);
		$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sectionleader . "/";

		$assns = PaperlessAssignment::loadForClass($class);
		//print_r($assns);

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