<?php
	class IndexHandler extends ToroHandler {
		
		public function get($class) {
			if(!$class) {
				$class = Model::getClass(USERNAME);
			}
			
    		if(IS_STUDENT_ONLY){
    		  Header("Location: " .  ROOT_URL ."$class/student/" . USERNAME);
				  return;
    		}
			
			if(IS_SECTION_LEADER){
			  Header("Location: " .  ROOT_URL ."$class/sectionleader/" . USERNAME);
			  return;
			}else{
				echo "what is your position?";
			}
		}
	}
	?>