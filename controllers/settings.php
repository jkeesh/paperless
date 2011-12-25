<?php

require_once(dirname(dirname(__FILE__)) . "/models/Settings.php");


	class SettingsHandler extends ToroHandler {				
		public function post(){

		}
		
		public function get() {
			$this->basic_setup(func_get_args());
			
			$settings = Settings::get_for_user($this->user);
			print_r($settings);
			
			$this->smarty->display("settings.html");			
		}
	}

?>