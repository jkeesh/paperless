<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
			$role = Model::getRoleForClass(USERNAME, $class);
    	if($role <= POSITION_STUDENT) {
    		  Header("Location: " .  ROOT_URL ."$class/student/" . USERNAME);
				  return;
    	}
			
			if($role == POSITION_SECTION_LEADER) {
			  Header("Location: " .  ROOT_URL ."$class/sectionleader/" . USERNAME);
			  return;
			} else {
				echo "what is your position?";
			}
		}
		
	}
	?>