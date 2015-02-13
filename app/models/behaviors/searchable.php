<?php
class SearchableBehavior extends ModelBehavior {
	var $settings = array();
	var $model = null;
	
	var $_index = false;
	var $foreignKey = false;
	var $_defaults = array(
		'rebuildOnUpdate' => true
	);
	
	var $SearchIndex = null;

	function setup(&$model, $settings = array()) {
		$settings = array_merge($this->_defaults, $settings);	
		$this->settings[$model->name] = $settings;
		$this->model = &$model;
	}
	
	function _indexData() {
		if (method_exists($this->model, 'indexData')) {
			return $this->model->indexData();
		} else {
			return $this->_index();
		}
	}
	
	function beforeSave() {
		if ($this->model->id) {
			$this->foreignKey = $this->model->id;		
		} else {
			$this->foreignKey = 0;
		}
		if ($this->foreignKey == 0 || $this->settings[$this->model->name]['rebuildOnUpdate']) {
			$this->_index = $this->_indexData();
		}
		return true;
	}
	
	function afterSave() {
		if ($this->_index !== false) {
			if (!$this->SearchIndex) {
				$this->SearchIndex = ClassRegistry::init('SearchIndex');
			}
			if ($this->foreignKey == 0) {
				$this->foreignKey = $this->model->getLastInsertID();
				$this->SearchIndex->save(
					array(
						'SearchIndex' => array(
							'model' => $this->model->name,
							'association_key' => $this->foreignKey,
							'data' => $this->_index
						)
					)
				);
			} else {
				$searchEntry = $this->SearchIndex->find('first',array('fields'=>array('id'),'conditions'=>array('model'=>$this->model->name,'association_key'=>$this->foreignKey)));
				$this->SearchIndex->save(
					array(
						'SearchIndex' => array(
							'id' => empty($searchEntry) ? 0 : $searchEntry['SearchIndex']['id'],
							'model' => $this->model->name,
							'association_key' => $this->foreignKey,
							'data' => $this->_index
						)
					)
				);				
			}
			$this->_index = false;
			$this->foreignKey = false;
		}
		return true;
	}
	
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
		$index = join('. ',$index);
		$index = iconv('UTF-8', 'ASCII//TRANSLIT', $index);
		$index = preg_replace('/[\ ]+/',' ',$index);
		return $index;
	}

	function afterDelete(&$model) {
		if (!$this->SearchIndex) {
			$this->SearchIndex = ClassRegistry::init('SearchIndex');
		}
		$conditions = array('model'=>$model->alias, 'association_key'=>$model->id);
		$this->SearchIndex->deleteAll($conditions);
	}
	
	function search(&$model, $q, $findOptions = array()) {
		if (!$this->SearchIndex) {
			$this->SearchIndex = ClassRegistry::init('SearchIndex');
		}
		$this->SearchIndex->searchModels($model->name);
		if (!isset($findOptions['conditions'])) {
			$findOptions['conditions'] = array();
		}
		App::import('Core', 'Sanitize');
		$q = Sanitize::escape($q);
		$findOptions['conditions'] = array_merge(
			$findOptions['conditions'], array("MATCH(SearchIndex.data) AGAINST('$q' IN BOOLEAN MODE)")
		);
		
		$retval = $this->SearchIndex->find('all', $findOptions);
		//echo '<pre>'.print_r($this->SearchIndex->Article, true).'</pre>';
		return $retval;		
	}

	// Hack found at http://code.google.com/p/searchable-behaviour-for-cakephp/issues/detail?id=1
	function reindexAll() {
		if (! $this->SearchIndex) {
			$this->SearchIndex = ClassRegistry::init('SearchIndex');
		}
		ini_set('max_execution_time', 3600); // increase execution time
		App::import('Model', $this->model->name);
		$newmodel = new $this->model->name();
		$data = $newmodel->find('all');
		foreach ($data as $i=>$row) {
			$this->model->data[$this->model->name] = $row[$this->model->name];
			$index = $this->_index();
			if ($index) {
				$searchEntry = $this->SearchIndex->find('first',array('fields'=>array('id'),'conditions'=>array('model'=>$this->model->name,'association_key'=>$row[$this->model->name]['id'])));
				$this->SearchIndex->save(array(
					'SearchIndex' => array(
						'model' => $this->model->name,
						'id' => empty($searchEntry) ? 0 : $searchEntry['SearchIndex']['id'],
						'association_key' => $row[$this->model->name]['id'],
						'data' => $index,
					)
				));
			} 
		}               
	}
}
?>