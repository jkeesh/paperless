<?php

	/**
	 * Lets the Class admins modify class configurations
	 */
	class ManageHandler extends ToroHandler {
		
		public function post($class){
			print_r($_POST);
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('manage.html');	
		}
		
		public function get($class) {
						
			$role = Permissions::requireRole(POSITION_TEACHING_ASSISTANT, $class);
			$this->smarty->assign("class", $class);
			$this->smarty->assign("admin_class", $class);
			// display the template
			$this->smarty->display('manage.html');
		}
	}
	?>