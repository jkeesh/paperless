<?php
	
	require_once('utils.php');
	
	class SelectHandler extends ToroHandler {
				
		public function get() {
			$classes = Model::getClass(USERNAME);
			
			for($i = 0; $i < count($classes); $i++){
				$classinfo = $classes[$i];
				$classname = strtolower($classinfo['Name']);
				$role = Model::getRoleForClass(USERNAME, $classname);				
				if($role <= POSITION_STUDENT) {
					$link = ROOT_URL ."$classname/student/" . USERNAME;
					$role = "Student";
				}else if($role <= POSITION_SECTION_LEADER){
					$link = ROOT_URL ."$classname/sectionleader/" . USERNAME;
					$role = "Section Leader";
				}else{
					$link = ROOT_URL ."$classname/admin";
					$role = "Administrator";
				}
				$classes[$i]['url'] = $link;
				$classes[$i]['role'] = $role;
			}
			
			$this->smarty->assign("multiple_classes", 0); // dont show the switch link on this page.
			$this->smarty->assign("classes", $classes);
			// display the template
			$this->smarty->display('select.html');
		}
	}
	?>