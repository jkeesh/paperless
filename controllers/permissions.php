<?php
	/* TODO fill with permissions methods 
	 
	 idea from eric:
	 
	 We could do a role based access control I think where the roles are:
	 student - 0
	 sl - 1
	 coordinator - 2
	 demo -- i guess we let you select a role?
	 
	 from jeremy: consider - we may need multiple role numbers because they are in multiple classes.
	 
	 and then we have a method
	 Role getRole(Student, Class)
	 and another method
	 void requireRole(Role)
	 that forwards the user to some neutral page / denies access if they don't have role >= Role for the specified class.
	 
	 
	 alternative idea 
	 
	 hasPermissionsForSection(class, sl)
	 hasPermissionForClass(class)
	 hasPermission(class,student)
	 
	 
	 */
	 
	 class Permissions {
	   public static function requireRole($role, $class) {
	     $curRole = Model::getRoleForClass(USERNAME, $class);
	     
	     if($curRole < $role) {
	       Header("Location: " . ROOT_URL);
	     }
	   }
	 }
	
	

?>
