<?php
    /*
 	 * This class represents a router to allow users to see all of the classes they 
 	 * have been in in the 198 program and choose which class they would like to see.
	 * They get to view what class they were in, when they were in this class,
	 * and what role they had (student, SL, etc.) in that quarter.
	 * 
	 * @author	Jeremy Keeshin	December 20, 2011
	 */
	class RouterHandler extends ToroHandler {	
		
		/*
		 * We respond only to the GET method here, construct a user from their
		 * sunetid, and get all of their relationships. A relationship contains
		 * the information for a user's involvement in a course during a certain
		 * quarter.
		 *
		 * @author	Jeremy Keeshin December 20, 2011
		 */
		public function get() {
			$user = new User;
			$user->from_sunetid(USERNAME);
			$relationships = $user->get_all_relationships();
			$this->smarty->assign("relationships", $relationships);	
			$this->smarty->display("router.html");			
		}		
	}
?>
