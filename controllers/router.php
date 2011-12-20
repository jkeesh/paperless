<?php
	class RouterHandler extends ToroHandler {	
		public function get() {
			$user = new User(USERNAME);
			$relationships = $user->get_all_relationships();
			$this->smarty->assign("relationships", $relationships);	
			
			$this->smarty->display("router.html");			
		}		
	}
	?>
