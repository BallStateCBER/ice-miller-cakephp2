<?php
class User extends AppModel {
	var $name = 'User';
	var $displayField = 'name';
	var $actsAs = array('Containable', 'ExtendAssociations');

	var $hasMany = array(
		'Comment' => array(
			'className'     => 'Comment',
			'foreignKey'    => 'user_id',
			'conditions'    => array('Comment.approved' => '1'),
			'order'    		=> 'Comment.created DESC',
			'dependent'		=> true
        ),
        
        /* When fetching associated articles, you may have to specify 
         * in the find() that only articles with a published_date <= today should
         * be returned. Not sure how to automatically specify that. */ 
        'Article' => array(
			'className'     => 'Article',
			'foreignKey'    => 'user_id',
			'conditions'    => array('Article.is_published' => '1'),
			'order'    		=> 'Article.created DESC',
			'dependent'		=> false
        )
	);
	
	var $hasAndBelongsToMany = array(
		'Role' => array('className' => 'Role',
			'joinTable' => 'roles_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'role_id',
			'unique' => true
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter your name.',
				'last' => true
			),
			'isUnique' => array(
				'rule' => '_isUnique',
				'message' => 'Sorry, someone with that name has already registered an account. If you have the misfortune of having a common name, try using your middle initial, full middle name, or a description of your location, e.g. "John Smith from Boston".'
			)
		),
		'new_password' => array(
			'nonempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a password. Without one, anyone will be able to log in as you.'
			)
		),
		'confirm_password' => array(
			'identicalFieldValues' => array(
				'rule' => array('_identicalFieldValues', 'new_password' ),
				'message' => 'Passwords did not match.'
			)
		),
		'email' => array(
			'is_email' => array(
				'rule' => 'email',
				'message' => 'That doesn\'t appear to be a valid email address.'
			),
			'emailUnclaimed' => array(
				'rule' => array('_isUnique'),
				'message' => 'Sorry, that email address is already in use.'
			)
		)
	); 
	
	function _identicalFieldValues( $field=array(), $compare_field=null ) {
		foreach( $field as $key => $value ){
			$v1 = $value;
			$v2 = $this->data[$this->name][$compare_field];
			if($v1 !== $v2) {
				return FALSE;
			} else {
				continue;
			}
		}
		return TRUE;
    }
	   
	function _isUnique($check) {
		$value = array_pop(array_values($check));
		$field = array_pop(array_keys($check));
		if ($field == 'email') {
			$value == strtolower($value);
		}
		if(isset($this->data[$this->name]['id'])) {
			$results = $this->field('id', array(
				"$this->name.$field" => $value, 
				"$this->name.id <>" => $this->data[$this->name]['id']
			));
		} else {
			$results = $this->field('id', array(
				"$this->name.$field" => $value
			));
		}
		return empty($results);
	}
	
	// Returns an array of the IDs of all roles held by this user
	function getRoles($id = null) {
		if (! $id) {
			if ($this->id) {
				$id = $this->id;
			} else {
				return false;
			}
		}
		$user_data = $this->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array('Role.id'),
			'fields' => array('User.id')
		));
		$user_roles = array();
		foreach ($user_data['Role'] as $role) {
			$user_roles[] = $role['id'];
		}
		return $user_roles;
	}
}
?>