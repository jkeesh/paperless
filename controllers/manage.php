<?php

	require_once("models/PaperlessAssignment.php");

	/**
	 * Lets the Class admins modify class configurations
	 */
	class ManageHandler extends ToroHandler {
		
		private function manage($qid, $class, $handle_post=false){
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_TEACHING_ASSISTANT, $this->role);		
			$quarter = Quarter::current();
			
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