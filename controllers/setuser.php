<?php
	
	require_once('utils.php');
	
	class SetUser extends ToroHandler {
				
		public function get($user) {
			//define('USERNAME', $user);
			$_SESSION['USERNAME'] = $user;
			Header("Location: ".  ROOT_URL);
		}
	}
	?>