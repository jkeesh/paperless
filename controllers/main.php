<?php
	class IndexHandler extends ToroHandler {
		
		public function get($qid, $class) {
							
			// if($class){
			// 	$this->smarty->assign("message", "It seems you have visited an invalid url.");
			// 	$this->smarty->display("message.html");
			// 	return;
			// }
			
			$userClasses = Model::getClass(USERNAME);
			
			if(!is_array($userClasses)){
				$this->smarty->assign("message", "It seems you are not a member of any classes this quarter.");
				$this->smarty->display("message.html");
				return;
			}
			if( count($userClasses) > 1){ //they are in multiple classes, redirect to choice page
				Header("Location: " . ROOT_URL . "select");
				return;
			}
			
			$class = strtolower($userClasses[0]['Name']);

			if(strlen($class) == 0){
                $this->smarty->assign("errorMsg", "Couldn't get name for user class.");
				$this->smarty->display("error.html");
				return;
			}

			$role = Model::getRoleForClass(USERNAME, $class);
	
			
			if($role > POSITION_SECTION_LEADER){
				Header("Location: ". ROOT_URL .$qid."/".$class."/admin");
				return;
			}else if($role == POSITION_SECTION_LEADER){
				Header("Location: ". ROOT_URL .$qid."/".$class."/sectionleader/" . USERNAME);
				return;
			}else{
				Header("Location: ". ROOT_URL .$qid."/".$class."/student/".USERNAME);
				return;
			}
		}
		
	}
	?>
