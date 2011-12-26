<?php
require_once("models/AssignmentFile.php");
require_once("models/AssignmentComment.php");
require_once("models/Model.php");
require_once("permissions.php");
require_once("utils.php");

/*
	* Controller that handles the syntax highlighted code view
	* for a student, assignment pair and other ajax actions.
	*/
class CodeHandler extends ToroHandler {

	/*
		* Gets the file contents, file names, and appropriate AssignmentFile model objects
		* corresponding to a given student, assignment pair.
		* TODO: Don't use a hardcoded path / we need to allow multiple base search paths
		* TODO: Handle error when a pathname is not found
		*/

	// $the_student		the Student object
	// $the_sl			the SectionLeader object
	// $course			the Course object
	private function getAssignmentFiles($class, $student, $assignment, $sl, $the_student, $the_sl, $course) {
		$dirname = $the_sl->get_base_directory() . "/". $assignment . "/" . $student . "/"; 

		$error = null;

		if(!is_dir($dirname)){
			$error = "This was not a valid directory.";
			return array($error);
		}

		$dir = opendir($dirname);
		$files = array();
		$file_contents = array();
		$assignment_files = array();

		$string = explode("_", $student); // if it was student_1 just take student
		$student_suid = $string[0];
		$submission_number = $string[1];
		
		$last_dir = $the_sl->get_base_directory() . "/". $assignment . "/" . $student_suid;
		$last_submission = Utilities::getLastSubmissionNumber($last_dir);
		
		echo "STUDENT: ". $student. "\n\n";
		
		$release = False;
		while($file = readdir($dir)) {
			if($file == "release"){
				$release = True;
			}else if(isCodeFileForClass($file, $class) && valid_size($dirname, $file)) {
				$assn = AssignmentFile::loadFile($course->quarter->id, $class, $student, $assignment, $file, $submission_number);
				// If we could load it by submission number, we are done
				if(is_null($assn)){
					$assn = AssignmentFile::loadFile($course->quarter->id, $class, $student, $assignment, $file);
					// Now we try to load and see if an old file was there.
				
					if(is_null($assn)){
						//It wasn't so create a new one.
						$assn = AssignmentFile::createFile($class, $assignment, $student_suid, $file, $submission_number);
						$assn->saveFile();
					}else{
						//If it was, update the old one.
						if($submission_number == $last_submission){
							//echo "set submission number";
							$assn->setSubmissionNumber($submission_number);
							//print_r($assn);
						}else{
							$assn = AssignmentFile::createFile($class, $assignment, $student_suid, $file, $submission_number);
							//echo "created new file";
						}
						$assn->saveFile();					
					}

				}
				$assignment_files[] = $assn;
				$files[] = $file;
				$file_contents[] = htmlentities(file_get_contents($dirname . $file));
			}
		}

		// print_r($files);
		// print_r($file_contents);
		// print_r($assignment_files);

		return array($error, $files, $file_contents, $assignment_files, $release);
	}

	/*
		* Displays the syntax highlighted code for a student, assignment pair
		*/
	public function get($qid, $class, $assignment, $student, $print=False) {
		$this->basic_setup(func_get_args());
				
		if($print){
			$this->smarty->assign("print_view", $print);
		}
		$this->smarty->assign("code_file", $student);

		$suid = explode("_", $student); // if it was student_1 just take student
		// The code directory was not well formed.
		if(count($suid) != 2){
			$this->smarty->assign('errorMsg', "The code directory was not well formed.");
			$this->smarty->display('error.html');
            return;
		}
		
		$submission_number = $suid[1];
		$suid = $suid[0];
		
		$the_student = new Student;
		$the_student->from_sunetid_and_course($suid, $this->course);
		
		$the_sl = $the_student->get_section_leader();

		$dirname = $the_sl->get_base_directory() . "/". $assignment . "/" . $student . "/"; 
		$all_files = Utilities::get_all_files($dirname);
		$code_files = Utilities::get_code_files($this->course, $the_student, $assignment, $dirname, $all_files, $submission_number);
		$release = Utilities::release_exists($dirname);
		$this->smarty->assign("release", $release);
		
		$this->smarty->assign("code_files", $code_files);
		
		$this->smarty->assign("the_student", $the_student);
		$this->smarty->assign("the_sl", $the_sl);

		// if the username is something other than the owner of these files, require
		// it to be a SL
		if($suid != USERNAME) {
			Permissions::gate(POSITION_SECTION_LEADER, $this->role);	
		}else{
		// Otherwise require this student to be in the class
			Permissions::gate(POSITION_STUDENT, $this->role);	
		}

		// $sl = Model::getSectionLeaderForStudent($suid, $class);
		// 
		// // public static function get_assignment_files($course, $student, $assignment, $sl, $submission){
		// list($error, $files, $file_contents, $assignment_files, $release) = Utilities::get_assignment_files($this->course, $the_student, $assignment, $the_sl, $student);
		// 
		// list($error, $files, $file_contents, $assignment_files, $release) = $this->getAssignmentFiles($class, $student, $assignment, $sl, $the_student, $the_sl, $this->course);
		
		
		// if($error != null){
		// 	$this->smarty->assign('errorMsg', $error);
		// 	$this->smarty->display('error.html');
		//             return;
		// }

		// if(count($files) == 0){
		// 	$this->smarty->assign("message", "Nothing here yet.");
		// }

		if($this->role >= POSITION_SECTION_LEADER){
			$this->smarty->assign("interactive", 1);
			$showComments = True;
		}
		if($this->role == POSITION_STUDENT){
			$showComments = $release;
		}

		// assign template vars
		// $this->smarty->assign("code", true);
		// 
		// $string = explode("_", $student); // if it was student_1 just take student
		// $student_suid = $string[0];
		// 
		// $this->smarty->assign("numbered_submission", $student);
		// $this->smarty->assign("class", htmlentities($class));
		// $this->smarty->assign("student", htmlentities($student_suid));
		$this->smarty->assign("assignment", htmlentities($assignment));
		// $this->smarty->assign("files", $files);
		// $this->smarty->assign("file_contents", $file_contents);
		// $this->smarty->assign("assignment_files", $assignment_files);
		// $this->smarty->assign("sl", $sl);
		// 
		$this->smarty->assign("showComments", $showComments);

		// display the template
		$this->smarty->display("code.html");
	}

