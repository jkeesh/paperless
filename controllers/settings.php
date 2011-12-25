<?php

require_once(dirname(dirname(__FILE__)) . "/models/Settings.php");


	class SettingsHandler extends ToroHandler {				
		public function post(){
			$this->basic_setup(func_get_args());
			$settings = Settings::get_for_user($this->user);

			foreach($_POST as $key => $val){
				$settings->set_value($key, $val);
			}
			$settings->save();

			$this->smarty->assign("settings", $settings);
			$this->smarty->assign("settings_saved", true);
			
			$theme_options = Settings::get_options('theme');
			$this->smarty->assign("theme_options", $theme_options);
			$this->smarty->display("settings.html");			
		}
		
		public function get() {
			$this->basic_setup(func_get_args());
			
			$settings = Settings::get_for_user($this->user);
			
			$theme_options = Settings::get_options('theme');
			
			$this->smarty->assign("settings_saved", false);
			
			$this->smarty->assign("settings", $settings);
			$this->smarty->assign("theme_options", $theme_options);
			$this->smarty->display("settings.html");			
		}
	}

?>