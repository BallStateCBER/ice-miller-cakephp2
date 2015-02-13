<?php
class Comment extends AppModel {
    var $name = 'Comment';
    var $displayField = 'body';
    var $actsAs = array('Containable');
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'fields' => array('User.id', 'User.name')
		),
		'Article' => array(
			'className' => 'Article',
			'foreignKey' => 'article_id',
			'fields' => array('Article.id')
		)
	);
	var $validate = array(
		'body' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 2000),
				'message' => 'Sorry, but you\'ll have to cut your comment down to less than 200 characters.',
				'allowEmpty' => false,
				'required' => true,
			)
		),
		'article_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'required' => true,
			'message' => 'Unable to post comment (article ID missing). Please <a href="/contact">contact the webmaster</a> for assistance.'
		),
		'user_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'required' => true,
			'message' => 'Unable to post comment (user ID missing). Please <a href="/login">log in</a> if you haven\'t already or <a href="/contact">contact the webmaster</a> for assistance.'
		),
	);
}
?>