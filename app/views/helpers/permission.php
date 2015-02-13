<?php
class PermissionHelper extends Helper {	
	var $helpers = array('Session');
	
	// Should be (nearly) identical to AppController::__permitted()
	// Adapted from http://www.studiocanaria.com/articles/cakephp_auth_component_users_groups_permissions_revisited
	function permitted($controllerName, $actionName){
		// Note that there is an identical function in /app/app_controller.php
		$debug_output = $dont_read_from_session = Configure::read('permissions_debug_mode');
		
		//Ensure checks are all made lower case
		$controllerName = low($controllerName);
		$actionName = low($actionName);
		//If permissions have not been cached to session...
		if ($dont_read_from_session || ! $this->Session->check('Permissions')){
			$permissions = $this->getPermissions();
			//write the permissions array to session
			//Or we WOULD be doing that if it were possible from within a helper
			//$this->Session->write('Permissions', $permissions);
		} else {
			//...they have been cached already, so retrieve them
			$permissions = $this->Session->read('Permissions');
		}
		//Now iterate through permissions for a positive match
		foreach ($permissions as $permission){
			if ($permission == '*'){
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName (*)", 'success');
				return true;//Super Admin Bypass Found
			}
			if ($permission == $controllerName.':*'){
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName ($controllerName:*)", 'success');
				return true;//Controller Wide Bypass Found
			}
			if($permission == $controllerName.':'.$actionName){
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName", 'success');
				return true;//Specific permission found
			}
		}
		if ($debug_output) $this->flash("User DOES NOT have permission for $controllerName:$actionName", 'error');
		return false;
	}
	
	function flash($message, $class = 'notification') {
		echo "<p class=\"{$class}_message\">$message</p>";
	}
	
	function getPermissions() {
		//...then build permissions array and cache it
		$permissions = array();
		//everyone gets permission to log in and out
		$permissions[]='users:logout';
		$permissions[]='users:login';
		//Import the User Model so we can build up the permission cache
		App::import('Model', 'User');
		$thisUser = new User;
		//Now bring in the current users full record along with groups
		$thisRoles = $thisUser->find(array('User.id' => $this->Session->read('Auth.User.id')));
		if ($thisRoles) {
			$thisRoles = $thisRoles['Role'];
			foreach($thisRoles as $thisRole) {
				$thisPermissions = $thisUser->Role->find(array('Role.id' => $thisRole['id']));
				$thisPermissions = $thisPermissions['Permission'];
				foreach($thisPermissions as $thisPermission) {
					$permissions[] = $thisPermission['name'];
				}
			}
		}
		return $permissions;
	}
}
?>