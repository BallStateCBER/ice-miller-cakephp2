<?php
class TagsController extends AppController {
	var $name = 'Tags';
	var $helpers = array('Tree');
	
    function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('*'));
		$this->require_captcha = false;
	}
		
	function index() {
		list($tag_list, $tag_cloud) = $this->Tag->getListAndCloud('articles_tags');
		list($count_min, $count_max) = $this->Tag->getCountRange($tag_cloud);
		$this->__setBreadcrumbs(array(
			'/tags' => 'Tags'
		));
		$title_for_layout = 'Tags';
		$this->set(compact(
			'tag_cloud', 
			'tag_list', 
			'count_min',
			'count_max',
			'title_for_layout'
		));
	}
	
	function cloud() {
		$model = isset($this->params['named']['model']) ? $this->params['named']['model'] : null;
		$tag_limit = isset($this->params['named']['tag_limit']) ? $this->params['named']['tag_limit'] : null;
		return $this->Tag->getCloud($model, $tag_limit);
	}
	
    function autocomplete() {
		$limit = 8;
		$string_to_complete = Sanitize::escape($_POST['string_to_complete']);
		$like_conditions = array(
			$string_to_complete.'%',
			'% '.$string_to_complete.'%',
			'%'.$string_to_complete.'%'
		);
		$select_statements = array();
		foreach ($like_conditions as $like) {
			$select_statements[] = 
				"SELECT `Tag`.`name`
				FROM `tags` AS `Tag`
				WHERE `Tag`.`selectable` = 1
				AND `Tag`.`name` LIKE '$like'";
		}
		$query = implode("\nUNION\n", $select_statements)."\nLIMIT $limit";
		$Tag =& ClassRegistry::init('Tag');
		$tags = $Tag->query($query);
		if (! empty($tags)) {
			$revised_tags = array();
			foreach ($tags as $tag) {
				$tag = $this->str_replace_once(
					$string_to_complete, 
					'<strong>'.$string_to_complete.'</strong>', 
					$tag[0]['name']
				);
				$revised_tags[] = $tag;
			}
			$tags = $revised_tags;
		}
		$this->set('tags', $tags);
		$this->layout = 'ajax';
	}
}
?>