<?php

class Permissions {	
	/*
	 * The main function of the permissions class. Given a required role,
	 * and the actual role of the user in this class, redirect the user
	 * to the home page if they do not have the required permissions
	 *
	 * $required	int, representing required permissions
	 * $actual		int, representing the permissions this user has
	 * @author	Jeremy Keeshin	December 22, 2011
	 */
	public static function gate($required, $actual) {
		if($actual < $required){
			Header("Location: " . ROOT_URL);	
		}
	}
	
	/*
	 * Verify that $user has $role in $course. This is used as sanity
	 * check in certain situations to make sure the information is 
	 * consistent.
	 * 
	 * $role	int, representing the required role
	 * $user	string, the sunet id of the user we are checking
	 * $course	Course, the Course object that we are checking against
	 *
	 * @author	Jeremy Keeshin	December 22, 2011
	 */
	public static function verify($role, $user, $course){
		
	}
}



?>
