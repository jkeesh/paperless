<?php
	 
	 class Permissions {
	   public static function requireRole($role, $class) {
	     $curRole = Model::getRoleForClass(USERNAME, $class);
	     
	     if($curRole < $role) {
	       Header("Location: " . ROOT_URL);
	     }
		 return $curRole;
	   }
	 }
	
	

?>
