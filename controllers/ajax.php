<?php

class AjaxHandler extends ToroHandler {
	public function post_xhr() {
		$this->basic_setup(func_get_args());
		
		/* If the action was settings update the key=>value pair */
		if(array_key_exists('action', $_POST) && $_POST['action'] == 'settings'){
			$this->settings->set_value($_POST['key'], $_POST['val']);
			$this->settings->save();
		}
		
		/* If the action was delete_file, delete the file from the submission */
		if(array_key_exists('action', $_POST) && $_POST['action'] == 'delete_file'){
			Utilities::delete_code_file($_POST['assn'], $_POST['file'], $this);
			echo json_encode(array('status' => 'ok', 'remove' => 'true'));
			return;
		}
		
		echo json_encode(array('status' => 'ok'));
	}
}
		

?>