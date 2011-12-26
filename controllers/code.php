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

		if($this->role >= POSITION_SECTION_LEADER){
			$this->smarty->assign("interactive", 1);
			$showComments = True;
		}
		if($this->role == POSITION_STUDENT){
			$showComments = $release;
		}

		$this->smarty->assign("assignment", htmlentities($assignment));
		$this->smarty->assign("showComments", $showComments);
		$this->smarty->display("code.html");
	}
	
	
	private function json_failure($error){
		echo json_encode(array("status" => "fail", "why" => $error));
	}
	
	private function json_success(){
		echo json_encode(array("status" => "ok"));
	}
	
	private function handle_release($release_action, $dirname){
		if($release_action == "create"){
			Utilities::create_release($dirname);
		}else{
			Utilities::delete_release($dirname);
		}
		$this->json_success();
		return;
	}

	/*
	 * Handles adding and deleting comments.  Note: when a comment is edited it is
	 * first deleted and then re-added to the database.
	 */
	public function post_xhr($qid, $class, $assignment, $student) {
		$this->basic_setup(func_get_args());
		
		// Only section leaders should be able to add comments
		Permissions::gate(POSITION_SECTION_LEADER, $this->role);		
		
		// We only modifications for the current quarter.
		$quarter = $this->current_quarter;
		if($this->current_quarter->id != $qid){
			return $this->json_failure("You cannot leave comments for earlier quarters.");
		}		
		
		
		$parts = explode("_", $student); // if it was student_1 just take student
		$suid = $parts[0];
		$submission_number = $parts[1];
		
		$the_student = new Student;
		$the_student->from_sunetid_and_course($suid, $this->course);
		$the_sl = $the_student->get_section_leader();
		
		$sl = Model::getSectionLeaderForStudent($suid, $class);
		
		$dirname = $the_sl->get_base_directory() . '/' . $assignment . '/' . $student .'/';

		// Return a failure message if variables are not properly set.
		if(!isset($_POST['action'])) {
			return $this->json_failure("The message to the server was not properly formed.");
		}
		// Handle the release of an assignment if that is what the action calls for.
		if($_POST['action'] == "release"){
			return $this->handle_release($_POST['release'], $dirname);
		}
				
		$curFile = AssignmentFile::loadFile($quarter->id, $class, $suid, $assignment, $_POST['filename'], $submission_number);
				
		$id = $curFile->getID();
		if(!isset($id)){ 
			return $this->json_failure("We could not load the assignment.");
		}
		$commenter = Model::getUserID(USERNAME);
		$student = Model::getUserID($suid);

		$db_id = null;
		if($_POST['action'] == "create") {
			$newComment = AssignmentComment::create($curFile->getID(), $_POST['rangeLower'], 
				$_POST['rangeHigher'], $_POST['text'], $commenter, $student);
			$newComment->save();
			$db_id = $newComment->getID();
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
	
		echo json_encode(array("status" => "ok", "action" => $_POST['action'], 'db_id' => $db_id));
	}
}
		

?>