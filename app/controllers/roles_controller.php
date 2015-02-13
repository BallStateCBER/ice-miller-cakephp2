<?php
class RolesController extends AppController {
    var $name = 'Roles';
    var $scaffold;
    
	function beforeFilter() {
		$this->Auth->allow('');
		$this->Auth->deny('*');
	}
	
	function index() {
		$this->set(array(
			'title_for_layout' => 'Roles / Permissions'
		));
        
		$this->loadModel('User');
		
		$this->__setBreadcrumbs(array(
			'/roles' => 'Roles and Permissions' 
		));
		
		$this->set(array(
			'roles' => $this->Role->find('all', array(
				'fields' => array('id', 'name'),
				'order' => array('name ASC'),
				'contain' => array(
					'Permission' => array(
						'fields' => array('id')
					)
				)
			)),
			'permissions' => $this->Role->Permission->find('all', array(
				'fields' => array('id', 'name', 'description'),
				'order' => array('description ASC'),
				'contain' => false
			)),
			'users' => $this->User->find('all', array(
				'fields' => array('id', 'name'),
				'order' => array('name ASC'),
				'contain' => false
			))
		));
	}
	
	function add() {
		if (! empty($this->data)) {
			$this->Role->set($this->data);
			if ($this->Role->validates()) {
				if ($this->Role->save($this->data, true)) {
					$this->flash('Role added.', 'success');
				}
			}
		}
		$this->redirect('/roles');
	}
	
	function delete($id = null) {
		if (! $id) {
			$this->flash('No ID specified. Which role do you want to delete?', 'error');
			$this->redirect('/roles');
		}
		$count = $this->Role->find('count', array(
			'conditions' => array('Role.id' => $id)
		));
		if ($count == 0) {
			$this->flash('That role doesn\'t exist.', 'error');
			$this->redirect('/roles');
		}
		if ($this->Role->delete($id)) {
			$this->flash('Role deleted.', 'success');
		} else {
			$this->flash('There was an error removing this role.', 'error');
		}
		$this->redirect('/roles');
	}
		
	function edit($id) {
		$this->Role->id = $id;
		if (! empty($this->data)) {
			if ($this->Role->save($this->data)) {
				$this->flash('Role permissions updated.', 'success');
				$this->redirect('/roles');
			} else {
				$this->flash('There was an error updating this role.', 'error');
			}
		}
		$this->data = $role = $this->Role->find('first', array(
			'fields' => array('id', 'name'),
			'order' => array('name ASC'),
			'conditions' => array('Role.id' => $id),
			'contain' => array(
				'Permission' => array(
					'fields' => array('id'),
					'order' => array('name ASC')
				)
			)
		));
		if ($this->data === false) {
			$this->flash('That role (id: '.$id.') doesn\'t exist.', 'error');
			$this->redirect('/roles');
		}
		
		$this->__setBreadcrumbs(array(
			'/roles' => 'Roles and Permissions',
			"/roles/edit/$id" => $role['Role']['name']
		));
		
		$this->set(array(
			'title_for_layout' => 'Role: '.$role['Role']['name'],
			'role' => $role,
			'permissions' => $this->Role->Permission->find(
				'list', 
				array('fields' => array('id','name'))
			)
		));
	}
	
	function user($user_id = null) {
		if (isset($this->data['Role']['user_id'])) {
			$this->redirect('/roles/user/'.$this->data['Role']['user_id']);
			//$user_id = $this->data['Role']['user_id'];
		}

		// Collect all roles
		$roles = $this->Role->getList();
		
		// Load user
		$this->loadModel('User');
		$this->User->id = $user_id;
		
		// Collect the user's roles
		$user_roles = $this->User->getRoles();
		
		// Get user's name
		$user_name = $this->User->field('name');
		
		// Redirect if user id is invalid
		if (! $user_name) {
			$this->flash('Invalid user selected.', 'error');
			$this->redirect('/roles');	
		}
				
		// Process role removals / additions
		if (isset($this->data['Role']['remove_these_roles']) || isset($this->data['Role']['add_this_role'])) {
			// Process role removal
			if (isset($this->data['Role']['remove_these_roles']) && ! empty($this->data['Role']['remove_these_roles'])) {
				if ($this->User->habtmDelete('Role', $user_id, $this->data['Role']['remove_these_roles'])) { 
					$this->flash('Role'.((count($this->data['Role']['remove_these_roles']) > 1) ? 's' : '').' removed. User may have to log out and log back in for change to take effect.', 'success');
				} else {
					$this->flash('Error removing role'.((count($this->data['Role']['remove_these_roles']) > 1) ? 's' : '').'.', 'error');
				}
			}
			
			// Process row addition
			if (isset($this->data['Role']['add_this_role']) && ! empty($this->data['Role']['add_this_role'])) {
				if ($this->User->habtmAdd('Role', $user_id, $this->data['Role']['add_this_role'])) {
					$this->flash('Role added. User may have to log out and log back in for change to take effect.', 'success');
				} else {
					$this->flash('There was a problem adding that role.', 'error');
				}
			}
			
			// Update user roles variable
			$user_roles = $this->User->getRoles();
		}
		
		$labeled_user_roles = array();
		foreach ($user_roles as $role_id) {
			$labeled_user_roles[$role_id] = $roles[$role_id];
		}
		asort($labeled_user_roles);
		
		$this->__setBreadcrumbs(array(
			'/roles' => 'Roles and Permissions',
			"/roles/user/$user_id" => $user_name 
		));
		
		$this->set(array(
			'user_id' => $user_id,
			'user_name' => $user_name,
			'user_roles' => $labeled_user_roles,
			'roles' => $roles,
			'roles_not_assigned' => array_diff_assoc($roles, $labeled_user_roles),
			'title_for_layout' => 'Roles: '.$user_name 
		));
	}
}
?>