<?php
	
	require_once('utils.php');
	
	/*
	 * For debugging purposes, enable testing of different user's perspectives.
	 * This is only setup for testing, and cannot be done by a random user
	 * on the live site.
	 */
	class SetUser extends ToroHandler {
				
		public function get($user) {
			$_SESSION['USERNAME'] = $user;
			Header("Location: ".  ROOT_URL);
		}
	}
	?>