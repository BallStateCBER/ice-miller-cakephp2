<?php
class Permission extends AppModel {
	var $name = 'Permission';
	var $useTable = 'permissions';
	var $actAs = array('Containable');
	var $hasAndBelongsToMany = array(
		'Role' => array(
			'className' => 'Role',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'permission_id',
			'associationForeignKey' => 'role_id',
			'unique' => true
		)
	);
}
?>