	/*
		* Handles adding and deleting comments.  Note: when a comment is edited it is
		* first deleted and then re-added to the database.
		* TODO: Log / handle an error when we don't have a valid path
		* TODO: We probably need to filter user input somehow
		* TODO: This should output (via echo or whatever) some valid JSON that is used
		*       to confirm the request succeeded
		*/
	public function post_xhr($qid, $class, $assignment, $student) {
		$this->basic_setup(func_get_args());
		Permissions::gate(POSITION_SECTION_LEADER, $this->role);		
		
		// only section leaders should be able to add comments
		$quarter = Quarter::current();

		if($quarter->id != $qid){
			echo json_encode(array("status" => "fail", "why" => "You cannot leave comments for earlier quarters."));
			return;
		}
		
		$course = Course::from_name_and_quarter_id($class, $qid);

		$parts = explode("_", $student); // if it was student_1 just take student
		$suid = $parts[0];
		$submission_number = $parts[1];
		
		
		$the_student = new Student;
		$the_student->from_sunetid_and_course($suid, $course);
		$the_sl = $the_student->get_section_leader();
		
		$sl = Model::getSectionLeaderForStudent($suid, $class);
		
		$dirname = $the_sl->get_base_directory() . '/' . $assignment . '/' . $student .'/';

		if(!isset($_POST['action'])) {
			echo json_encode(array("status" => "fail"));
			return;
		}
		if($_POST['action'] == "release"){
			// echo $dirname;
			if($_POST['release'] == "create"){
				Utilities::create_release($dirname);
			}else{
				Utilities::delete_release($dirname);
			}
			echo json_encode(array("status" => "ok"));
			return;
		}
				
		$curFile = AssignmentFile::loadFile($quarter->id, $class, $suid, $assignment, $_POST['filename'], $submission_number);
				
		$id = $curFile->getID();
		if(!isset($id)){ //			echo "no valid assnment found";
			echo json_encode(array("status" => "fail"));
			return;
		}
		$commenter = Model::getUserID(USERNAME);
		$student = Model::getUserID($suid);

		if($_POST['action'] == "create") {
			$newComment = AssignmentComment::create($curFile->getID(), $_POST['rangeLower'], 
				$_POST['rangeHigher'], $_POST['text'], $commenter, $student);
			$newComment->save();
		} else if($_POST['action'] == "delete") {
			// find the comment to delete: 
			foreach($curFile->getAssignmentComments() as $comment) {
					if(	$comment->getStartLine() == $_POST['rangeLower'] && 
						$comment->getEndLine() == $_POST['rangeHigher'] && 
					$comment->getCommentText() == $_POST['text'] ) {
						$comment->delete();
						break;
					}
					// TODO: Probably a better way to delete comments would be to associate with a unique id
					// if(	$comment->getID() == $_POST['commentID'] ) {
						// 	$comment->delete();
						// 	break;
						// }
			}
		} 
	
		echo json_encode(array("status" => "ok", "action" => $_POST['action']));
	}
}
		

?>