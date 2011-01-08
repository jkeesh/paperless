<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {			
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
			if(!is_array($class)){
				Header("Location: ".  ROOT_URL ."error/for/you");
				return;
			}
			if( count($class) > 1){ //they are in multiple classes, redirect to choice page
				Header("Location: " . ROOT_URL . "select");
				return;
			}
			
			$class = strtolower($class[0]['Name']);
			
			if(strlen($class) == 0){
				$this->smarty->display("error.html");
				return;
			}

			$role = Model::getRoleForClass(USERNAME, $class);
			
			if($role > POSITION_SECTION_LEADER){
				Header("Location: ".  ROOT_URL ."$class/admin");
				return;
			}else if($role == POSITION_SECTION_LEADER){
				Header("Location: " .  ROOT_URL ."$class/sectionleader/" . USERNAME);
				return;
			}else{
				Header("Location: " .  ROOT_URL ."$class/student/" . USERNAME);
				return;
			}
		}
		
	}
	?>