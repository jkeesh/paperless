<?php

require_once(dirname(dirname(__FILE__)) . "/models/Settings.php");


	class SettingsHandler extends ToroHandler {	
		private function settings($handle_post = false){
			$this->basic_setup(func_get_args());
			$settings = Settings::get_for_user($this->user);

			if($handle_post){
				foreach($_POST as $key => $val){
					$settings->set_value($key, $val);
				}
				$settings->save();
				$settings_saved = true;
			}else{
				$settings_saved = false;
			}

			$this->smarty->assign("settings", $settings);
			$this->smarty->assign("settings_saved", $settings_saved);			
			$theme_options = Settings::get_options('theme');
			$this->smarty->assign("theme_options", $theme_options);
			$this->smarty->display("settings.html");				
		}
					
		public function post(){
			$this->settings(true);
		}
		
		public function get() {
			$this->settings();
		}
	}

?>