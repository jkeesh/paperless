<?php

	require_once("models/PaperlessAssignment.php");

	/**
	 * Lets the course admins modify class configurations, which right now is the 
	 * list of class assignments.
	 * 
	 * Both GET and POST requests load the same page, but on a POST request, we process
	 * the form data to either, create, update, or delete an assignment listing.
	 *
	 * We gate this page to only TAs and higher, and go through the basic setup
	 * to setup the course, and role for the current user.
	 *
	 * @author	Jeremy Keeshin	December 23, 2011
	 */
	class ManageHandler extends ToroHandler {
		
		/*
		 * This method handles most of the logic for both get and post requests.
		 * The only difference is that on a POST request we update the relevant
		 * assignment info.
		 */
		private function manage($qid, $class, $handle_post=false){
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_TEACHING_ASSISTANT, $this->role);		
			$quarter = Quarter::current();
			
			// If it is an old quarter, we do not allow modifications.
			if($quarter->id != $qid){
				$this->smarty->assign("old_quarter", true);
			}else if($handle_post){
				if($_POST['action'] == "Update"){
					PaperlessAssignment::update($_POST['id'], $class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
				}else if($_POST['action'] == "Delete"){
					PaperlessAssignment::deleteID($_POST['id']);
				}else{
					$assn = PaperlessAssignment::create($class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
					$assn->save();
				}
			}

			$assns = PaperlessAssignment::load_for_course($this->course);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->display('manage.html');
		}
		
		public function post($qid, $class){
			$this->manage($qid, $class, true);	
		}
		
		public function get($qid, $class) {
			$this->manage($qid, $class);
		}
	}
	?>