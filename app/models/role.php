<?php
class Role extends AppModel {
	var $name = 'Role';
	var $useTable = 'roles';
	var $actAs = array('Containable', 'ExtendAssociations');
	var $hasAndBelongsToMany = array(
		'Permission' => array('className' => 'Permission',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'role_id',
			'associationForeignKey' => 'permission_id',
			'unique' => true
		),
		'User' => array('className' => 'User',
			'joinTable' => 'roles_users',
			'foreignKey' => 'role_id',
			'associationForeignKey' => 'user_id',
			'unique' => true
		)
	);
	
	function getList() {
		$this->recursive = -1;
		$results = $this->find('all', array(
			'fields' => array('Role.id', 'Role.name'),
			'order' => array('Role.name ASC')
		));
		$roles = array();
		foreach ($results as $role) {
			$roles[$role['Role']['id']] = $role['Role']['name'];	
		}
		return $roles;	
	}
}
?>