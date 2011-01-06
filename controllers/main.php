<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
			if( count($class) > 1){ //they are in multiple classes, redirect to choice page
				Header("Location: " . ROOT_URL . "select");
				return;
			}
			
			$class = strtolower($class[0]['Name']);
			$role = Model::getRoleForClass(USERNAME, $class);
			
			if($role <= POSITION_STUDENT) {
				Header("Location: " .  ROOT_URL ."$class/student/" . USERNAME);
				return;
			}else if($role <= POSITION_SECTION_LEADER) {
				Header("Location: " .  ROOT_URL ."$class/sectionleader/" . USERNAME);
				return;
			}else{
				Header("Location: ".  ROOT_URL ."$class/admin");
				return;
			}
		}
		
	}
	?>