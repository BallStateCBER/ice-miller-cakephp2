<?php
class Article extends AppModel {
	var $name = 'Article';
    var $displayField = 'title';
    var $actsAs = array(
    	'Containable', 
    	'Searchable',
    
    	// Documentation: http://planetcakephp.org/aggregator/items/5639-sluggable-behavior
    	'Sluggable' => array(
			'fields' => 'title',
			'scope' => false,
			'conditions' => false,
			'slugfield' => 'slug',
			'separator' => '-',
			'overwrite' => true,
			'length' => 200,
			'lower' => true
		)
	);
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'fields' => array('User.id', 'User.name')
		)
	);
	var $hasAndBelongsToMany = array(
		'Tag' => array(
			'className'              => 'Tag',
			'joinTable'              => 'articles_tags',
			'foreignKey'             => 'article_id',
			'associationForeignKey'  => 'tag_id',
			'order'                  => 'Tag.name ASC',
			'fields'				 => array('Tag.id', 'Tag.name')
		)
	);
	var $hasMany = array(
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'article_id',
			'conditions' => array('Comment.approved' => 1),
			'order' => 'Comment.created DESC',
			'dependent' => true
		)
	);
	var $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please provide a title for this article.'
			),
			'maxLength' => array(
				'rule' => array('maxLength', 200),
				'message' => 'Sorry, you\'ll have to cut the title down to less than 200 characters.'
			)
		),
		'body' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Hey, this isn\'t supposed to be blank.'
			)
		)
	);

	// Sets is_published to 1 and sets published_date
	function publish() {
		if (! $this->id) {
			return false;
		}
		/* This method formerly updated the published_date, but that was removed in 
		 * favor of giving manual control of the published_date to the author
		 * $this->saveField('published_date', date('Y-m-d', time()).' 00:00:00') */
		return $this->saveField('is_published', 1);
	}
	
	// Sets is_published to 0
	function unpublish() {
		if (! $this->id) {
			return false;
		}
		return $this->saveField('is_published', 0);
	}
	
	// Custom indexing function for Searchable behavior
	function _index() {
		$index = array();
		$data = $this->model->data[$this->model->name];
		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$columns = $this->model->getColumnTypes();
				if ($key != $this->model->primaryKey && isset($columns[$key]) && in_array($columns[$key],array('text','varchar','char','string'))) {
					$index []= strip_tags(html_entity_decode($value,ENT_COMPAT,'UTF-8'));
				}
			}
		}
		
		// This is the custom part
		$tag_results = $this->find('all', array(
			'conditions' => array('Article.id' => $this->id),
			'fields' => array('Article.id'),
			'contain' => array('Tag')
		));
		echo '<pre>'.print_r($tag_results, true).'</pre>';
		
		$index = join('. ',$index);
		$index = iconv('UTF-8', 'ASCII//TRANSLIT', $index);
		$index = preg_replace('/[\ ]+/',' ',$index);
		return $index;
	}
	
	/* Takes a search term and returns Articles sorted with the following descending priorities:
	 * 		Articles with search terms in title
	 * 		Articles with matching tags
	 * 		Articles with search terms in body
	 * 		Articles published more recently */
	//function search($query) {}
	
	/*
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		
		// Hack to make sure that articles are paginated correctly on 
		//public (approved == 1) and admin (approved == 0) pages
		global $Dispatcher;
		if ($Dispatcher->params['action'] == 'moderate') {
			$approved = 0;
		} else {
			$approved = 1;
		}
		$sql = 'SELECT DISTINCT(id) FROM articles WHERE approved = '.$approved;
		$this->recursive = $recursive;
		$results = $this->query($sql);
		return count($results);
	}
	*/
}
?>