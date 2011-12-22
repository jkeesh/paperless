<?php

	require_once("models/PaperlessAssignment.php");

	/**
	 * Lets the Class admins modify class configurations
	 */
	class ManageHandler extends ToroHandler {
		
		public function post($qid, $class){
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_TEACHING_ASSISTANT, $this->role);		
			
			$quarter = Quarter::current();
			
			if($quarter->id == $qid){
				if($_POST['action'] == "Update"){
					PaperlessAssignment::update($_POST['id'], $class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
				}else if($_POST['action'] == "Delete"){
					PaperlessAssignment::deleteID($_POST['id']);
				}else{
					$assn = PaperlessAssignment::create($class, $_POST['directory'], $_POST['name'], $_POST['duedate']);
					$assn->save();
				}
			}else{
				$this->smarty->assign("old_quarter", true);
			}
			
			$assns = PaperlessAssignment::loadForClass($class);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->display('manage.html');	
		}
		
		public function get($qid, $class) {
			$this->basic_setup(func_get_args());
			Permissions::gate(POSITION_TEACHING_ASSISTANT, $this->role);		

			$quarter = Quarter::current();
			
			if($quarter->id != $qid){
				$this->smarty->assign("old_quarter", true);
			}

			$assns = PaperlessAssignment::loadForClass($class);
			$this->smarty->assign("assignments", $assns);
			$this->smarty->assign("class", $class);
			$this->smarty->display('manage.html');
		}
	}
	?>