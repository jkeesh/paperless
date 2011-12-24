<?php

	class SettingsHandler extends ToroHandler {		
		public function post(){

		}
		
		public function get() {
			$this->basic_setup(func_get_args());
			$this->smarty->assign("message", "Hi");	
			
			$this->smarty->display("message.html");			
		}
	}

?>