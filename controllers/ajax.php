<?php

class AjaxHandler extends ToroHandler {
	public function post_xhr() {
		$this->basic_setup(func_get_args());
		
		/* If the action was settings update the key=>value pair */
		if($_POST['action'] == 'settings'){
			$this->settings->set_value($_POST['key'], $_POST['val']);
			$this->settings->save();
		}
		
		echo json_encode(array('status' => 'ok'));
	}
}
		

?>